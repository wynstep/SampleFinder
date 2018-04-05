### HISTORY ##############################################################################
# Version			Date					Coder						Comments
# 1.0 				13/07/2017				Ema & Stefano
#
#   Contact: Emanuela, Jacek, Stefano (e.gadaleta@qmul.ac.uk; s.pirro@qmul.ac.uk)
#
#   Barts Cancer Institute
#   Queen Mary, University of London
#   Charterhouse Square, London EC1M 6BQ
#
### DESCRIPTION ##########################################################################
#  Read in the mutation and clinical data downloaded previously from TCGA and plot top/defined mutations
## https://cancergenome.nih.gov/; https://ocg.cancer.gov/programs/target/research
## http://bioconductor.org/packages/release/bioc/vignettes/TCGAbiolinks/inst/doc/tcgaBiolinks.html


#========================================================================================#
# Clear workspace
rm(list=ls());
# Close any open graphics devices
graphics.off();

suppressMessages(library(TCGAbiolinks))
suppressMessages(library(SummarizedExperiment))
suppressMessages(library(optparse))

#===============================================================================
#    Catching the arguments
#===============================================================================
option_list = list(
  make_option(c("-e", "--mut_file"), action="store", default=NA, type='character',
              help="File containing mutation data"),
  make_option(c("-t", "--target"), action="store", default=NA, type='character',
              help="Clinical data saved in tab-delimited format"),
    make_option(c("-d", "--outfile"), action="store", default=NA, type='character',
                help="output file")
)

opt = parse_args(OptionParser(option_list=option_list))

mutFile <- opt$mut_file
annFile <- opt$target
outFile <- opt$outfile

#========================================================================================#
#    										MAIN
#========================================================================================#

# COLLECTION OF INFORMATION: CLINICAL, MUTATION AND GENES
annData <- read.table(file = annFile, row.names = NULL, header = T, sep = "\t", stringsAsFactors =F);
mutData <- read.table(file = mutFile, row.names = NULL, header = T, sep = "\t", stringsAsFactors=F);
annData$bcr_patient_barcode = annData$FILE_NAME
#========================================================================================#
#    							PREPARE DATA INPUTS										 #
#========================================================================================#
# DEFINE PLOTTING PARAMETERS

# count the number of times the genes are mutated
mutCount <- as.data.frame(table(mutData$Hugo_Symbol));
mutCount <- mutCount[ order(mutCount$Freq, decreasing=TRUE ),];
top.genes = c(10, 20, 30, 40, 50, 75, 100);

for (tg in top.genes) {
  # initilising filename
  OutDataName <- paste0(outFile, ".oncoprint_top",tg,".pdf")
  mutCount.top <- mutCount[c(1:tg),];
  # Subset clinical data to display pre-defined covariates
  annCovar <- c("bcr_patient_barcode","SURVIVALSTATUS", "GENDER", "MOLECULAR_SUBTYPE", "STAGE");
  annData.top <- annData[ , annCovar];
  # GENERATE ONCOPRINT
  TCGAvisualize_oncoprint(
  	mut = mutData[mutData$Hugo_Symbol %in% mutCount[,1],],
  	genes = mutCount.top[,1],
  	filename = OutDataName,
  	annotation = annData.top,
  	color=c("background"="#CCCCCC","DEL"="purple", "INS"="yellow","SNP"="brown"),
  	rows.font.size= 4,
  	width = 5,
  	heatmap.legend.side = "right",
  	dist.col = 0,
  	label.font.size = 4
  );
}
