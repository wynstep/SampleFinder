<?php

/* Perform TCGA analyses on the selected datasets *
 * Coder: Stefano Pirro'
 * Institution: Barts Cancer Institute */

error_reporting(E_ALL);
include('vars.php'); // from this point it's possible to use the variables present inside 'var.php' file

// retrieving posted variables
// analysis code
$ac = $_POST["ac"];
// tcga codes
$tcga_codes = explode(",",$_POST["tcga"]);

// writing tcga codes into txt file
$tcga_codes_string = implode("\n", $tcga_codes);
$tcga_codes_file = "../queries/tcga/tmp".$ac.".codes.txt";
file_put_contents($tcga_codes_file, $tcga_codes_string);

// creating target file for selected tcga codes
$tcga_target_file = "../queries/tcga/tmp".$ac.".target.txt";
system ("head -n 1 ../data/tcga_gea_target.txt > $tcga_target_file"); // here we copy the intest
system ("for i in `cat $tcga_codes_file`; do grep \$i ../data/tcga_gea_target.txt >> $tcga_target_file; done");

// create expression matrix based on the selected tcga codes
$tcga_expression_file = "../data/tcga_gene_exp.csv";
$tcga_filtered_exp_file = "../queries/tcga/tmp".$ac.".expr.txt";
system ("Rscript R/CreateExprMatrix.R --exp_file $tcga_expression_file --target $tcga_target_file --outfile $tcga_filtered_exp_file");

// upload PCA analysis
$pca_outfile = $absolute_root_dir."/queries/tcga/".$ac."";
exec("Rscript R/PCA.R --exp_file $tcga_filtered_exp_file --target $tcga_target_file --colouring MOLECULAR_SUBTYPE -p 1 --outfile $pca_outfile");

// create survival analysis
$survival_outfile = $absolute_root_dir."/queries/tcga/".$ac."";
exec("Rscript R/Survival.R --target $tcga_target_file --outfile $survival_outfile");

?>
