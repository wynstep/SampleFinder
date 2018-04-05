<?php

// importing variables file
include('vars.php'); // from this point it's possible to use the variables present inside 'vars.php' file
include('functions.php');

$ae = $_GET["ae"];
$pmid = $_GET["pmid"];
$type_analysis = $_GET["type_analysis"];
$query = strtoupper($_GET["q"]); // query for the search field

// retrieving all the gene expression matrices inside the result directory

if ($type_analysis == "tcga") {
  $result_directory = "$absolute_root_dir/data/";
  chdir($result_directory);
  $expr_files = glob("tcga_gene_list.txt"); // in this case the "gene_exp.csv" file is too much big to manage
} else if ($type_analysis == "dr") {
  $result_directory = "$absolute_root_dir/data/";
  chdir($result_directory);
  $expr_files = glob("dr_gene_list.txt"); // in this case the "gene_exp.csv" file is too much big to manage
} else if ($type_analysis == "dr_mut") {
  $result_directory = "$absolute_root_dir/data/";
  chdir($result_directory);
  $expr_files = glob("dr_gene_list_mut.txt"); // in this case the "gene_exp.csv" file is too much big to manage
} else if ($type_analysis == "ccle") {
  $result_directory = "$absolute_root_dir/data/";
  chdir($result_directory);
  $expr_files = glob("ccle_gene_list.txt");
}

// putting all the found genes inside an array
$GeneContainer = array();
foreach ($expr_files as &$ef) {
  $GeneContainer[] = retrieveGeneList($ef);
}
// flatting array
$GeneContainer = array_unique(array_flatten_recursive($GeneContainer));

$data = array();
foreach ($GeneContainer as &$g) {
  if (strpos($g, $query) !== false) {
    $nestedData["id"] = $g;
    $nestedData["text"] = $g;

    $data[] = $nestedData;
  }
}

echo json_encode($data);
