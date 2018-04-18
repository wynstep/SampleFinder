# SampleFinder
Web Portal for searching samples from BCNTB

## Structure of the SampleFinder directory

+ [index.php](./index.php)
+ [header.php](./header.php)
+ [body.php](./body.php)
+ [ccle.php](./ccle.php)
+ [tgca.php](./tcga.php)
+ [data_returned.php](./data_returned.php)
+ [request.php](./request.php)
	+ [data](./data)
		+ [SF2BiomartMapping.txt](./data/SF2BiomartMapping.txt)
		+ [SF2MinerMapping.txt](./data/SF2MinerMapping.txt)
		+ [ccle_cn.csv](./data/ccle_cn.csv)
		+ [ccle_cn_target_with_chr.csv](./data/ccle_cn_target_with_chr.csv)
		+ [ccle_gea_target.txt](./data/ccle_gea_target.txt)
		+ [ccle_gene_exp.csv](./data/ccle_gene_exp.csv)
		+ [ccle_gene_list.txt](./data/ccle_gene_list.txt)
		+ [ccle_mut.csv](./data/ccle_mut.csv)
		+ [dr_gea_target.txt](./data/dr_gea_target.txt)
		+ [dr_gene_list.txt](./data/dr_gene_list.txt)
		+ [dr_gene_list_mut.txt](./data/dr_gene_list_mut.txt)
		+ [dr_mut_exp.txt](./data/dr_mut_exp.txt)
		+ [dr_mut_target.txt](./data/dr_mut_target.txt)
		+ [pam50.genes.txt](./data/pam50.genes.txt)
		+ [tcga_gea_target.txt](./data/tcga_gea_target.txt)
		+ [tcga_gene_list.txt](./data/tcga_gene_list.txt)
		+ [mentha.txt](http://bioinformatics.breastcancertissuebank.org:9003/bcntb_bioinformatics/src/mentha.txt)
		+ [tcga_gene_exp.csv](http://bioinformatics.breastcancertissuebank.org:9003/bcntb_bioinformatics/bcntb_backoffice/data/tcga/gene_exp.csv)
	+ [images](./images)
		+ [front_logo.svg](./images/front_logo.svg)
		+ [loading](./images/loading.svg)
		+ [net_legend.svg](./images/net_legend.svg)
			+ [icons](./images/icons)
				+ [breadcrumb-arrow.png](./images/icons/breadcrumb-arrow.png)
				+ [female.png](./images/icons/female.png)
				+ [male.png](./images/icons/male.png)
				+ [question_mark.png](./images/icons/question_mark.png)
				+ [statistics.png](./images/icons/statistics.png)
+ [js](./js)
	+ [candlestick.js](./js/candlestick.js)
	+ [ccle.js](./js/ccle.js)
	+ [data_return.js](./js/data_return.js)
	+ [jquery-ui.accordion.multiple.js](./js/jquery-ui.accordion.multiple.js)
	+ [jquery.dataTables.yadcf.js](./js/jquery.dataTables.yadcf.js)
	+ [jquery.scrollIntoView.js](./js/jquery.scrollIntoView.js)
	+ [network.js](./js/network.js)
	+ [sf.js](./js/sf.js)
	+ [tcga.js](./js/tcga.js)
	+ [tcga_analysis.js](./js/tcga_analysis.js)
+ [scripts](./scripts)
	+ [ExecQuery.php](./scripts/ExecQuery.php)
	+ [LaunchCommand.php](./scripts/LaunchCommand.php)
	+ [LoadCCLEAnalysis.php](./scripts/LoadCCLEAnalysis.php)
	+ [LoadDRAnalysis.php](./scripts/LoadDRAnalysis.php)
	+ [LoadStatisticalDetails.php](./scripts/LoadStatisticalDetails.php)
	+ [LoadTCGAAnalysis.php](./scripts/LoadTCGAAnalysis.php)
	+ [PHPMailer.php](./scripts/PHPMailer.php)
	+ [PerformRequest.php](./scripts/PerformRequest.php)
	+ [RetrieveCCLEDatasets.php](./scripts/RetrieveCCLEDatasets.php)
	+ [RetrieveDRDatasets.php](./scripts/RetrieveDRDatasets.php)
	+ [RetrieveGeneList.php](./scripts/RetrieveGeneList.php)
	+ [RetrievePublications.php](./scripts/RetrievePublications.php)
	+ [RetrieveTCGADatasets.php](./scripts/RetrieveTCGADatasets.php)
	+ [RetrieveTableFeatures.php](./scripts/RetrieveTableFeatures.php)
	+ [conn_details.php](./scripts/conn_details.php)
	+ [functions.php](./scripts/functions.php)
	+ [res_mixed.php](./scripts/res_mixed.php)
	+ [res_rnaseq.php](./scripts/RetrieveCCLEDatasets.php)
	+ [res_wgs.php](./scripts/res_wgs.php)
	+ [vars.php](./scripts/vars.php)
		+ [R](./script/R)
			+ [CreateExprMatrix.R](./scripts/R/CreateExprMatrix.R)
			+ [CreateMutMatrix.R](./scripts/R/CreateMutMatrix.R)
			+ [LiveCNAlterations.R](./scripts/R/LiveCNAlterations.R)
			+ [LiveCoExpression.R](./scripts/R/LiveCoExpression.R)
			+ [LiveExprCN.R](./scripts/R/LiveExprCN.R)
			+ [LiveExprCNMut.R](./scripts/R/LiveExprCNMut.R)
			+ [LiveGeneExpression.R](./scripts/R/LiveGeneExpression.R)
			+ [LiveMuts.R](./scripts/R/LiveMuts.R)
			+ [LiveNetworkCreator.R](./scripts/R/LiveNetworkCreator.R)
			+ [LiveOncoprint.R](./scripts/R/LiveOncoprint.R)
			+ [LiveSurvivalGene.R](./scripts/R/LiveSurvivalGene.R)
			+ [Mutations.R](./scripts/R/Mutations.R)
			+ [PCA.R](./scripts/R/PCA.R)
			+ [Survival.R](./scripts/R/Survival.R)
			+ [estimate.R](./scripts/R/estimate.R)
			+ [mclust.R](./scripts/R/mclust.R)
			+ [pam50.R](./scripts/R/pam50.R)
+ [styles](./styles)
	+ [candlestick.css](./styles/candlestick.css)
	+ [datatables_additional.css](./styles/datatables_additional.css)
	+ [dr.css](./styles/dr.css)
	+ [sf.css](./styles/sf.css)
	+ [tcga.css](./styles/tcga.css)
	+ [odometer-theme-train-station.css](./styles/odometer-theme-train-station.css)

## Main description of **PHP** files
For further details please refer to the comments inside the code

### Web elements

| File name | Description |
|----------|------------|
| index.php | This is the main home page. It includes 3 other pages (header, body, footer)|
| header.php | The header contains the html "head" section, with all the links to CSS files and JS plugins |
| body.php | The body page contains the logo, breadcrumb, and the main filtering section |
| data_returned.php | Analysis page for the data collected inside the BCNTB |
| tcga.php | Analysis page for The Cancer Genome Atlas data |
| ccle.php | Analysis page for data extracted from the Cancer Cell Line Enciclopedia |
| request.php | Web page where interested users can proceed to request selected samples |
| scrips/res_mixed.php | Page of analyses if returned data are from mixed technologies (wgs and rna-seq)|
| scrips/res_rnaseq.php | Page of analyses if returned data are from RNA-seq technology |
| scrips/res_wgs.php | Page of analyses if returned data are from WGS technology |

### Scripts and functions 

| File name | Description |
|----------|------------|
| ExecQuery.php | This is the most important script. Based on what selected, it performs multiple queries to the BCNTB database and retrieve the results |
| RetrieveCCLEDatasets.php | Retrieve the samples from CCLE, according to the features selected in the main page |
| RetrieveDRDatasets.php | Retrieve the samples from returned data, according to the features selected in the main page |
| RetrieveTCGADatasets.php | Retrieve the samples from TCGA, according to the features selected in the main page |
| RetrieveGeneList.php | Retrieve list of genes specific for each dataset (TCGA, CCLE, data returned) for the autocomplete search fields |
| conn_details.php | Page with all the connection (db) details. This page was planned to be encrypted at the final stage of production |
| vars.php | All the variables and useful function to be imported in each script |

## Main description of **JS** files

| File name | Description |
|----------|------------|
| candlestick.js | [Plugin for interactive buttons](https://github.com/EdouardTack/candlestick)|
| ccle.js | Methods for the analysed results of CCLE data |
| tcga.js | Methods for the analysed results of TCGA data |
| data_return.js | Methods for the analysed results of returned data |
| sf.js | Methods for animations and functions in the main webpage |
| network.js | Methods for visualising protein-protein interaction networks |
| jquery.scrollintoView.js | Auto scrolling once the plot is loaded |
| jquery.dataTables.yadcf.js | Look [here](https://github.com/vedmack/yadcf) for further details

## Main description of **CSS** files

| File name | Description |
|----------|------------|
| candlestick.css | [Plugin for interactive buttons](https://github.com/EdouardTack/candlestick)|
| sf.css | Style for elements of the main web page |
| tcga.css | Style for the page collecting analysed results of TCGA data |
| dr.css | Style for the page collecting analysed results of returned data |
| odometer-theme-train-station.css | Style for samples interactive, counter |

## Main description of **R** files

R scripts were developed for performing bioinformatics analysis on microarrays, RNA-seq and WGS data.
For further details on the available analyses, please refer to our [latest publication](https://www.ncbi.nlm.nih.gov/pmc/articles/PMC5753182/)

| File name | Description |
|----------|------------|
| CreateExprMatrix.R | Extracts the expression values for selected samples from TCGA or CCLE |
| CreateMutMatrix.R | Extracts mutation values for selected samples (if available) |
| LiveCNAlterations.R | Evaluates copy number alterations for selected genes and plot the results as an interactive heatmap |
| LiveCoExpression.R | Calculates the coexpression between user-defined genes/probes, for selected samples|
| LiveExprCN.R | Visualise copy number alterations (boxplots and barplots) across selected samples |
| LiveExprCNMut.R | Visualise copy number alterations across selected samples. It also integrates mutation profiles |
| LiveGeneExpression.R | Visualise gene expression levels for selected genes|
| LiveMuts.R | Visualise the mutation profile for TCGA data|
| LiveNetworkCreator.R | Visualise an interactive protein interaction network where nodes (genes) are colored according to the expression level in selected samples (average)|
| LiveOncoprint.R | Visualise the mutation profile (oncoprint) for selected genes, across multiple samples |
| LiveSurvivalGene.R | Visualise survival data (for SELECTED genes) as a KM plot. It also produce a JSON file to be imported as an interactive table|
| PCA.R| Performs PCA analysis using normalised expression data on selected samples. It visualise results as an interactive 3D scatterplot|
| Survival.R | Visualise survival data (for ALL genes across selected samples) as a KM plot |
| estimate.R | Evaluates tumour purity of selected samples |
| mclust.R | Identifies triple negative samples. It also assigns Er, PR and Her2 status according to the expression profile |
| pam50.R | Predicts PAM50 breast cancer molecular subtypes and visualise the aboundance as a barplot|

## Main description of **Data** files

These files contain dictionary for ID conversions, raw data from TCGA or CCLE, sources for conducting pam50 and gene network analyses

| File name | Description |
|----------|------------|
| SF2BiomartMapping.txt | Link between BCNTB literature service (Biomart) URLs and selectable feautures |
| ccle_cn.csv | Copy number data from Cancer Cell Line Encyclopedia |
| ccle_cn_target_with_chr.csv | Copy number data from Cancer Cell Line Encyclopedia (with chromosome info) |
| ccle_gea_target.txt | Clinical informations for CCLE samples |
| ccle_gene_exp.csv | Gene expression matrix for CCLE sample |
| ccle_gene_list.txt | List of unique genes listed in the "cle_gene_exp.csv" file |
| ccle_mut.csv | Mutations data from Cancer Cell Line Encyclopedia |
| dr_gea_target.txt | Clinical informations for "BCN returned" samples |
| dr_gene_exp.csv | Gene expression matrix for "BCN returned" samples |
| dr_gene_list.txt | List of unique genes listed in the "dr_gene_exp.csv" file |
| dr_mut_exp.txt | Mutations data for "BCN returned" samples
| dr_gene_list_mut.txt | List of unique genes listed in the "dr_mut_exp.txt" file |
| dr_mut_target.txt | Clinical informations for "BCN returned" samples (where mutation data are available) |
| mentha.txt | Protein-protein interaction data extracted from [Mentha](https://mentha.uniroma2.it) database |
| pam50.genes.txt | List of genes to be considered in the [PAM50](https://www.ncbi.nlm.nih.gov/pubmed/19204204) analysis |
| tcga_gea_target.txt | Clinical informations for TCGA samples |
| tcga_gene_exp.csv | Gene expression matrix for CCLE sample |
| tcga_gene_list.txt | List of unique genes listed in the "tcga_gene_exp.csv" file |

## Further annotations

The **queries** folder contains a tracklog of all the queries launched on the web portal. This represent a useful tool for creating access statistics and other analyses.

