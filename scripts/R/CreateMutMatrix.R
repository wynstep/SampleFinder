################################################################################
#
#   File name: CreateMutMatrix.R
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
#   Description: Script for extracting the mutation values for selected samples
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
  make_option(c("-m", "--mut_file"), action="store", default=NA, type='character',
              help="File containing expression data"),
  make_option(c("-t", "--target"), action="store", default=NA, type='character',
              help="Clinical data saved in tab-delimited format"),
  make_option(c("-o", "--outfile"), action="store", default=NA, type='character',
              help="Output filename")
)

opt = parse_args(OptionParser(option_list=option_list))

mutFile <- opt$mut_file
annFile <- opt$target
outFile <- opt$outfile

#===============================================================================
#    Main
#===============================================================================

##### Read sample annotation file
annData <- read.table(annFile,sep="\t",as.is=TRUE,header=TRUE, stringsAsFactors=FALSE)

##### Read file with expression data
mutData <- as.data.frame(fread(mutFile,sep="\t",header=TRUE,stringsAsFactors = FALSE))
mutSamples = unique(mutData[,ncol(mutData)])
# intersect sample names in the target and mutation files
selected_samples <- intersect(as.character(annData$FILE_NAME),mutSamples)
# subsetting mutation file according to the intersected sample names
mutData.subset <- mutData[mutData[,ncol(mutData)] %in% mutSamples,]

### write expression values into a file
write.table(mutData.subset, file=outFile, sep = "\t", col.names = TRUE, row.names = FALSE, quote = FALSE)
