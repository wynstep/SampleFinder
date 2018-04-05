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

## Main description of PHP files
For further details please refer to the comments inside the code

| File name | Description |
|----------|------------|
| index.php | bar      |


