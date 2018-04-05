################################################################################
#
#   File name: PCA.R
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
#   Description: Script performing principal component analysis using normalised expression data.
#
################################################################################

#===============================================================================
#    Functions
#===============================================================================

##### Create 'not in' operator
"%!in%" <- function(x,table) match(x,table, nomatch = 0) == 0

##### Assign colours to analysed groups
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
  make_option(c("-p", "--principal_component"), action="store", default=NA, type='character',
              help="The principal component to be plotted together with the two subsequent most prevalent principal components"),
  make_option(c("-d", "--outfile"), action="store", default=NA, type='character',
              help="output file")
)

opt = parse_args(OptionParser(option_list=option_list))

expFile <- opt$exp_file
annFile <- opt$target
target <- opt$colouring
PC1 <- as.numeric(opt$principal_component)
PC2 <- PC1 + 1
PC3 <- PC1 + 2
outFile <- opt$outfile

#===============================================================================
#    Main
#===============================================================================

##### Read sample annotation file
annData <- read.table(annFile,sep="\t",as.is=TRUE,header=TRUE)

##### Read file with expression data
expData <- as.data.frame(fread(expFile,sep="\t",header=TRUE,stringsAsFactors = FALSE))
### assigning gene ids to rownames
rownames(expData) <- expData[,1]
expData <- expData[,-1]

selected_samples <- intersect(as.character(annData$FILE_NAME),colnames(expData))

#===============================================================================
#     Principal components analysis
#===============================================================================

##### Assign colours according to defined sample annotation
targets <- annData[which(annData$FILE_NAME %in% selected_samples),target]
targets.colour <- getTargetsColours(targets)

##### Keep only probes with variance > 0 across all samples
rsd <- apply(expData,1,sd)
expData.subset <- expData[rsd>0,]

##### Perform principal components analysis
expData_pca <- prcomp(t(expData.subset), scale=FALSE)

##### Get variance importance for all principal components
importance_pca <- summary(expData_pca)$importance[2,]
importance_pca <- paste(round(100*importance_pca, 2), "%", sep="")

##### Generate bar-plot (PLOTLY)
##### Prepare data frame
expData_pca.df <- data.frame(paste0("PC ", c(1:length(expData_pca$sdev))), expData_pca$sdev)
colnames(expData_pca.df) <- c("PC", "Variances")

##### The default order will be alphabetized unless specified as below
expData_pca.df$PC <- factor(expData_pca.df$PC, levels = expData_pca.df[["PC"]])

p <- plot_ly(expData_pca.df, x = ~PC, y = ~Variances, type = 'bar', width = 800, height = 600) %>%
layout(title = "The variances captured by principal components", xaxis = list(title = ""), margin = list(l=50, r=50, b=100, t=100, pad=4), autosize = F)

##### Save the box-plot as html (PLOTLY)
htmlwidgets::saveWidget(as_widget(p), paste0(outFile,".PCAbp.html"))

##### Generate PCA plot (PLOTLY)
##### Prepare data frame
expData_pca.df <- data.frame(targets, expData_pca$x[,PC1], expData_pca$x[,PC2], expData_pca$x[,PC3])
colnames(expData_pca.df) <- c("Target", "PC1", "PC2", "PC3")
rownames(expData_pca.df) <- selected_samples

p <- plot_ly(expData_pca.df, x = ~PC1, y = ~PC2, color = ~Target, text=rownames(expData_pca.df), colors = targets.colour[[1]], type='scatter', mode = "markers", marker = list(size=10, symbol="circle"), width = 800, height = 600) %>%
layout(title = "", xaxis = list(title = paste("PC ", PC1, " (",importance_pca[PC1],")",sep="")), yaxis = list(title = paste("PC ", PC2, " (",importance_pca[PC2],")",sep="")), margin = list(l=50, r=50, b=50, t=50, pad=4), autosize = F, legend = list(orientation = 'h', y = 1.1), showlegend=TRUE)

##### Save the PCA 2-D as html (PLOTLY)
htmlwidgets::saveWidget(as_widget(p), paste0(outFile,".PCA2d.html"))

##### Generate PCA 3-D plot (PLOTLY)
p <- plot_ly(expData_pca.df, x = ~PC1, y = ~PC2, z = ~PC3, color = ~Target, text=rownames(expData_pca.df), colors = targets.colour[[1]], type='scatter3d', mode = "markers", marker = list(size=8, symbol="circle"), width = 800, height = 800) %>%
layout(scene = list(xaxis = list(title = paste("PC ", PC1, " (",importance_pca[PC1],")",sep="")), yaxis = list(title = paste("PC ", PC2, " (",importance_pca[PC2],")",sep="")), zaxis = list(title = paste("PC ", PC3, " (",importance_pca[PC3],")",sep="")) ), margin = list(l=50, r=50, b=50, t=50, pad=4), autosize = F, legend = list(orientation = 'h', y = 1.1), showlegend=TRUE)

##### Save the box-plot as html (PLOTLY)
htmlwidgets::saveWidget(as_widget(p), paste0(outFile,".PCA3d.html"))