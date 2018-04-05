### pam50.R ###
### DESCRIPTION ########################################################
# This script makes predicts PAM50 breast cancer subtypes using expression matrix
# Used in the BCCTB analysis tools interface to produce PAM50 subtypes

### HISTORY ###########################################################
# Version		Date					Coder						Comments
# 1.0			2017/04/18			Stefano					optimized from 2015/06/16 Emanuela's version

suppressMessages(library(optparse))
suppressMessages(library(plotly))
suppressMessages(library('genefu')) # library to calculate pam50

##### COMMAND LINE PARAMETERS ###############################################
### Here we take advantage of Optparse to manage arguments####
### Creating list of arguments ###
option_list = list(
  make_option(c("-e", "--exp_file"), action="store", default=NA, type='character',
              help="File containing experimental data"),
  make_option(c("-t", "--target"), action="store", default=NA, type='character',
              help="Clinical data saved in tab-delimited format"),
  make_option(c("-o", "--outfile"), action="store", default=NA, type='character',
              help="output file")
)

opt = parse_args(OptionParser(option_list=option_list))

expFile = opt$exp_file
annFile = opt$target
outFile = opt$outfile
pam50File = "../data/pam50.genes.txt"

### loading files ###
PAM50.genes <- read.table(file = pam50File, header = T, sep = "\t", row.names = 2)
annData <- read.table(file = annFile, header = T, sep = "\t", stringsAsFactors = F)
expData <- read.table(file = expFile, header = T, sep = "\t", stringsAsFactors = FALSE, row.names = NULL)
### assigning gene ids to rownames
rownames(expData) <- expData[,1]
expData <- expData[,-1]

### important! The pam50 calculation can be performed just on rna-seq data, so subsetting
rnaseq_samples = annData[which(annData$TECHNOLOGY=="rnaseq"),"FILE_NAME"]
selected_samples <- intersect(rnaseq_samples,colnames(expData))
expData <- expData[,selected_samples]

# perform pam50 calculation
PAM50Preds <- molecular.subtyping(sbt.model = "pam50",data=t(expData), annot=PAM50.genes,do.mapping=FALSE)
subtypes = as.character(unlist(PAM50Preds$subtype))
PAM50Preds.report = data.frame("sample_name"=names(PAM50Preds$subtype), "subtype"=subtypes, stringsAsFactors=F)

# calculating frequency of different molecular subtypes
pam50.freq <- as.data.frame(table(PAM50Preds$subtype))
# calculating the total number of samples into the report
total = sum(pam50.freq$Freq)

# generating the Plotly barplot
pam50_plot <- plot_ly(pam50.freq,
        x = pam50.freq$Var1,
        y = pam50.freq$Freq,
        type = 'bar',

        ### defining bar colors ####
        # rgba(255,51,51,0.3) --> red
        # rgba(0,0,204,0.3) --> blue
        # rgba(102,204,0,0.3) --> light green
        # rgba(0,102,0,0.3) --> dark green
        # rgba(204,0,102,0.3) --> dark pink

        marker = list(color = c('rgba(255,51,51,0.3)', 'rgba(0,0,204,0.3)',
                                'rgba(102,204,0,0.3)', 'rgba(0,102,0,0.3)',
                                'rgba(204,0,102,0.3)'),
                      # defining bar line colors (same as bar colors but different opacity)
                      line = list(color = c('rgba(255,51,51,1)', 'rgba(0,0,204,1)',
                                            'rgba(102,204,0,1)', 'rgba(0,102,0,1)',
                                            'rgba(204,0,102,1)'), width = 1.5)),
        # adding percentage in the hover label text
        text = paste(round((pam50.freq$Freq/total)*100, digits = 2),'%'),
        textpositionsrc = 'center',
        width = '800px'
) %>%
layout(yaxis = list(title = 'Frequency'),
       xaxis = list(title = 'Subtypes'),
      barmode = 'group',
      title = 'Molecular classification',
      showlegend = FALSE
)

## defining plot file name
pam50_filename = paste0(outFile,".pam50.html")
## saving generated plot into web page
htmlwidgets::saveWidget(pam50_plot, pam50_filename)

### generating json for the datatable ###
# saving json file for the jquery interactive table
json.filename = paste0(outFile,".pam50.json")
json.string.header = paste0("{\"draw\":0,\"recordsTotal\":",nrow(PAM50Preds.report),",\"recordsFiltered\":",nrow(PAM50Preds.report),",\"data\":[")
total.json.string = ""
for (k in 1:nrow(PAM50Preds.report)) {
  t = ""
  json.string.body = toString(paste0('"',PAM50Preds.report[k,c("sample_name","subtype")],'"'))
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
