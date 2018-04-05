<?php

/* Query builder for the Tissue Finder 2.0 *
 * Coder: Stefano Pirro'
 * Institution: Barts Cancer Institute
 * Details: PHP page for selecting the number of samples according to specific parameters */

// defining the parameters for database connection
include("conn_details.php");
// define custom functions
include("functions.php");

// initialise dictionary for returning data
$return_data = [];

// starting connection
$db_conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($db_conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {

  // adding parameters according to selected criteria (if not empty)
  $all_params = [];
  foreach($_POST as $name => $value) {
    $all_params[$name] = $value;
  }

  // initilizing variables
  $all_tables = [];
  $all_select = [];
  $all_select_in = [];
  $all_select_ex = [];
  $all_cases_ids = [];
  $all_sample_ids = [];
  $all_queries_c = [];
  $all_queries_s = [];
  // initilising type of therapy excluding filter
  $tot_ex_flag = 0;

  // trying to change strategy, a single (faster query for each selected parameter)
  foreach($_POST as $name => $value) {
    if (!empty($value) and $value!='null') { // checking if the variable is set
      // building the right filter according to the selected criteria
      switch ($name) {
        case 'gender':
          $table = "contact";
          $all_tables[] = $table;
          $select = "$table.SEX = ".$value."";
          $all_select[] = $select;
          break;
        case 'max_age':
          $table = "contact";
          $all_tables[] = $table;
          $select = "$table.AGE <= $value";
          $all_select[] = $select;
          break;
        case 'min_age':
          $table = "contact";
          $all_tables[] = $table;
          $select = "$table.AGE >= $value";
          $all_select[] = $select;
          break;
        case 'surv_state':
          $table = "contact";
          $all_tables[] = $table;
          $select = "$table.SURVIVALSTATUS = ".$value."";
          $all_select[] = $select;
          break;
        case 'fam_hist':
          $table = "contact";
          $all_tables[] = $table;
          $select = "$table.FAMILYHISTORY = ".$value."";
          $all_select[] = $select;
          break;
        case 'eth_group':
          $table = "contact";
          $all_tables[] = $table;
          $value = explode(",",$value);
          $select = [];
          foreach ($value as $v) {
            $select[] = "$table.ETHNICGROUP=$v";
          }
          $select = implode(" OR ", $select);
          $all_select[] = "(".$select.")";
          break;
        case 'her2':
          $table = "diseasebreastcancerepisode";
          $all_tables[] = $table;
          $select = "$table.HER2 = ".$value."";
          $all_select[] = $select;
          break;
        case 'er':
          $table = "diseasebreastcancerepisode";
          $all_tables[] = $table;
          $select = "$table.ERSTATUS =".$value."";
          $all_select[] = $select;
          break;
        case 'pr':
          $table = "diseasebreastcancerepisode";
          $all_tables[] = $table;
          $select = "$table.PRSTATUS =".$value."";
          $all_select[] = $select;
          break;
        case 'men_status':
          $table = "diseasebreastcancerepisode";
          $all_tables[] = $table;
          $value = explode(",",$value);
          $select = [];
          foreach ($value as $v) {
            $select[] = "$table.MENOPAUSALSTATUS=$v";
          }
          $select = implode(" OR ", $select);
          $all_select[] = "(".$select.")";
          break;
        case 'stage':
          $table = "diseasebreastcancerepisode";
          $all_tables[] = $table;
          $value = explode(",",$value);
          $select = [];
          foreach ($value as $v) {
            $select[] = "$table.STAGE=$v";
          }
          $select = implode(" OR ", $select);
          $all_select[] = "(".$select.")";
          break;
        case 'grade':
          $table = "diseasebreastcancerepisode";
          $all_tables[] = $table;
          $value = explode(",",$value);
          $select = [];
          foreach ($value as $v) {
            $select[] = "$table.GRADE=$v";
          }
          $select = implode(" OR ", $select);
          $all_select[] = "(".$select.")";
          break;
        case 'tot_in': //tot = type of therapy
          $table = "therapy";
          $all_tables[] = $table;
          $value = explode(",",$value);
          $select = [];
          foreach ($value as $v) {
            $select[] = "$table.TYPE=$v";
          }
          if ($all_params["tot_in_matched"] == 1) {
            $select = implode(" AND ", $select);
          } else {
            $select = implode(" OR ", $select);
          }
          $all_select[] = "(".$select.")";
          break;
        case 'tot_ex': //tot = type of therapy
          $table = "therapy";
          $all_tables[] = $table;
          $value = explode(",",$value);
          $select = [];
          $select_ex = [];
          foreach ($value as $v) {
            $select[] = "$table.TYPE=$v";
            $select_ex[] = "$table.TYPE!=$v";
          }
          if ($all_params["tot_ex_matched"] == 1) {
            $select = implode(" AND ", $select);
            $select_ex = implode(" AND ", $select_ex);
          } else {
            $select = implode(" OR ", $select);
            $select_ex = implode(" OR ", $select_ex);
          }
          $all_select_in[] = "(".$select.")";
          $all_select_ex[] = "(".$select_ex.")";
          // swith on tot_ex flag for launching a different query!!!
          $tot_ex_flag = 1;
          break;
        case 'stype': //tot = type of therapy
          $table = "sample";
          $all_tables[] = $table;
          $value = explode(",",$value);
          $select = [];
          foreach ($value as $v) {
            $select[] = "$table.SAMPLETYPE=$v";
          }
          if ($all_params["stype_matched"] == 1) {
            $select = implode(" AND ", $select);
          } else {
            $select = implode(" OR ", $select);
          }
          $all_select[] = "(".$select.")";
          break;
        case 'ttype': //tot = type of therapy
          $table = "sample";
          $all_tables[] = $table;
          $value = explode(",",$value);
          $select = [];
          foreach ($value as $v) {
            $select[] = "$table.TISSUETYPE=$v";
          }
          if ($all_params["ttype_matched"] == 1) {
            $select = implode(" AND ", $select);
          } else {
            $select = implode(" OR ", $select);
          }
          $all_select[] = "(".$select.")";
          break;
        case 'ctype':
          $table = "diseasebreastcancerepisode";
          $all_tables[] = $table;
          $value = explode(",",$value);
          $select = [];
          foreach ($value as $v) {
            $select[] = "$table.TYPEOFTUMOR=$v";
          }
          if ($all_params["ctype_matched"] == 1) {
            $select = implode(" AND ", $select);
          } else {
            $select = implode(" OR ", $select);
          }
          $all_select[] = "(".$select.")";
          break;
        default:
          break;
      }

      // building query from sample
      if ($table != "sample") {
        $s_query = "SELECT SAMPLENO FROM sample LEFT JOIN $table ON sample.CONTACTNO = ".$table.".CONTACTNO WHERE $select";
      } else {
        $s_query = "SELECT SAMPLENO FROM sample WHERE $select";
      }

      $samples = mysqli_query($db_conn, $s_query);

      // taking notes of the queries
      array_push($all_queries_s, $s_query);

      // retrieving ids of samples
      $tmp_samples_ids = [];
      while ($row = mysqli_fetch_assoc($samples)) {
        array_push($tmp_samples_ids, $row["SAMPLENO"]);
      }

      $all_sample_ids[] = $tmp_samples_ids;
    }
  }

  // performing query for cases
  // creating crosslink interactions
  $all_tables = array_unique($all_tables);
  $cross_link = crossLink($all_tables);
  $cross_link = array_map(function($couple) {
    $couples_string = [];
    if (count($couple) == 2) {
      for ($i=0;$i<count($couple);$i++) {
        $couples_string[] = "".$couple[$i].".CONTACTNO";
      }
    }
    return implode("=", $couples_string);
  }, $cross_link);
  $cross_link = implode(" AND ", array_filter(array_unique($cross_link)));

  // different queries if one or multiple tables selected
  if (count($all_tables) == 1) {
    $c_query = "SELECT DISTINCT contact.CONTACTNO contact FROM ".implode(",",array_unique($all_tables))." WHERE ".implode(" AND ",$all_select)."";
  } else {
    if ($tot_ex_flag == 1) { //if the user selected a specific exluding type of therapy
      $c_query = "SELECT DISTINCT contact.CONTACTNO contact FROM ".implode(",",array_unique($all_tables))." WHERE ".implode(" AND ",$all_select_in)." AND ".implode(" AND ",$all_select)." AND ".$cross_link."";
      $c_query_ex = "SELECT DISTINCT contact.CONTACTNO contact FROM ".implode(",",array_unique($all_tables))." WHERE ".implode(" AND ",$all_select)." AND ".implode(" AND ",$all_select_ex)." AND ".$cross_link."";
    } else {
      $c_query = "SELECT DISTINCT contact.CONTACTNO contact FROM ".implode(",",array_unique($all_tables))." WHERE ".implode(" AND ",$all_select)." AND ".$cross_link."";
    }
  }

  $all_cases_ids = [];
  $all_cases_ids_in = [];
  $all_cases_ids_ex = [];

  // performing queries
  if ($tot_ex_flag == 1) {
    $cases = mysqli_query($db_conn, $c_query);
    $cases_ex = mysqli_query($db_conn, $c_query_ex);
    // retrieving ids of cases including and excluding, then make the subtraction
    // filling all cases including type of therapy parameters
    while ($row = mysqli_fetch_assoc($cases)) {
      array_push($all_cases_ids_in, $row["contact"]);
    }
    // filling all cases excluding type of therapy parameters
    while ($row = mysqli_fetch_assoc($cases_ex)) {
      array_push($all_cases_ids_ex, $row["contact"]);
    }

    // calculating the difference among the arrays
    $all_cases_ids = array_diff($all_cases_ids_ex, $all_cases_ids_in);

  } else {

    $cases = mysqli_query($db_conn, $c_query);
    // retrieving ids of cases
    while ($row = mysqli_fetch_assoc($cases)) {
      array_push($all_cases_ids, $row["contact"]);
    }
  }

  // filling the return data array
  $return_data["count_cases"] = count($all_cases_ids);
  $return_data["count_samples"] = count(call_user_func_array('array_intersect', $all_sample_ids));
  $return_data["cases"] = $all_cases_ids;
  $return_data["queries_c"] = $c_query;
  $return_data["queries_c_ex"] = $c_query_ex;
  $return_data["queries_s"] = $all_queries_s;
  echo json_encode($return_data);
  exit;
}

?>
