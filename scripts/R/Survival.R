 ### DESCRIPTION ########################################################
# script perform survival analysis on the molecular classes.

#===============================================================================
#    Functions
#===============================================================================
##### Create 'not in' operator
"%!in%" <- function(x,table) match(x,table, nomatch = 0) == 0;

#===============================================================================
#    Load libraries
#===============================================================================
suppressMessages(library(survival));
suppressMessages(library(optparse));
suppressMessages(library(plyr));

#===============================================================================
#    Catching the arguments
#===============================================================================
option_list = list(
  make_option(c("-t", "--target"), action="store", default=NA, type='character',
              help="Clinical data saved in tab-delimited format"),
  make_option(c("-o", "--outfile"), action="store", default=NA, type='character',
              help="Output filename")
);

opt = parse_args(OptionParser(option_list=option_list));

annFile <- opt$target;
outFile <- opt$outfile;

#===============================================================================
#    Main
#===============================================================================
# state the molecular subtypes
pam50.subtypes <- c("Basal", "Her2", "LumA", "LumB", "Normal");

# assign each subtype (riskgroup) to a colour
pam50.names <- list();
pam50.names[["Basal"]] <- c("colour" = "red");
pam50.names[["Her2"]] <- c("colour" = "cyan");
pam50.names[["LumA"]] <- c("colour" = "green");
pam50.names[["LumB"]] <- c("colour" = "hotpink");
pam50.names[["Normal"]] <- c( "colour" = "blue");

# read sample annotation file
annData <- read.table(annFile,sep="\t",as.is=TRUE,header=TRUE);

# subset to samples/patients for which full survival data are available
annData.s <- annData[ complete.cases(annData$SURVIVALTIME) , ];

# DEFINE THE SURVIVAL COVARIATES
# define the risk groups
rg <- rep( 0, length(rownames(annData.s)) );
names(rg) <- annData.s$FILE_NAME;
rg.names <- rg;

# extract survival time and events
surv.time <- as.numeric(annData.s$SURVIVALTIME);
surv.stat <- annData.s$SURVIVALSTATUS;
surv.stat.binary <- as.numeric(mapvalues(surv.stat, from = c("Alive", "Dead"), to = c(0,1)))

# assign riskgroups
for( subtype in pam50.subtypes ) {
	# identify samples in each subgroup
	samples <- annData.s[ annData.s$MOLECULAR_SUBTYPE == subtype , "FILE_NAME" ]
	rg[samples] <- subtype;
}

rg.cols <- c(
	"red" = "Basal", "cyan" = "Her2", "green" = "LumA", "hotpink" = "LumB", "blue" = "Normal"
);


# PLOT SURVIVAL
# apply univariate cox model
cox.fit <- summary( coxph( Surv(surv.time, surv.stat.binary) ~ rg) );
cox.km <- survfit(coxph(Surv(surv.time, surv.stat.binary) ~ strata(rg)));

# prepare risk table below the plot
times <- seq(0, max(cox.km$time), by=1);

risk.data <- data.frame(
	strata = summary(cox.km, times = times, extend = TRUE)$strata,
	time = summary(cox.km, times = times, extend = TRUE)$time,
	n.risk = summary(cox.km, times = times, extend = TRUE)$n.risk
);

riskData.BL <- t(risk.data[ risk.data$strata == "Basal", ]);
riskData.Her2 <- t(risk.data[ risk.data$strata == "Her2", ]);
riskData.LumA <- t(risk.data[ risk.data$strata == "LumA", ]);
riskData.LumB <- t(risk.data[ risk.data$strata == "LumB", ]);
riskData.Her2 <- t(risk.data[ risk.data$strata == "Her2", ]);
riskData.Normal <- t(risk.data[ risk.data$strata == "Normal", ]);

# generate kaplan Meier plot
png(filename=paste0(outFile,".KM_subtype.png"), width = 680, height = 680, units = "px", pointsize = 18);

plot(
	cox.km,
	mark.time=TRUE,
	col=names(rg.cols),
	xlab="Survival time",
	ylab="Survival probability",
	lty=c(1,1),
	lwd=2.5,
	cex.axis=0.9,
	ylim=c(0,1.15),
	xlim=c(0-min(cox.km$time)/2,max(cox.km$time)+min(cox.km$time)/2),
	xaxt="none"
);
axis( 1, at=round(seq(0, max(cox.km$time), by=1, digits=2)), cex.axis=0.9 );


# report numbers in risk groups
legend("topright", legend=rg.cols, col=names(rg.cols), lty=c(1,1), lwd=2.5, box.col="transparent", cex=0.8);
text(riskData.BL[2,], rep(0.10, length(riskData.BL[2,])), col=pam50.names[["Basal"]][["colour"]], labels= as.numeric(riskData.BL[3,]), cex=0.8);
text(riskData.Her2[2,], rep(0.07, length(riskData.Her2[2,])), col=pam50.names[["Her2"]][["colour"]], labels= as.numeric(riskData.Her2[3,]), cex=0.8);
text(riskData.LumA[2,], rep(0.04, length(riskData.LumA[2,])), col=pam50.names[["LumA"]][["colour"]], labels= as.numeric(riskData.LumA[3,]), cex=0.8);
text(riskData.LumB[2,], rep(0.01, length(riskData.LumB[2,])), col=pam50.names[["LumB"]][["colour"]], labels= as.numeric(riskData.LumB[3,]), cex=0.8);
text(riskData.Normal[2,], rep(-0.02, length(riskData.Normal[2,])), col=pam50.names[["Normal"]][["colour"]], labels= as.numeric(riskData.Normal[3,]), cex=0.8);

dev.off();
