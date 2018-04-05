<?php

/* Perform TCGA analyses on the selected datasets *
 * Coder: Stefano Pirro'
 * Institution: Barts Cancer Institute */

error_reporting(E_ALL);
include('vars.php'); // from this point it's possible to use the variables present inside 'var.php' file

// retrieving posted variables
// analysis code
$ac = $_POST["ac"];
// ccle codes
$ccle_codes = explode(",",$_POST["ccle"]);

// writing ccle codes into txt file
$ccle_codes_string = implode("\n", $ccle_codes);
$ccle_codes_file = "../queries/ccle/tmp".$ac.".codes.txt";
file_put_contents($ccle_codes_file, $ccle_codes_string);

// creating target file for selected ccle codes
$ccle_target_file = "../queries/ccle/tmp".$ac.".target.txt";
system ("head -n 1 ../data/ccle_gea_target.txt > $ccle_target_file"); // here we copy the intest
system ("for i in `cat $ccle_codes_file`; do grep \$i ../data/ccle_gea_target.txt >> $ccle_target_file; done");

// create expression matrix based on the selected tcga codes
$ccle_expression_file = "../data/ccle_gene_exp.csv";
$ccle_filtered_exp_file = "../queries/ccle/tmp".$ac.".expr.txt";
system ("Rscript R/CreateExprMatrix.R --exp_file $ccle_expression_file --target $ccle_target_file --outfile $ccle_filtered_exp_file");

// upload PCA analysis
$pca_outfile = $absolute_root_dir."/queries/ccle/".$ac."";
exec("Rscript R/PCA.R --exp_file $ccle_filtered_exp_file --target $ccle_target_file --colouring MOLECULAR_SUBTYPE -p 1 --outfile $pca_outfile");

// create survival analysis
$survival_outfile = $absolute_root_dir."/queries/tcga/".$ac."";
exec("Rscript R/Survival.R --target $tcga_target_file --outfile $survival_outfile");

?>
