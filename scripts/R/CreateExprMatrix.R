################################################################################
#
#   File name: CreateExprMatrix.R
#
#   Authors: Stefano Pirro ( s.pirro@qmul.ac.uk )
#
#   Barts Cancer Institute,
#   Queen Mary, University of London
#   Charterhouse Square, London EC1M 6BQ
#
################################################################################

################################################################################
#
#   Description: Script for extracting the expression values for selected TCGA and CCLE samples
#
################################################################################

# silent warnings
options(warn=-1)

#===============================================================================
#    Load libraries
#===============================================================================

suppressMessages(library(optparse))
suppressMessages(library(data.table))

#===============================================================================
#    Catching the arguments
#===============================================================================
option_list = list(
  make_option(c("-e", "--exp_file"), action="store", default=NA, type='character',
              help="File containing expression data"),
  make_option(c("-t", "--target"), action="store", default=NA, type='character',
              help="Clinical data saved in tab-delimited format"),
  make_option(c("-f", "--outfile"), action="store", default=NA, type='character',
              help="Output filename")
)

opt = parse_args(OptionParser(option_list=option_list))

expFile <- opt$exp_file
annFile <- opt$target
outFile <- opt$outfile

#===============================================================================
#    Main
#===============================================================================

##### Read sample annotation file
annData <- read.table(annFile,sep="\t",as.is=TRUE,header=TRUE)

##### Read file with expression data
expData <- as.data.frame(fread(expFile,sep="\t",header=TRUE,stringsAsFactors = FALSE))

# intersecting the sample names in the target file and in the expression matrix
selected_samples <- intersect(as.character(annData$FILE_NAME),colnames(expData))
# subset the expression matrix according to the intersected samples
expData.subset <- as.data.frame(expData[,colnames(expData) %in% selected_samples])
expData.subset <- cbind(expData$Gene.ID,expData.subset)
colnames(expData.subset) = c("gene.ID", selected_samples)

### write expression values into a file
write.table(expData.subset, file=outFile, sep = "\t", col.names = TRUE, row.names = FALSE, quote = FALSE)
