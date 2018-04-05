<?php

/* Perform TCGA analyses on the selected datasets *
 * Coder: Stefano Pirro'
 * Institution: Barts Cancer Institute */

error_reporting(E_ALL);
include('vars.php'); // from this point it's possible to use the variables present inside 'var.php' file
include('functions.php');

// retrieving posted variables
// analysis code
$ac = $_POST["ac"];
// tcga codes
$dr_codes = explode(",",$_POST["dr"]);

// writing dr codes into txt file
$dr_codes_string = implode("\n", $dr_codes);
$dr_codes_file = "../queries/data_return/tmp".$ac.".codes.txt";
file_put_contents($dr_codes_file, $dr_codes_string);

// creating target file for selected dr codes
$dr_target_file = "../queries/data_return/tmp".$ac.".target.txt";
system ("head -n 1 ../data/dr_gea_target.txt > $dr_target_file"); // here we copy the intest
system ("for i in `cat $dr_codes_file`; do grep -w \$i ../data/dr_gea_target.txt >> $dr_target_file; done");

/* EDIT 2017/11/28 -- Understanding the type of technology (all wgs, all rna-seq, mixed) */
$dr_target_file_stream = fopen($dr_target_file, "r") or die("Unable to open temp target file");
fgets($dr_target_file_stream);
while (!feof($dr_target_file_stream)) {
  $technology = explode("\t", fgets($dr_target_file_stream))[16];
  $all_technologies[] = trim($technology);
}
fclose($dr_target_file_stream);
$all_technologies = array_filter(array_unique($all_technologies));

// create expression matrix based on the selected dr codes
$dr_expression_file = "../data/dr_gene_exp.csv";
$dr_filtered_exp_file = "../queries/data_return/tmp".$ac.".expr.txt";
system ("Rscript R/CreateExprMatrix.R --exp_file $dr_expression_file --target $dr_target_file --outfile $dr_filtered_exp_file");

// create mutation matrix based on the selected dr codes
$dr_mutation_file = "../data/dr_mut_exp.txt";
$dr_filtered_mut_file = "../queries/data_return/tmp".$ac.".mut.txt";
system ("Rscript R/CreateMutMatrix.R --mut_file $dr_mutation_file --target $dr_target_file --outfile $dr_filtered_mut_file");

// selecting the analysis based on the type of technologies
if (count($all_technologies) == 2) { // mixed
  RunStandardAnalyses($absolute_root_dir, $dr_target_file, $dr_filtered_exp_file, $ac);
  RunWGSAnalysis($absolute_root_dir, $dr_target_file, $dr_filtered_mut_file, $ac);
  $analysis_type = "mixed";
} elseif (count($all_technologies) == 1 && $all_technologies[0] == "wgs") { // just wgs samples
  //RunStandardAnalyses($absolute_root_dir, $dr_target_file, $dr_filtered_exp_file, $ac);
  RunWGSAnalysis($absolute_root_dir, $dr_target_file, $dr_filtered_mut_file, $ac);
  $analysis_type = $all_technologies[0];
} elseif (count($all_technologies) == 1 && $all_technologies[0] == "rnaseq") { // just wgs samples
  //RunStandardAnalyses($absolute_root_dir, $dr_target_file, $dr_filtered_exp_file, $ac);
  RunStandardAnalyses($absolute_root_dir, $dr_target_file, $dr_filtered_exp_file, $ac);
  $analysis_type = $all_technologies[0];
}

echo json_encode($analysis_type);
?>
