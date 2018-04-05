<?php

/* Retrieve TCGA datasets from BCNTB miner *
 * Coder: Stefano Pirro'
 * Institution: Barts Cancer Institute */

/* How it works. For each selected criteria, the system selects returned samples and performs analyses on these */

//error_reporting(E_ALL);

// initialising list datasets
$list_datasets = array();

foreach($_POST as $name => $value) {
  $value = explode(",",$value);
  $tmp_list_datasets = array();
  foreach ($value as &$v) {
    if ($v!='null') { // checking if the variable is set
      // open dr target file (with all the clinical information)
      $target_file = fopen('../data/dr_gea_target.txt','r');
      while(!feof($target_file)) {
        $line = fgets($target_file);
        // splitting line by tab
        $fields = explode("\t", rtrim($line));
        // retrieving field values
        $fname = $fields[0];
        $stage = "'".$fields[1]."'";
        $surv_status = "'".$fields[2]."'";
        $surv_time = $fields[3];
        $gender = "'".$fields[4]."'";
        $ethnic_group = "'".$fields[5]."'";
        $age = $fields[6];
        $menopausal_status = "'".$fields[7]."'";
        $er = "'".$fields[8]."'";
        $pr = "'".$fields[9]."'";
        $her2 = "'".$fields[10]."'";
        $subtype = "'".$fields[13]."'";
        $fam_hist = "'".$fields[14]."'";
        $technology = "'".$fields[16]."'";
        $p53_mut = "'".$fields[17]."'";

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
          case 'men_status':
            if ($v == $menopausal_status) {
              array_push($tmp_list_datasets, $fname);
            }
            break;
          case 'eth_group':
            if (strpos($v, $ethnic_group)) {
              array_push($tmp_list_datasets, $fname);
            }
            break;
          case 'fam_hist':
            if (strpos($v, $fam_hist)) {
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
          case 'technology':
            if ($v == $technology) {
              array_push($tmp_list_datasets, $fname);
            }
            break;
          case 'tp53_mut':
            if ($v == $p53_mut) {
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
  $return_data["count_dr"] = count($list_datasets[0]);
  $return_data["dr"] = $list_datasets[0];
} else {
  $return_data["count_dr"] = count($inter_datasets);
  $return_data["dr"] = array_values($inter_datasets);
}

echo json_encode($return_data);
exit;

?>
