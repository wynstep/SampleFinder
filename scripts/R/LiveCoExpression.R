################################################################################
#
#   File name: GeneCoExprMultiGenes.R
#
#   Authors: Jacek Marzec ( j.marzec@qmul.ac.uk )
#
#   Barts Cancer Institute,
#   Queen Mary, University of London
#   Charterhouse Square, London EC1M 6BQ
#
################################################################################

################################################################################
#
#   Description: Script for calculating coexpression between user-defined genes/probes. Person correlation coefficient is used to measure correaltion between genes' expression. The pairwise correlation between expression of user-defined genes is depicted in a form of a correlation matrix heat map. NOTE: the script allowes to process gene matrix with duplicated gene IDs. It allows to process up to 50 genes
#
#   Command line use example: R --file=./GeneCoExprMultiGenes.R --args "TCGA_PAAD_normalized.txt" "TCGA_PAAD_target.txt" "Genes_of_interest.txt" "Example_results/PC_GeneCoExprMultiGenes" "PDAC"
#
#   First arg:      Full path with name of the normalised expression matrix
#   Second arg:     Full path with name of the text file with samples annotation. The file is expected to include the following columns: sample name (1st column) and annotation (3rd column)
#   Third arg:      ID of gene/probe of interest
#   Fourth arg:     Full path with name of the output folder
#   Fifth arg (OPTIONAL):  Samples group to use for the analysis
#
################################################################################

# silent warnings
options(warn=-1)

##### Clear workspace
rm(list=ls())
##### Close any open graphics devices
graphics.off()

### Setting environment for pandoc
Sys.setenv(HOME = "")

#===============================================================================
#    Functions
#===============================================================================

##### Prepare object to write into a file
prepare2write <- function (x) {

	x2write <- cbind(rownames(x), x)
    colnames(x2write) <- c("",colnames(x))
	return(x2write)
}

##### Create 'not in' operator
"%!in%" <- function(x,table) match(x,table, nomatch = 0) == 0


##### Deal with the duplicated genes
duplGenes <- function(expData) {

    genesList <- NULL
    genesRepl <- NULL

    for ( i in 1:nrow(expData) ) {

        geneName <- expData[i,1]

        ##### Distingish duplicated genes by adding duplicate number
        if ( geneName %in% genesList ) {

            ##### Report genes with more than one duplicates
            if ( geneName %in% names(genesRepl) ) {

                genesRepl[[ geneName ]] = genesRepl[[ geneName ]]+1

                geneName <- paste(geneName, "-", genesRepl[[ geneName ]], sep="")

            } else {
                genesRepl[[ geneName ]] <- 2

                geneName <- paste(geneName, "-2", sep="")
            }
        }
        genesList <- c(genesList,geneName)
    }

    rownames(expData) <- genesList

    ##### Remove the first column with gene names, which now are used as row names
    expData <- expData[, -1]

    return(expData)
}


#===============================================================================
#    Load libraries
#===============================================================================

suppressMessages(library(gplots))
suppressMessages(library(plotly))
suppressMessages(library(heatmaply))
suppressMessages(library(optparse))
suppressMessages(library(data.table))

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
type_an = opt$type

#===============================================================================
#    Main
#===============================================================================

##### Read sample annotation file
annData <- read.table(annFile,sep="\t",as.is=TRUE,header=TRUE)

##### Read file with expression data
expData <- as.data.frame(fread(expFile,sep="\t",header=TRUE, stringsAsFactors = FALSE))
### assigning gene ids to rownames
rownames(expData) <- expData[,1]
expData <- expData[,-1]

if (type_an == "dr_co_expression") {
	rnaseq_samples = annData[annData$TECHNOLOGY=="rnaseq","FILE_NAME"]
	selected_samples <- intersect(rnaseq_samples,colnames(expData))
	expData <- expData[,selected_samples]
	annData <- annData[which(annData$FILE_NAME %in% selected_samples),]
}

genes = unlist(strsplit(gene_list, ","))
gene.expr <- expData[genes, ]

##### Identify genes of interest not present in the expression matrix
absentGenes <- genes[genes %!in% rownames(expData)]

##### Change working directory to the project workspace
setwd(outFolder)

#===============================================================================
#     Calculate Pearson correlation coefficients
#===============================================================================

##### Remove gense with standard deviation = 0 (otherwise the cor.test complains)
#####  Keep only genes/probes with variance > 0 across all samples (otherwise the cor.test complains)
rsd<-apply(gene.expr,1,sd)
expData <- gene.expr[rsd>0,]

##### Check if used-defined genes are present in the data
for ( i in 1:length(genes) ) {
	if ( genes[i] %!in% rownames(expData) ) {
    	#cat("The genes/probes", genes[i], "are not present in the data!", sep=" ")
			genes <- genes[-i]
		}
}

##### Calculate Pearson correlation coefficient for user-defined genes
corr.res <- matrix(data = 0, nrow = nrow(expData), ncol = (2*length(genes))+1, dimnames = list( rownames(expData), c( "Gene", paste(rep(genes, each = 2), c("Correlation", "P-value")) ) ))

for ( i in 1:length(genes) ) {
	for ( x in 1:nrow(expData) ) {

    #### Pearson correlation coefficient and test P
		corr.res[x, paste0(genes[i], " Correlation")] <- cor.test( as.numeric(expData[ genes[i] ,]), as.numeric(expData[ x, ]), method = "pearson" )$estimate;
		corr.res[x, paste0(genes[i], " P-value")] <- cor.test( as.numeric(expData[ genes[i] ,]), as.numeric(expData[ x, ]), method = "pearson" )$p.value;
	}
}

corr.res <- data.frame(corr.res)
corr.res[,"Gene"] <- rownames(expData)
colnames(corr.res) <- c( "Gene", paste(rep(genes, each = 2), c("Correlation", "P-value")) )

#===============================================================================
#     Pairwise correlation heat map for defined genes
#===============================================================================

##### Extract the the correlation results for the user-defined genes
corr.res.genes <- corr.res[genes, paste(genes, "Correlation")]
colnames(corr.res.genes) <- rownames(corr.res.genes)
corr.res.genes[upper.tri(corr.res.genes)] <- NA

##### Generate heatmap including the top correlated genes (PLOTLY)
p <- heatmaply(data.frame(corr.res.genes), dendrogram="none", colors = colorRampPalette(c('dark blue','white','dark red'))(100), limits=c(-1,1), scale="none", trace="none", hide_colorbar = FALSE, fontsize_row = 8, fontsize_col = 8) %>%
layout(autosize = TRUE, width = 800, margin = list(l=150, r=50, b=150, t=50, pad=4), showlegend = FALSE)

##### Save the heatmap as html (PLOTLY)
htmlwidgets::saveWidget(as_widget(p), paste0(hexcode,".live.corr_hm.html"))

##### Clear workspace
rm(list=ls())
##### Close any open graphics devices
graphics.off()
