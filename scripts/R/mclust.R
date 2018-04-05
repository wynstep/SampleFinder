### bcntb.mclust.R ###
### DESCRIPTION ########################################################
# This script assigns ER, PR and Her2 status to samples based on their expression values
# and identifies triple negative samples

### HISTORY ###########################################################
# Version		Date					Coder						Comments
# 1.0			2017/04/18			Stefano					optimized from 'bcntb.apply.mclust.eg.R' Emanuela's version

### PARAMETERS #######################################################
suppressMessages(library(mclust))
suppressMessages(library(optparse))
suppressMessages(library(gdata))
suppressMessages(library(plotly))

##### COMMAND LINE PARAMETERS ###############################################
### Here we take advantage of Optparse to manage arguments####
### Creating list of arguments ###
option_list = list(
  make_option(c("-e", "--exp_file"), action="store", default=NA, type='character',
              help="File containing experimental data"),
  make_option(c("-t", "--target"), action="store", default=NA, type='character',
              help="Clinical data saved in tab-delimited format"),
  make_option(c("-o", "--outfile"), action="store", default=NA, type='character',
              help="Output file")
)

opt = parse_args(OptionParser(option_list=option_list))

expFile = opt$exp_file
annFile = opt$target
outFile = opt$outfile

### loading files ###
receptor.genes = c("ESR1","PGR","ERBB2")
annData <- read.table(file = annFile, header = T, sep = "\t", stringsAsFactors = F)
expData <- read.table(file = expFile, header = T, sep = "\t", stringsAsFactors = FALSE, row.names = NULL)
### assigning gene ids to rownames
rownames(expData) <- expData[,1]
expData <- expData[,-1]

### important! The mclust calculation can be performed just on rna-seq data, so subsetting
rnaseq_samples = annData[annData$TECHNOLOGY=="rnaseq","FILE_NAME"]
selected_samples <- intersect(rnaseq_samples,colnames(expData))
expData <- expData[,selected_samples]
annData <- annData[which(annData$FILE_NAME %in% selected_samples),]

# implement MCLUST to a +'ve or -'ve receptor status to each sample
# where G=2  i.e. 2-component Gaussian mix
mclust.final.report <- data.frame("File_name"=annData$FILE_NAME, stringsAsFactors=F)
for( rgene in receptor.genes ) {
  if (!is.na(expData[rgene , ])) {
    optimal.model <- Mclust(
      data = expData[rgene , ],
      G = 2,
      prior = NULL,
      control = emControl(),
      initialization = NULL,
      warn = FALSE
    );

    # assign sample names into -'ve (grp.1) & +'ve (grp.2) groups
    grp.1 <- names( which(optimal.model$classification == 1) );
    grp.2 <- names( which(optimal.model$classification == 2) );

    # assign the -'ve & +'ve sample names for each EnsemblGene to list
    neg.data <- data.frame('File_name'=grp.1,'status'=rep('0',length(grp.1)), stringsAsFactors=F);
    colnames(neg.data)[2] = rgene
    pos.data <- data.frame('File_name'=grp.2,'status'=rep('1',length(grp.2)), stringsAsFactors=F);
    colnames(pos.data)[2] = rgene

    ### updating mclust total report target file ###
    mclust.final.report <- cbind(mclust.final.report, rbind(neg.data,pos.data)[,2])
  } else {
    na.data <- data.frame('File_name'=ann.data$File_name,'status'=rep(NA,length(ann.data$File_name)));
    colnames(na.data)[2] = rgene
    mclust.final.report <- cbind(mclust.final.report, na.data[,2])
  }
}

colnames(mclust.final.report) = c('File_name','ER','PR','HER2')
mclust.final.report <- mclust.final.report[!duplicated(mclust.final.report[,1]),]

# id TN sample names from list by identifying er, pr and her2 negative samples
mrna.na <- mclust.final.report[which(is.na(mclust.final.report[,2]) | is.na(mclust.final.report[,3]) | is.na(mclust.final.report[,4])),];
mrna.tn <- mclust.final.report[which(mclust.final.report[,2]==0 & mclust.final.report[,3]==0 & mclust.final.report[,4]==0),];
mrna.non.tn <- mclust.final.report[ !(mclust.final.report$File_name %in% rbind(mrna.na,mrna.tn)$File_name), ]

# all TNs with assignment of TN to "1", "0" otherwise
if (nrow(mrna.na) > 0) {
    mrna.na$tn_status = NA
}
if (nrow(mrna.tn) > 0) {
    mrna.tn$tn_status = 1
}
if (nrow(mrna.non.tn) > 0) {
    mrna.non.tn$tn_status = 0
}

