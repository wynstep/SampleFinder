<?php

/* Retrieve TCGA datasets from BCNTB miner *
 * Coder: Stefano Pirro'
 * Institution: Barts Cancer Institute */

/* How it works. For each selected criteria, the system selects TCGA samples and performs analyses on these */

error_reporting(E_ALL);

// initialising list datasets
$list_datasets = array();

// for each posted criteria of selection, we try to map the SOAP query
foreach($_POST as $name => $value) {
  $value = explode(",",$value);
  $tmp_list_datasets = array();
  foreach ($value as &$v) {
    if ($v!='null') { // checking if the variable is set
      // open TCGA target file (with all the clinical information)
      $target_file = fopen('../data/tcga_gea_target.txt','r');
      while(!feof($target_file)) {
        $line = fgets($target_file);
        // splitting line by tab
        $fields = explode("\t", rtrim($line));
        // retrieving field values
        $fname = $fields[0];
        $tissue_type = "'".$fields[1]."'";
        $stage = "'".$fields[2]."'";
        $surv_status = "'".$fields[3]."'";
        $surv_time = $fields[4];
        $gender = "'".$fields[5]."'";
        $ethnic_group = $fields[6];
        $age = $fields[7];
        $er = "'".$fields[8]."'";
        $pr = "'".$fields[9]."'";
        $her2 = "'".$fields[10]."'";
        $subtype = "'".$fields[11]."'";

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
          case 'surv_state':
            if ($v == $surv_status) {
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
          case 'stage':
            if ($v == $stage) {
              array_push($tmp_list_datasets, $fname);
            }
            break;
          case 'grade':
            if ($v == $grade) {
              array_push($tmp_list_datasets, $fname);
            }
            break;
          case 'ttype':
            if ($v == $tissue_type) {
              array_push($tmp_list_datasets, $fname);
            }
            break;
          case 'molsubtype':
            if ($v == $subtype) {
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

// using an AND logical gate, so applying intersect on retrieved TCGA samples
// filling the return data array
if (count($list_datasets) == 1) {
  $return_data["count_tcga"] = count($list_datasets[0]);
  $return_data["tcga"] = $list_datasets[0];
} else {
  $return_data["count_tcga"] = count($inter_datasets);
  $return_data["tcga"] = array_values($inter_datasets);
}

echo json_encode($return_data);
exit;

?>
