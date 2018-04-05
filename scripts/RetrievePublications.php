<?php

/* Retrieve publication lists from BCNTB miner *
 * Coder: Stefano Pirro'
 * Institution: Barts Cancer Institute */

// using the biomart service to retrieve the list of publications and relative fields
/* How it works. For each selected criteria, the system performs a traslation into the SOAP format
  and queries the BCNTB:miner for retrieving the total number of papers */

//error_reporting(E_ALL);
//ini_set('display_errors', 'on');

include('functions.php');

// generate random code for queries
$rc = substr(md5(uniqid(mt_rand(), true)) , 0, 8);

// initilising total url for launching BCNTB miner
$url_string = [];
$url_string[] = "http://bioinformatics.breastcancertissuebank.org/martwizard/#!/import?mart=hsapiens_gene_breastCancer_config&step=3";

// loading BIOMART and URL features categories present
$biomart_dic = [];
$url_dic = [];
$mapping_file = fopen('../data/SF2BiomartMapping.txt','r');
$url_file = fopen('../data/SF2MinerMapping.txt','r');

// count basic papers
$basic_num_papers = count(LaunchSOAPQuery($rc, ""));
$init_flag = 0;

while(!feof($mapping_file)) {
  // splitting line by tab
  $fields = explode("\t", fgets($mapping_file));
  // population biomart dictionary. Key: fname@value  Value: soap query
  $biomart_dic["$fields[0]@'$fields[3]'"]=$fields[4];
}
// close connection to the file
fclose($mapping_file);

while(!feof($url_file)) {
  // splitting line by tab
  $fields = explode("\t", fgets($url_file));
  // population biomart dictionary. Key: fname@value  Value: url
  $url_dic["$fields[0]@'$fields[3]'"]=$fields[4];
}
// close connection to the file
fclose($url_file);

// for each posted criteria of selection, we try to map the SOAP query
$tmp_all_papers = [];
foreach($_POST as $name => $value) {
  // check if the variable is set
  if (!empty($value) and $value!='null') {
    // exploding multiple values (separated by comma)
    $value = explode(",",$value);
    foreach ($value as &$v) {
      // search for corrensponding soap query
      $s_key = "$name@$v";
      if (array_key_exists($s_key, $biomart_dic)) {
        // performing queries
        $tmp_all_papers[$name][] = LaunchSOAPQuery($rc, $biomart_dic[$s_key]);
        $url_string[] = $url_dic[$s_key];
      } else {
        $init_flag += 1;
      }
    }
  }
}

// applying manipulations based on the selected feature
$all_papers = [];
foreach($tmp_all_papers as $name => $value) {
  foreach ($value as &$v) {
    switch ($name) {
      case 'gender':
        $all_papers[$name] = $v;
        break;
      case 'max_age':
        break;
      case 'min_age':
        break;
      case 'surv_state':
        echo json_encode(NullPubsData());
        exit;
      case 'fam_hist':
        echo json_encode(NullPubsData());
        exit;
      case 'eth_group':
        echo json_encode(NullPubsData());
        exit;
      case 'her2':
        $all_papers[$name] = $v;
        break;
      case 'er':
        $all_papers[$name] = $v;
        break;
      case 'pr':
        $all_papers[$name] = $v;
        break;
      case 'men_status':
        $all_papers[$name] = array_unique(call_user_func_array('array_merge', $value));
        break;
      case 'stage':
        echo json_encode(NullPubsData());
        exit;
      case 'grade':
        $all_papers[$name] = array_unique(call_user_func_array('array_merge', $value));
        break;
      case 'stype':
        if ($all_params["stype_matched"] == 1) { // if the user selected the matched options
          $all_papers[$name] = array_unique(call_user_func_array('array_intersect', $value));
        } else {
          $all_papers[$name] = array_unique(call_user_func_array('array_merge', $value));
        }
        break;
      case 'ttype':
        if ($all_params["ttype_matched"] == 1) { // if the user selected the matched options
          $all_papers[$name] = array_unique(call_user_func_array('array_intersect', $value));
        } else {
          $all_papers[$name] = array_unique(call_user_func_array('array_merge', $value));
        }
        break;
      case 'ctype':
        if ($all_params["ctype_matched"] == 1) { // if the user selected the matched options
          $all_papers[$name] = array_values(array_unique(call_user_func_array('array_intersect', $value)));
        } else {
          $all_papers[$name] = array_unique(call_user_func_array('array_merge', $value));
        }
        break;
      case 'tot_in': // type of therapy (including)
        echo json_encode(NullPubsData());
        exit;
      case 'tot_ex': // type of therapy (excluding)
        echo json_encode(NullPubsData());
        exit;
    }
  }
}

if (count($all_papers) > 1) {
  $all_papers = call_user_func_array('array_intersect', $all_papers);
} else {
  $all_papers = array_unique(array_flatten_recursive($all_papers));
}

// filling the return data array
if (count($all_papers)-1 < 0 && $init_flag == 2) {
  $return_data["count_pubs"] = $basic_num_papers;
} else {
  $return_data["count_pubs"] = count($all_papers);
}

$return_data["pubs"] = array_values($all_papers);
$return_data["url"] = implode("",$url_string);
$return_data["flag"] = $init_flag;
echo json_encode($return_data);
exit;

?>