# updating final report
mclust.final.report = rbind.data.frame(mrna.tn,mrna.non.tn,mrna.na)

# creating data report for plotly
mclust.plotly <- data.frame(row.names = c("Neg", "Pos"), ER = numeric(2), PGR=numeric(2), HER2=numeric(2), TripleNegative=numeric(2))
#### Counting samples with Positive or Negative status for each receptor (and also Triple Negative)
ER_report <- table(mclust.final.report$ER)
PGR_report <- table(mclust.final.report$PR)
HER2_report <- table(mclust.final.report$HER2)
TN_report <- table(mclust.final.report$tn_status)
#### Filling mclust report
mclust.plotly$ER <- ER_report
mclust.plotly$PGR <- PGR_report
mclust.plotly$HER2 <- HER2_report
mclust.plotly$TripleNegative <- TN_report

# creating stacked barplot for receptor status
mclust = as.data.frame(t(mclust.plotly))
# creating stacked barplot for receptor status
r.status <- plot_ly(mclust[-4,],
                    x = rownames(mclust)[-4],
                    y = mclust[-4,]$Neg ,
                    type = 'bar',
                    name = 'Negative',
                    legendgroup="1",
                    # rgba(255,51,51,0.3) --> red
                    # rgba(0,0,204,0.3) --> blue
                    marker = list(color = 'rgba(255,51,51,0.3)',
                                  line = list(color = 'rgba(255,51,51,1)',
                                              width = 1.5)),
                    text = paste(round((mclust$Neg[-4]/rowSums(mclust)[-4])*100, digits = 2),'%'),
                    textpositionsrc = 'center',
                    width = '800px'
            ) %>%
            add_trace(y = mclust[-4,]$Pos,
                      name = 'Positive',
                      legendgroup="1",
                      marker = list(color = 'rgba(0,0,204,0.3)',
                                    line = list(color = 'rgba(0,0,204,1)',
                                                width = 1.5)),
                      text = paste(round((mclust$Pos[-4]/rowSums(mclust)[-4])*100, digits = 2),'%')
            ) %>%
            layout(yaxis = list(title = 'Count'),
                   barmode = 'stack'
            )

# creating stacked barplot for triple negative status

triple.negative <- plot_ly(mclust[4,],
                            x = rownames(mclust)[4],
                            y = mclust[4,]$Neg ,
                            type = 'bar',
                            name = 'No',
                            legendgroup="2",
                             # rgba(102,204,0,0.3) --> light green
                             # rgba(204,0,102,0.3) --> dark pink
                            marker = list(color = 'rgba(102,204,0,0.3)',
                                          line = list(color = 'rgba(102,204,0,1)',
                                                      width = 1.5)),
                            text = paste(round((mclust$Neg[4]/rowSums(mclust)[4])*100, digits = 2),'%'),
                            textpositionsrc = 'center',
                            width = '800px'
                    ) %>%
                    add_trace(y = mclust[4,]$Pos,
                              name = 'Yes',
                              legendgroup="2",
                              marker = list(color = 'rgba(204,0,102,0.3)',
                                            line = list(color = 'rgba(204,0,102,1)',
                                                        width = 1.5)),
                              text = paste(round((mclust$Pos[4]/rowSums(mclust)[4])*100, digits = 2),'%')
                    ) %>%
                    layout(yaxis = list(title = 'Count'), barmode = 'stack')

# merging plots together
mclust.plot <- subplot(r.status, triple.negative, nrows = 1, margin = 0.02, shareY = TRUE)
mclust.plot <- layout(mclust.plot, title="Receptor Status");

## defining plot file name
mclust_filename = paste0(outFile,".mclust.html")

## saving generated plot into web page
htmlwidgets::saveWidget(mclust.plot, mclust_filename)

### generating json for the datatable ###
# saving json file for the jquery interactive table
mclust.json = mclust.final.report
mclust.json$ER = ifelse(mclust.json$ER==1, "Positive", "Negative")
mclust.json$PR = ifelse(mclust.json$PR==1, "Positive", "Negative")
mclust.json$HER2 = ifelse(mclust.json$HER2==1, "Positive", "Negative")
mclust.json$tn_status = ifelse(mclust.json$tn_status==1, "Yes", "No")
json.filename = paste0(outFile,".mclust.json")
json.string.header = paste0("{\"draw\":0,\"recordsTotal\":",nrow(mclust.json),",\"recordsFiltered\":",nrow(mclust.json),",\"data\":[")
total.json.string = ""
for (k in 1:nrow(annData)) {
  t = ""
  json.string.body = toString(paste0('"',mclust.json[k,c("File_name","ER","PR","HER2","tn_status")],'"'))
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
