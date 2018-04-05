<?php

/* Retrieve TCGA datasets from BCNTB miner *
 * Coder: Stefano Pirro'
 * Institution: Barts Cancer Institute */

/* How it works. For each selected criteria, the system selects CCLE samples and performs analyses on these */

//error_reporting(E_ALL);
//ini_set('display_errors', 'on');

// initialising list datasets
$list_datasets = array();
$all_names = array();

// for each posted criteria of selection, we try to map the SOAP query
foreach($_POST as $name => $value) {
  $value = explode(",",$value);
  $tmp_list_datasets = array();
  foreach ($value as &$v) {
    if ($v !== 'null') { // checking if the variable is set
      // open CCLE target file (with all the clinical information)
      $target_file = fopen('../data/ccle_gea_target.txt','r');
      while(!feof($target_file)) {
        $line = fgets($target_file);
        // splitting line by tab
        $fields = explode("\t", rtrim($line));
        // retrieving field values
        $fname = $fields[0];
        $cancer_type = "'".$fields[2]."'";
        $gender = "'".$fields[6]."'";
        $ethnic_group = $fields[7];
        $age = $fields[8];
        $er = "'".$fields[9]."'";
        $pr = "'".$fields[10]."'";
        $her2 = "'".$fields[11]."'";

        switch ($name) {
          case 'gender':
            if ($gender == $v) {
              array_push($tmp_list_datasets, $fname);
            }
            break;
          case 'max_age':
            if ($age <= $v) {
              array_push($tmp_list_datasets, $fname);
            }
            break;
          case 'min_age':
            if ($age >= $v) {
              array_push($tmp_list_datasets, $fname);
            }
            break;
          case 'eth_group':
            if (strpos($v, $ethnic_group)) {
              array_push($tmp_list_datasets, $fname);
            }
            break;
          case 'her2':
            if ($v == $her2) {
              array_push($tmp_list_datasets, $fname);
            }
            break;
          case 'er':
            if ($v == $er) {
              array_push($tmp_list_datasets, $fname);
            }
            break;
          case 'pr':
            if ($v == $pr) {
              array_push($tmp_list_datasets, $fname);
            }
            break;
          case 'ctype':
            if ($v == $cancer_type) {
              array_push($tmp_list_datasets, $fname);
            }
            break;
          default:
            array_push($tmp_list_datasets, $fname);
            break;
        }
      }
    } else {
      $tmp_list_datasets =  "null";
    }
  }

  $list_datasets[] = $tmp_list_datasets;
  // removing null values, then evaluate if there are empty datasets (term not matched)
  $filtered_list_datasets = array();
  foreach ($list_datasets as &$ds) {
    if ($ds == "null") {
      $filtered_list_datasets[] = null; // putting empty instead
    } else {
      $filtered_list_datasets[] = $ds;
    }
  }
}

$list_datasets = array_filter($filtered_list_datasets, function($var){return !is_null($var);} );
$inter_datasets = call_user_func_array('array_intersect',$list_datasets);

// using an AND logical gate, so applying intersect on retrieved CCLE samples
// filling the return data array
if (count($list_datasets) == 1) {
  $return_data["count_ccle"] = count($list_datasets[0]);
  $return_data["ccle"] = $list_datasets[0];
} else {
  $return_data["count_ccle"] = count($inter_datasets);
  $return_data["ccle"] = array_values($inter_datasets);
}


echo json_encode($return_data);
exit;

?>
