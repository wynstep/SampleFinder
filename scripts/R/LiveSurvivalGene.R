### HISTORY ###########################################################
# Version		Date			Coder			Comments
# 1.0           07/02/2012     	Emanuela        init
# 1.1           Dec/2013      	Ros             for BCCTB data portal
# 1.2			18/06/2015		Emanuela		tidy script, increase annotation, remove ds-specific & redundancies
# 1.3			xx/08/2015		Emanuela		adapt to avoid calling FC library and improve KM plots
# 1.4       	21/06/2017   	Stefano         adapt to be included in the new BoB portal
# 1.5			22/06/2017		Stefano			take into account multiple exp files
# 1.6			10/07/2017		Emanuela		alterations for multiple files, addition multivariate
# 1.7			17/07/2017		Ema/Stefano		finalise multivariate and add output table
# 1.8           03/08/2017      Ema/Stefano     expand script for truncated survival times and subtype-specific analyses

################################################################################

# silent warnings
options(warn=-1)

##### Clear workspace
rm(list=ls())
##### Close any open graphics devices
graphics.off()

#===============================================================================
#    Functions
#===============================================================================

# Dichotomise expression based on median
local.dichotomise.dataset <- function(x, split_at = 99999) {
  if (split_at == 99999) { split_at = median(x, na.rm = TRUE); }
  return( as.numeric( x > split_at ) );
}

#===============================================================================
#    Load libraries
#===============================================================================
suppressMessages(library(survival))
suppressMessages(library(optparse))
suppressMessages(library(data.table))
suppressMessages(library(plyr))

#===============================================================================
#    Catching the arguments
#===============================================================================
option_list = list(
  make_option(c("-e", "--exp_file"), action="store", default=NA, type='character',
              help="File containing experimental data"),
  make_option(c("-t", "--target"), action="store", default=NA, type='character',
              help="Clinical data saved in tab-delimited format"),
  make_option(c("-p", "--genes"), action="store", default=NA, type='character',
              help="ID of genes/probe of interest"),
  make_option(c("-d", "--dir"), action="store", default=NA, type='character',
              help="Default directory"),
  make_option(c("-x", "--hexcode"), action="store", default=NA, type='character',
              help="unique_id to save temporary plots"),
  make_option(c("-y", "--type"), action="store", default=NA, type='character',
              help="type of analysis (dr, tcga, ccle)")
)

opt = parse_args(OptionParser(option_list=option_list))

expFile <- opt$exp_file
annFile <- opt$target
gene_list <- opt$genes
outFolder <- opt$dir
hexcode <- opt$hexcode
type_an <- opt$type

#===============================================================================
#    Main
#===============================================================================
# isolate genes of interest, split by comma
genes = unlist(strsplit(gene_list, ","));

# molecular subtypes
pam50.subtypes <- c("LumA","LumB","Basal","Her2","Normal");

# EXTRACT CLINICAL, EXPRESSION & SURVIVAL COVARIATES
# read sample annotation file
annData <- read.table(annFile,sep="\t",as.is=TRUE,header=TRUE);

expData <- as.data.frame(fread(expFile,sep="\t",header=TRUE, stringsAsFactors = FALSE))
### assigning gene ids to rownames
rownames(expData) <- expData[,1]
expData <- expData[,-1]

# subset to samples/patients for which full survival data are available
annData.slimmed <- annData[ complete.cases(annData$SURVIVALTIME) , ];

if (type_an == "dr_survival") {
  rnaseq_samples = annData.slimmed[annData.slimmed$TECHNOLOGY=="rnaseq","FILE_NAME"]
  selected_samples <- intersect(rnaseq_samples,colnames(expData))
  annData.slimmed <- annData.slimmed[which(annData.slimmed$FILE_NAME %in% selected_samples),]
}

# filter the expression data
selected_samples <- intersect(as.character(annData.slimmed$FILE_NAME),colnames(expData));
# subset the annData to take into account instances in which more than one expData file is present
annData.slimmed <-  annData.slimmed[ annData.slimmed$FILE_NAME %in% selected_samples , ]

