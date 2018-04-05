################################################################################
#
#   File name: ExprProfile.R
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
#   Description: Script generating box-plots and bar-plots to visualise expression measurments across samples and groups (as indicated in target file) from normalised expression data for user-defined gene. NOTE: the script allowes to process gene matrix with duplicated gene IDs.
#
#   Command line use example: R --file=./ExprProfile.R --args "CCLE_PC_processed_mRNA.txt" "CCLE_PC_target.txt" "Target" "KRAS" "Example_results/BC_ExprProfile"
#
#   First arg:      Full path with name of the normalised expression matrix
#   Second arg:     Full path with name of the text file with samples annotation. The file is expected to include the following columns: sample name (1st column) and annotation (3rd column)
#   Third arg:      Variable from the samples annotation file to be used for samples grouping
#   Forth arg:      ID of gene/probe of interest
#   Fifth arg:      Full path with name of the output folder

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

##### Create 'not in' operator
"%!in%" <- function(x,table) match(x,table, nomatch = 0) == 0


##### Assign colours to analysed groups (for plots filling)
getTargetsColours <- function(targets) {

    ##### Predefined selection of colours for groups
    targets.colours <- c("red","blue","green","darkgoldenrod","darkred","deepskyblue", "coral", "cornflowerblue", "chartreuse4", "bisque4", "chocolate3", "cadetblue3", "darkslategrey", "lightgoldenrod4", "mediumpurple4", "orangered3")

    f.targets <- factor(targets)
    vec.targets <- targets.colours[1:length(levels(f.targets))]
    targets.colour <- rep(0,length(f.targets))
    for(i in 1:length(f.targets))
    targets.colour[i] <- vec.targets[ f.targets[i]==levels(f.targets)]

    return( list(vec.targets, targets.colour) )
}

##### Deal with the duplicated genes
duplGenes <- function(expData) {
  gene_names = expData[,1]
  splitted.exp.data <- split( expData , f = gene_names)

  # calculating IQR and selecting just the most variables probes
  for(i in 1:length(splitted.exp.data)) {
    iqr = 0
    gene_name = ''
    for(j in 1:nrow(splitted.exp.data[[i]])) {
      print(splitted.exp.data[[i]][j,1])
      tmp_iqr = IQR(splitted.exp.data[[i]][j,2:length(splitted.exp.data[[i]])], na.rm = FALSE, type = 7)
      if (tmp_iqr > iqr) {
        iqr = tmp_iqr
      } else {
        splitted.exp.data[[i]] <- splitted.exp.data[[i]][-j,]
      }
    }
  }

  expData <- unsplit(splitted.exp.data, f = gene_names, drop = FALSE)
  return(expData)
}


#===============================================================================
#    Load libraries
#===============================================================================

suppressMessages(library(plotly))
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
  make_option(c("-c", "--colouring"), action="store", default=NA, type='character',
              help="Variable from the samples annotation file to be used for samples colouring"),
  make_option(c("-p", "--gene"), action="store", default=NA, type='character',
              help="ID of gene/probe of interest"),
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
target <- opt$colouring
gene <- opt$gene
outFolder <- opt$dir
hexcode <- opt$hexcode
type_an <- opt$type

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

selected_samples <- intersect(as.character(annData$FILE_NAME),colnames(expData))
expData <- expData[,selected_samples]

if ( gene %in% rownames(expData) ) {
  gene.expr <- expData[gene, ]
}

##### Change working directory to the project workspace
setwd(outFolder)

#===============================================================================
#     Generate box-plot and bar-plot
#===============================================================================

targets <- annData[which(annData$FILE_NAME %in% selected_samples),target]
targets.colour <- getTargetsColours(targets)

##### Order samples accordingly to investigated groups
dataByGroups <- NULL
targetByGroups <- NULL
colourByGroups <- NULL
expr <- list()
averegeExpr <- NULL

for (i in 1:length(unique(targets))) {

    ##### Separate samples accordingly to investigated groups
    expr.tmp <- gene.expr[ targets %in% unique(sort(targets))[i] ]
    print(expr.tmp)
    averegeExpr <- c(averegeExpr, rep(mean(as.numeric(expr.tmp)), length(expr.tmp)))
    colour <- targets.colour[[2]][ targets %in% unique(sort(targets))[i] ]

    ##### Order samples within each group based on the expression level
    expr.tmp <- expr.tmp[order(expr.tmp)]
    colour <- colour[order(expr.tmp)]

    expr[[i]] <- as.numeric(expr.tmp)
    names(expr)[[i]] <- unique(sort(targets))[i]
    dataByGroups <- c(dataByGroups, expr.tmp)
    targetByGroups <- c(targetByGroups, targets[ targets %in% unique(sort(targets))[i] ])
    colourByGroups <- c(colourByGroups, colour)
}

dataByGroups <- unlist(dataByGroups)

##### Generate box-plot  (PLOTLY)
##### Prepare data frame
gene.expr.df <- data.frame(targets, as.numeric(gene.expr))
colnames(gene.expr.df) <- c("Group", "Expression")


p <- plot_ly(gene.expr.df, y= ~Expression, color = ~Group, type = 'box', jitter = 0.3, pointpos = 0, boxpoints = 'all', marker = list(color = colourByGroups), line = list(color = unique(colourByGroups)), width = 800, height = 600) %>%
layout(yaxis = list( title = paste0(gene, "  mRNA expression (z-score)")), margin = list(l=50, r=50, b=50, t=50, pad=4), autosize = F, legend = list(orientation = 'h', y = 1.1))

##### Save the box-plot as html (PLOTLY)
htmlwidgets::saveWidget(as_widget(p), paste0(hexcode,".live.box.html"))


##### Generate bar-plot (PLOTLY)
##### Prepare data frame
dataByGroups.df <- data.frame(targetByGroups, names(dataByGroups), as.numeric(dataByGroups))
colnames(dataByGroups.df) <- c("Group","Sample", "Expression")

##### The default order will be alphabetized unless specified as below
dataByGroups.df$Sample <- factor(dataByGroups.df$Sample, levels = dataByGroups.df[["Sample"]])

p <- plot_ly(dataByGroups.df, x = ~Sample, y = ~Expression, color = ~Group, type = 'bar',  marker = list(color = c(colourByGroups)), width = 800, height = 400) %>%
layout(title = "", xaxis = list(title = ""), yaxis = list(title = paste0(gene, "  mRNA expression (z-score)")), margin = list(l=50, r=50, b=100, t=50, pad=4), autosize = F, legend = list(orientation = 'h', y = 1.1))

##### Save the bar-plot as html (PLOTLY)
htmlwidgets::saveWidget(as_widget(p), paste0(hexcode,".live.bar.html"))

##### Clear workspace
rm(list=ls())
##### Close any open graphics devices
graphics.off()
