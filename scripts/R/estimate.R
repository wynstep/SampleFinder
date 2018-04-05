### bcntb.estimate.R ###
### DESCRIPTION ########################################################
# This script evaluates tumor purity by applying
# 'estimate' R package on an expression matrix

### HISTORY ###########################################################
# Version		Date					Coder						Comments
# 1.0			2017/04/19			Stefano					optimized from 'bcntb.apply.estimate.R' Emanuela's version

### PARAMETERS #######################################################
suppressMessages(library(estimate))
suppressMessages(library(optparse))
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

### debug ###
#setwd("/Users/pirro01/Desktop/")
#expFile = "test"
#annFile = "tmplszyor.target.txt"
############

### loading files ###
annData <- read.table(file = annFile, header = T, sep = "\t", stringsAsFactors = F)
expData <- read.table(file = expFile, header = T, sep = "\t", stringsAsFactors = FALSE, row.names = NULL)
### assigning gene ids to rownames
rownames(expData) <- expData[,1]
expData <- expData[,-1]

### important! The estimate calculation can be performed just on rna-seq data, so subsetting
rnaseq_samples = annData[annData$TECHNOLOGY=="rnaseq","FILE_NAME"]
selected_samples <- intersect(rnaseq_samples,colnames(expData))
expData <- expData[,selected_samples]
annData <- annData[which(annData$FILE_NAME %in% selected_samples),]

### Performing estimate analysis ###
estimateFile = paste0(outFile,".estimate.gct")
scoreFile = paste0(outFile, ".score.gct")
fcg <- filterCommonGenes(expFile, estimateFile, id = "GeneSymbol")
estimateScore(estimateFile, output.ds=scoreFile)

# updating annFile
scoreData = as.data.frame(t(read.table(scoreFile, sep = "\t", skip=2,header=T,row.names=1,stringsAsFactors = F)[,-1]))
scoreData = scoreData[rownames(scoreData) %in% annData$FILE_NAME, ]
annData <- cbind(annData, scoreData)

### producing plot ###
#### initialising colorbar style
cb_style<-list(
  colorbar = list(title = "Tumour Purity",
                  titleside = "top",
                  titlefont = list(
                    family = "Helvetica Neue",
                    size = 15
                  ),
                  tickfont = list (
                    family = "Helvetica Neue",
                    size = 12,
                    color = 'grey'
                  ),
                  ypad = 10,
                  y = 3,
                  thickness = 15,
                  nticks = 10,
                  xanchor = 'center',
                  xpad = 10),
  size = 5, symbol = 'circle', cmin=0, cmax=1, cauto = F)

### initialising plot margins
margins <- list(
  l = 10,
  r = 10,
  b = 10,
  t = 10,
  pad = 1
)

### create plotly scatterplot
p <- plot_ly(annData,
             x = annData$StromalScore,
             y = annData$ImmuneScore,
             z = annData$ESTIMATEScore,
             color = annData$TumorPurity,
             hoverinfo = 'text',
             text = paste('Sample Name:', annData$FILE_NAME,
                          '<br><br>StromalScore:', annData$StromalScore,
                          '<br>ImmuneScore:', annData$ImmuneScore,
                          '<br>ESTIMATEScore:', annData$ESTIMATEScore,
                          '<br><br><b>Tumour Purity</b>:', annData$TumorPurity),
             marker = cb_style,
             width = '800px'
) %>%
  add_markers() %>%
  layout(scene = list(xaxis = list(title = 'StromalScore', orientation = "v"),
                      yaxis = list(title = 'ImmuneScore'),
                      zaxis = list(title = 'EstimateScore')),
         margin = margins,
         title = "Tumor purity"
  )

## defining plot file name
estimate_filename = paste0(outFile,".estimate.html")

## saving generated plot into web page
htmlwidgets::saveWidget(p, estimate_filename)

### generating json for the datatable ###
# saving json file for the jquery interactive table
json.filename = paste0(outFile,".estimate.json")
json.string.header = paste0("{\"draw\":0,\"recordsTotal\":",nrow(annData),",\"recordsFiltered\":",nrow(annData),",\"data\":[")
total.json.string = ""
for (k in 1:nrow(annData)) {
  t = ""
  json.string.body = toString(paste0('"',as.character(annData[k,c("FILE_NAME","MOLECULAR_SUBTYPE","TumorPurity")]),'"'))
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
