<?php

// importing variables file
include('vars.php'); // from this point it's possible to use the variables present inside 'vars.php' file
include('functions.php');

// initialising vars called from ajax
$TypeAnalysis = $_GET["TypeAnalysis"];
$genes = $_GET["Genes"];
$unique_id = $_GET["rc"];

// initialising values just for gene networks analysis
$min_thr = $_GET["min_thr"];
$max_thr = $_GET["max_thr"];

if (strpos($TypeAnalysis, 'tcga') !== false) {
  // *** File variables *** //
  $expr_file = "$absolute_root_dir/queries/tcga/tmp".$unique_id.".expr.txt";
  $target_file = "$absolute_root_dir/queries/tcga/tmp".$unique_id.".target.txt";
  $tmp_dir = "$absolute_root_dir/queries/tcga/";
  $genes_file = "$absolute_root_dir/data/tcga_gene_list.txt";
} else if (strpos($TypeAnalysis, 'ccle') !== false){
  // *** File variables *** //
  $expr_file = "$absolute_root_dir/queries/ccle/tmp".$unique_id.".expr.txt";
  $target_file = "$absolute_root_dir/queries/ccle/tmp".$unique_id.".target.txt";
  $tmp_dir = "$absolute_root_dir/queries/ccle/";
  $genes_file = "$absolute_root_dir/data/ccle_gene_list.txt";
} else if (strpos($TypeAnalysis, 'dr') !== false){
  // *** File variables *** //
  $expr_file = "$absolute_root_dir/queries/data_return/tmp".$unique_id.".expr.txt";
  $target_file = "$absolute_root_dir/queries/data_return/tmp".$unique_id.".target.txt";
  $tmp_dir = "$absolute_root_dir/queries/data_return/";
  $genes_file = "$absolute_root_dir/data/dr_gene_list.txt";
}

$net_file = "$absolute_root_dir/data/mentha.txt";

if ($TypeAnalysis == "tcga_gene_expression" || $TypeAnalysis == "ccle_gene_expression" || $TypeAnalysis == "dr_gene_expression") {
  // *** Gene Expression Analyses *** //
  // launching Rscript for the analysis...
  echo "Rscript R/LiveGeneExpression.R --exp_file $expr_file --target $target_file --colouring \"MOLECULAR_SUBTYPE\" --gene \"$genes\" --dir $tmp_dir --hexcode \"$unique_id\" --type \"$TypeAnalysis\"";
  system("Rscript R/LiveGeneExpression.R --exp_file $expr_file --target $target_file --colouring \"MOLECULAR_SUBTYPE\" --gene \"$genes\" --dir $tmp_dir --hexcode \"$unique_id\" --type \"$TypeAnalysis\" 2>&1", $output);
} elseif ($TypeAnalysis == "tcga_survival" || $TypeAnalysis == "dr_survival") {
  // *** Survival Analyses *** //
  // launching Rscript for the analysis...
  system("Rscript R/LiveSurvivalGene.R --exp_file $expr_file --target $target_file --gene \"$genes\" --dir $tmp_dir --hexcode \"$unique_id\" --type \"$TypeAnalysis\" 2>&1", $output);
} elseif ($TypeAnalysis == "tcga_co_expression" || $TypeAnalysis == "ccle_co_expression" || $TypeAnalysis == "dr_co_expression") {
  // *** Co-expression Analyses *** //

  // here we load an array with all the gene names inside the expression files
  $global_gene_list = array();
  $global_gene_list[] = retrieveGeneList($genes_file);
  // uniquing and flatting the global genes list
  $global_gene_list = array_unique(array_flatten_recursive($global_gene_list));

  // here we intersect gene names (uploaded) with the ones available in the analysis
  $genes = explode(",", $genes);
  $right_genes = array_intersect($genes, $global_gene_list);
  if (sizeof($right_genes) > 2) {
    $right_genes = implode(",", array_intersect($genes, $global_gene_list));
    // launching Rscript for the analysis...
    //echo "Rscript R/LiveCoExpression.R --exp_file $expr_file --target $target_file --genes \"$right_genes\" --dir $tmp_dir --hexcode \"$unique_id\"";
    system("Rscript R/LiveCoExpression.R --exp_file $expr_file --target $target_file --genes \"$right_genes\" --dir $tmp_dir --hexcode \"$unique_id\" --type \"$TypeAnalysis\" 2>&1", $output);
  } else {
    die(header("HTTP/1.0 404 Not Found"));
  }
} elseif ($TypeAnalysis == "tcga_gene_network" || $TypeAnalysis == "ccle_gene_network" || $TypeAnalysis == "dr_gene_network") {
  // *** Gene Network analysis *** //
  // launching Rscript for the analysis...
  //echo "Rscript R/LiveNetworkCreator.R --net_file $net_file --exp_file $expr_file --target $target_file --genes \"$genes\" --dir $tmp_dir --hexcode \"$unique_id\" --min_thr $min_thr --max_thr $max_thr --type $TypeAnalysis 2>&1";
  system("Rscript R/LiveNetworkCreator.R --net_file $net_file --exp_file $expr_file --target $target_file --genes \"$genes\" --dir $tmp_dir --hexcode \"$unique_id\" --min_thr $min_thr --max_thr $max_thr --type $TypeAnalysis 2>&1", $output);
} elseif ($TypeAnalysis == "ccle_expression_layering") {
  // *** Expression Layering Analyses *** //
  // launching Rscript for the analysis...
  $cn_file = "$absolute_root_dir/data/ccle_cn.csv";
  $mut_file = "$absolute_root_dir/data/ccle_mut.csv";
  system("Rscript R/LiveExprCN.R --exp_file $expr_file --cn_file $cn_file --target $target_file --colouring \"MOLECULAR_SUBTYPE\" --gene \"$genes\" --dir $tmp_dir --hexcode \"$unique_id\" 2>&1", $output);
  system("Rscript R/LiveExprCNMut.R --exp_file $expr_file --cn_file $cn_file --mut_file $mut_file --target $target_file --gene \"$genes\" --dir $tmp_dir --hexcode \"$unique_id\" 2>&1", $output);
} elseif ($TypeAnalysis == "ccle_cn_alterations") {
  // *** CN alteration Analyses *** //
  // launching Rscript for the analysis...
  $cn_file = "$absolute_root_dir/data/ccle_cn_target_with_chr.csv";
  //echo "Rscript R/LiveCNAlterations.R --cn_file $cn_file --cn_file $cn_file --target $target_file --colouring \"MOLECULAR_SUBTYPE\" --gene \"$genes\" --dir $tmp_dir --hexcode \"$unique_id\" 2>&1";
  system("Rscript R/LiveCNAlterations.R --cn_file $cn_file --cn_file $cn_file --target $target_file --colouring \"MOLECULAR_SUBTYPE\" --gene \"$genes\" --dir $tmp_dir --hexcode \"$unique_id\" 2>&1", $output);
} elseif ($TypeAnalysis == "dr_mutations") {
  /* Oncoprint analysis */
  $mutation_file = "$absolute_root_dir/queries/data_return/tmp".$unique_id.".mut.txt";
  $outfile = "$absolute_root_dir/queries/data_return/$unique_id";
  system("Rscript R/LiveMuts.R --mut_file $mutation_file --target $target_file --gene \"$genes\" --outfile $outfile 2>&1", $output);
  // converting produced pdf file into high-res png
  system("convert -geometry 3600x3600 -density 300x300 -quality 100 $outfile.oncoprint_hm.pdf $outfile.oncoprint_hm.png");
}