# assign riskgroups
sub_samples = array();
for( subtype in pam50.subtypes ) {
  # identify samples in each subgroup
  sub_samples <- c(sub_samples, annData.slimmed[ annData.slimmed$MOLECULAR_SUBTYPE == subtype , "FILE_NAME" ]);
}

# collecting samples
annData.sub <-  annData.slimmed[ annData.slimmed$FILE_NAME %in% sub_samples , ];
# subset expression data to selected samples
expData.sub <- expData[ , sub_samples[complete.cases(sub_samples)] ];

surv.stat <- annData.sub[ , "SURVIVALSTATUS"];
surv.stat.binary <- as.numeric(mapvalues(surv.stat, from = c("Alive", "Dead"), to = c(0,1)))

# extract surv time
surv.time <- as.numeric( annData.sub[ , "SURVIVALTIME"]);
# univariate model run 5 years, 10 years and maximum survival time
surv.trunc <- c(5, 10, max(surv.time));

## initializing hr and p-value arrays
all_hrs = array();
all_p = array();

all_labels = character();
for( trunc in surv.trunc ){
  p_label = paste0("Log-rank P ",trunc," years");
  hr_label = paste0("HR ",trunc," years");
  all_labels <- c(all_labels, p_label, hr_label)
}

gene.covar <- matrix(data = 0,
                      nrow=length(genes)+1,
                      ncol = length(surv.trunc)*2,
                      dimnames=list(c("Multivariate",unlist(genes)),unlist(all_labels))
                    );

#setting the working directory
setwd(outFolder);

for( trunc in surv.trunc ){
  # truncate survival in the annotation data - assign all patients as alive from truncating point
  all.rgs = NULL
  rg <- list();
  tmp.surv.stat <- surv.stat.binary
  tmp.surv.stat[surv.time > trunc] <- 0
  tmp.surv.time <- surv.time
  tmp.surv.time[surv.time > trunc] <- trunc;


  # FOR EACH GENE SUPPLIED BY USER CALCULATE RISK GROUPS AND APPLY UNIVARIATE MODELLING
  # calculate riskgroups (rg) for each gene
  cont = 2; ## starting from 2 because the row 1 is occupied by multivariate
  for( gene.i in genes ) {

		rg[[gene.i]] <- local.dichotomise.dataset( as.matrix(expData.sub[ gene.i , ] ));

		# apply surv.model
		cox.fit <- summary( coxph( Surv(tmp.surv.time , tmp.surv.stat) ~ rg[[gene.i]]) );

    # kaplan meier input
    cox.km <- survfit(coxph(Surv(tmp.surv.time, tmp.surv.stat) ~ strata(rg[[gene.i]])));

    # PLOTTING PARAMETERS
    # calculating p-value and hr
    pVal <- round(cox.fit$sctest[3], digits = 3);
    hr <- round(cox.fit$conf.int[1,1], digits = 2);

    ### run univariate analysis
    all.rgs = cbind(all.rgs,rg[[gene.i]]);
    p_label = paste0("Log-rank P ",trunc," years");
    hr_label = paste0("HR ",trunc," years");
    gene.covar[cont,p_label] <- pVal;
    gene.covar[cont,hr_label] <- hr;
    cont = cont + 1;

    ##### ---- PRODUCING PLOT ---- #######
    # prepare risk table
    times <- seq(0, max(cox.km$time), by = max(cox.km$time)/6);
    risk.data <- data.frame(
        strata = summary(cox.km, times = times, extend = TRUE)$strata,
        time = summary(cox.km, times = times, extend = TRUE)$time,
        n.risk = summary(cox.km, times = times, extend = TRUE)$n.risk
    );

    risk.dataLow <- t(risk.data[1:(nrow(risk.data)/2), ]);
    risk.dataHigh <- t(risk.data[(nrow(risk.data)/2+1):nrow(risk.data), ]);

    # PLOTTING PARAMETERS
    # specify how p-values are to be presented
    if ( cox.fit$sctest[3] < 0.001 ) {
      pValue <- "Log-rank P < 0.001";
    } else {
      pValue <- paste("Log-rank P = ", round(cox.fit$sctest[3], digits = 3), sep="");
    }

    # GENERATE KM PLOT FOR EACH GENE
    png(filename=paste0(hexcode,".live.KMplot.", gene.i, ".png"), width = 680, height = 680, units = "px", pointsize = 18);

    plot(
        cox.km,
        mark.time=TRUE,
        col=c("darkblue", "red"),
        xlab="Time",
        ylab="Survival probability",
        main=paste("Gene: ", gene.i, sep=""),
        cex.main=1,
        lty=c(1,1),
        lwd=2.5,
        ylim=c(0,1.15),
        xlim=c(0-((max(cox.km$time)-min(cox.km$time))/10),max(cox.km$time)+((max(cox.km$time)-min(cox.km$time))/10)),
        xaxt="none"
    );
    axis(1, at=round(seq(0, max(cox.km$time), by = max(cox.km$time)/6), digits=2));

    # report HR value, 95% confidence intervals and p-values
    legend(
        "topright",
        legend=c(
          paste("HR=", round(cox.fit$conf.int[1,1], digits=2), sep=""),
          paste("95% CI (", round(cox.fit$conf.int[1,3], digits=2), "-", round(cox.fit$conf.int[1,4], digits=2), ")", sep=""),
          pValue
      ),
      box.col="transparent",
      cex = 0.8,
      bg="transparent"
    );

    # report numbers in risk groups
    legend("topleft", legend=c("Low expression", "High expression"), col=c("darkblue", "red"), lty=c(1,1), lwd=2.5, box.col="transparent", cex=0.9, bg="transparent");
    legend("bottom", legend="Number at risk\n\n\n\n", box.col="transparent", bg="transparent");
    text( risk.dataLow[2,], rep(0.05, length(risk.dataLow[2,])), col="darkblue", labels= as.numeric(risk.dataLow[3,]), cex=0.8, bg="transparent" );
    text( risk.dataHigh[2,], rep(0, length(risk.dataHigh[2,])), col="red", labels= as.numeric(risk.dataHigh[3,]), cex=0.8, bg="transparent"  );
    dev.off();
  }

  # MULTIVARIATE MODEL -- applied just for the mazimum survival!
  multi.cox.fit <- summary( coxph( Surv(tmp.surv.time, tmp.surv.stat) ~ all.rgs) );

  # Collate p value and HR from univariate and multivariate modelling to present in a table
  p_label = paste0("Log-rank P ",trunc," years");
  hr_label = paste0("HR ",trunc," years");
  pVal <- round(multi.cox.fit$sctest[3], digits = 3);
  hr <- round(multi.cox.fit$conf.int[1,1], digits = 2);
  #cat(pVal, "Multi<br>")
  #cat(hr, "Multi<br>")
  gene.covar[1,p_label] <- pVal;
  gene.covar[1,hr_label] <- hr;
}

if (length(genes) != 1) {
  rownames(gene.covar) <- c("Multivariate",genes);
} else {
  # deleting the "Multivariate row"
  gene.covar <- as.matrix(t(gene.covar[-1,]))
  rownames(gene.covar) <- genes;
}

# SAVE json FILE FOR jquery INTERACTIVE TABLE
json.filename = paste0(hexcode,".multivariate.json")
json.string.header = paste0("{\"draw\":0,\"recordsTotal\":",nrow(gene.covar),",\"recordsFiltered\":",nrow(gene.covar),",\"data\":[")
total.json.string = ""
for (k in 1:nrow(gene.covar)) {
	t = ""
	json.string.body = toString(paste0('"',as.character(c(rownames(gene.covar)[k],gene.covar[k,])),'"'))
	t = paste0(t,"[")
	t = paste0(t,json.string.body)
	t = paste0(t,"],")
	total.json.string = paste0(total.json.string,t)
}
totalnchars.json.string.body = nchar(total.json.string)
total.json.string = substr(total.json.string,1,totalnchars.json.string.body-1)
json.string.footer = "]}"

final.json.string = paste0(json.string.header, total.json.string, json.string.footer)
cat(final.json.string, file = json.filename)
