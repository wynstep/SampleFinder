<?php

/* Query builder for the Tissue Finder 2.0 *
 * Coder: Stefano Pirro'
 * Institution: Barts Cancer Institute
 * Details: PHP page for selecting the number of samples according to specific parameters */

//error_reporting(E_ALL);
//ini_set('display_errors', 'on');
// define custom functions
include("functions.php");

// initialise dictionary for returning data
$return_data = [];

// UPDATE FOR THE WAY AS QUERIES ARE EXECUTED: FOR each POSTED variable, a single query is launched.

// initilising variables
$all_cases = [];
$all_samples = [];
$tmp_cases_matched = [];
$tmp_cases_all = [];
$tmp_samples_matched = [];
$tmp_samples_all = [];

// adding all parameters to an assoctive array
$all_params = [];
foreach($_POST as $name => $value) {
  $all_params[$name] = $value;
}

// iterating into the associative array for parsing the parameters
foreach ($all_params as $pname => $pvalue) {
  $matched_flag = 0;
  // checking if the variable has a proper value
  if (!empty($pvalue) and $pvalue!='null') {
    // splitting pvalue according to comma and iterating
    $pvalue_array = explode(",",$pvalue);
    foreach ($pvalue_array as &$pv) {
      // checking the parameter name for building the right query
      switch ($pname) {
        case 'gender':
          $c_query = "SELECT CONTACTNO FROM contact WHERE SEX = $pv";
          $s_query = "SELECT sample.CONTACTNO, SAMPLENO FROM sample LEFT JOIN contact ON sample.CONTACTNO = contact.CONTACTNO WHERE contact.SEX = $pv";
          break;
        case 'max_age':
          $c_query = "SELECT CONTACTNO FROM contact WHERE AGE <= $pv";
          $s_query = "SELECT sample.CONTACTNO FROM sample LEFT JOIN contact ON sample.CONTACTNO = contact.CONTACTNO WHERE contact.AGE <= $pv";
          break;
        case 'min_age':
          $c_query = "SELECT CONTACTNO FROM contact WHERE AGE >= $pv";
          $s_query = "SELECT sample.CONTACTNO FROM sample LEFT JOIN contact ON sample.CONTACTNO = contact.CONTACTNO WHERE contact.AGE >= $pv";
          break;
        case 'surv_state':
          $c_query = "SELECT CONTACTNO FROM contact WHERE SURVIVALSTATUS = $pv";
          $s_query = "SELECT sample.CONTACTNO FROM sample LEFT JOIN contact ON sample.CONTACTNO = contact.CONTACTNO WHERE contact.SURVIVALSTATUS = $pv";
          break;
        case 'fam_hist':
          $c_query = "SELECT CONTACTNO FROM contact WHERE FAMILYHISTORY = $pv";
          $s_query = "SELECT sample.CONTACTNO FROM sample LEFT JOIN contact ON sample.CONTACTNO = contact.CONTACTNO WHERE contact.FAMILYHISTORY = $pv";
          break;
        case 'eth_group':
          $c_query = "SELECT CONTACTNO FROM contact WHERE ETHNICGROUP IN ($pvalue)";
          $s_query = "SELECT sample.CONTACTNO FROM sample LEFT JOIN contact ON sample.CONTACTNO = contact.CONTACTNO WHERE contact.ETHNICGROUP IN ($pvalue)";
          break;
        case 'her2':
          $c_query = "SELECT CONTACTNO FROM diseasebreastcancerepisode WHERE HER2 = $pv";
          $s_query = "SELECT sample.CONTACTNO FROM sample LEFT JOIN diseasebreastcancerepisode ON sample.CONTACTNO = diseasebreastcancerepisode.CONTACTNO WHERE diseasebreastcancerepisode.HER2 = $pv";
          break;
        case 'er':
          $c_query = "SELECT CONTACTNO FROM diseasebreastcancerepisode WHERE ERSTATUS = $pv";
          $s_query = "SELECT sample.CONTACTNO FROM sample LEFT JOIN diseasebreastcancerepisode ON sample.CONTACTNO = diseasebreastcancerepisode.CONTACTNO WHERE diseasebreastcancerepisode.ERSTATUS = $pv";
          break;
        case 'pr':
          $c_query = "SELECT CONTACTNO FROM diseasebreastcancerepisode WHERE PRSTATUS = $pv";
          $s_query = "SELECT sample.CONTACTNO FROM sample LEFT JOIN diseasebreastcancerepisode ON sample.CONTACTNO = diseasebreastcancerepisode.CONTACTNO WHERE diseasebreastcancerepisode.PRSTATUS = $pv";
          break;
        case 'men_status':
          $c_query = "SELECT CONTACTNO FROM diseasebreastcancerepisode WHERE MENOPAUSALSTATUS IN ($pvalue)";
          $s_query = "SELECT sample.CONTACTNO FROM sample LEFT JOIN diseasebreastcancerepisode ON sample.CONTACTNO = diseasebreastcancerepisode.CONTACTNO WHERE diseasebreastcancerepisode.MENOPAUSALSTATUS IN ($pvalue)";
          break;
        case 'stage':
          $c_query = "SELECT CONTACTNO FROM diseasebreastcancerepisode WHERE STAGE IN ($pvalue)";
          $s_query = "SELECT sample.CONTACTNO FROM sample LEFT JOIN diseasebreastcancerepisode ON sample.CONTACTNO = diseasebreastcancerepisode.CONTACTNO WHERE diseasebreastcancerepisode.STAGE IN ($pvalue)";
          break;
        case 'grade':
          $c_query = "SELECT CONTACTNO FROM diseasebreastcancerepisode WHERE GRADE IN ($pvalue)";
          $s_query = "SELECT sample.CONTACTNO FROM sample LEFT JOIN diseasebreastcancerepisode ON sample.CONTACTNO = diseasebreastcancerepisode.CONTACTNO WHERE diseasebreastcancerepisode.GRADE IN ($pvalue)";
          break;
        case 'stype_matched':
          $matched_flag = 1;
          break;
        case 'stype':
          if ($all_params["stype_matched"] == 1) { // if the user selected the matched options
            // here we launch a query to mySQL database and we collect the intersection of results
            $c_query = "SELECT CONTACTNO FROM sample WHERE SAMPLETYPE = $pv";
            $s_query = "SELECT CONTACTNO, SAMPLENO FROM sample WHERE SAMPLETYPE = $pv";
            $tmp_cases_matched["stype"][] = MatchedQuery($c_query, "cases");
            $tmp_samples_matched["stype"][] = MatchedQuery($s_query, "samples");
            $matched_flag = 1;
          } else {
            $c_query = "SELECT CONTACTNO FROM sample WHERE SAMPLETYPE IN ($pvalue)";
            $s_query = "SELECT CONTACTNO, SAMPLENO FROM sample WHERE SAMPLETYPE IN ($pvalue)";
          }
          break;
        case 'ttype_matched':
          $matched_flag = 1;
          break;
        case 'ttype':
          if ($all_params["ttype_matched"] == 1) { // if the user selected the matched options
            // here we launch a query to mySQL database and we collect the intersection of results
            $c_query = "SELECT CONTACTNO FROM sample WHERE TISSUETYPE = $pv";
            $s_query = "SELECT CONTACTNO, SAMPLENO FROM sample WHERE TISSUETYPE = $pv";
            $tmp_cases_matched["ttype"][] = MatchedQuery($c_query, "cases");
            $tmp_samples_matched["ttype"][] = MatchedQuery($s_query, "samples");
            $matched_flag = 1;
          } else {
            $c_query = "SELECT CONTACTNO FROM sample WHERE TISSUETYPE IN ($pvalue)";
            $s_query = "SELECT CONTACTNO, SAMPLENO FROM sample WHERE TISSUETYPE IN ($pvalue)";
          }
          break;
        case 'ctype_matched':
          $matched_flag = 1;
          break;
        case 'ctype':
          if ($all_params["ctype_matched"] == 1) { // if the user selected the matched options
            // here we launch a query to mySQL database and we collect the intersection of results
            $c_query = "SELECT CONTACTNO FROM diseasebreastcancerepisode WHERE TYPEOFTUMOR = $pv";
            $s_query = "SELECT diseasebreastcancerepisode.CONTACTNO, SAMPLENO FROM sample LEFT JOIN diseasebreastcancerepisode ON sample.CONTACTNO = diseasebreastcancerepisode.CONTACTNO WHERE diseasebreastcancerepisode.TYPEOFTUMOR = $pv";
            $tmp_cases_matched["ctype"][] = MatchedQuery($c_query, "cases");
            $tmp_samples_matched["ctype"][] = MatchedQuery($s_query, "samples");
            $matched_flag = 1;
          } else {
            $c_query = "SELECT CONTACTNO FROM diseasebreastcancerepisode WHERE TYPEOFTUMOR IN ($pvalue)";
            $s_query = "SELECT diseasebreastcancerepisode.CONTACTNO, SAMPLENO FROM sample LEFT JOIN diseasebreastcancerepisode ON sample.CONTACTNO = diseasebreastcancerepisode.CONTACTNO WHERE diseasebreastcancerepisode.TYPEOFTUMOR IN ($pvalue)";
          }
          break;
        case 'tot_in_matched':
          $matched_flag = 1;
          break;
        case 'tot_in': // type of therapy (including)
          if ($all_params["tot_in_matched"] == 1) { // if the user selected the matched options
            // here we launch a query to mySQL database and we collect the intersection of results
            $c_query = "SELECT CONTACTNO FROM therapy WHERE TYPE = $pv";
            $s_query = "SELECT therapy.CONTACTNO as CONTACTNO, SAMPLENO FROM sample LEFT JOIN therapy ON sample.CONTACTNO = therapy.CONTACTNO WHERE therapy.TYPE = $pv";
            $tmp_cases_matched["tot_in"][] = MatchedQuery($c_query, "cases");
            $tmp_samples_matched["tot_in"][] = MatchedQuery($s_query, "samples");
            $matched_flag = 1;
          } else {
            $c_query = "SELECT CONTACTNO FROM therapy WHERE TYPE IN ($pvalue)";
            $s_query = "SELECT therapy.CONTACTNO as CONTACTNO, SAMPLENO FROM sample LEFT JOIN therapy ON sample.CONTACTNO = therapy.CONTACTNO WHERE therapy.TYPE IN ($pvalue)";
          }
          break;
        case 'tot_ex_matched':
          $matched_flag = 1;
          break;
        case 'tot_ex': // type of therapy (excluding)
          if ($all_params["tot_ex_matched"] == 1) { // if the user selected the matched options
            // here we launch a query to mySQL database and we collect the intersection of results
            $matched_tmp_query_in = "SELECT CONTACTNO FROM therapy WHERE TYPE = $pv";
            $matched_tmp_query_ex = "SELECT CONTACTNO FROM therapy WHERE TYPE != $pv";
            $matched_tmp_query_s_in = "SELECT therapy.CONTACTNO as CONTACTO, SAMPLENO FROM sample LEFT JOIN therapy ON sample.CONTACTNO = therapy.CONTACTNO WHERE therapy.TYPE = $pv";
            $matched_tmp_query_s_ex = "SELECT therapy.CONTACTNO as CONTACTNO, SAMPLENO FROM sample LEFT JOIN therapy ON sample.CONTACTNO = therapy.CONTACTNO WHERE therapy.TYPE != $pv";
            $matched_flag = 1;
          } else {
            $matched_tmp_query_in = "SELECT CONTACTNO FROM therapy WHERE TYPE IN ($pvalue)";
            $matched_tmp_query_ex = "SELECT CONTACTNO FROM therapy WHERE TYPE NOT IN ($pvalue)";
            $matched_tmp_query_s_in = "SELECT therapy.CONTACTNO as CONTACTNO, SAMPLENO FROM sample LEFT JOIN therapy ON sample.CONTACTNO = therapy.CONTACTNO WHERE therapy.TYPE IN ($pvalue)";
            $matched_tmp_query_s_ex = "SELECT therapy.CONTACTNO as CONTACTNO, SAMPLENO FROM sample LEFT JOIN therapy ON sample.CONTACTNO = therapy.CONTACTNO WHERE therapy.TYPE NOT IN ($pvalue)";
          }

          // the right result would be the subtraction among the results including the search parameter and those excluding
          $tmp_cases_matched_in = MatchedQuery($matched_tmp_query_in, "cases");
          $tmp_cases_matched_ex = MatchedQuery($matched_tmp_query_ex, "cases");
          $tmp_samples_matched_in = MatchedQuery($matched_tmp_query_s_in, "samples");
          $tmp_samples_matched_ex = MatchedQuery($matched_tmp_query_s_ex, "samples");

          // setting the difference
          $tmp_cases_matched["tot_ex"][] = array_diff($tmp_cases_matched_ex, $tmp_cases_matched_in);
          $tmp_samples_matched["tot_ex"][] = array_diff($tmp_samples_matched_ex, $tmp_samples_matched_in);
          //print_r($tmp_samples_matched["tot_ex"]);
          break;
      }

      // performing query and filling the global box of cases
      if ($matched_flag != 1) {
        $all_cases[] = MatchedQuery($c_query, "cases");
        $all_samples[] = MatchedQuery($s_query, "samples");
      }
    }
  }
}

/* COUNTING CASES */
foreach ($tmp_cases_matched as $tname => $tvalue) {
  if (count($tvalue) > 1) {
    $all_cases[] = call_user_func_array('array_intersect', $tvalue);
  } else {
    $all_cases[] = $tvalue[0];
  }
}

/* COUNTING SAMPLES */
//echo(count($tmp_samples_matched));
if (count($tmp_samples_matched) > 0) {
  foreach ($tmp_samples_matched as $tname => $tvalue) {
    if (count($tvalue) > 1) {
      $all_samples[] = call_user_func_array('array_intersect', $tvalue);
    } else {
      $all_samples[] = $tvalue[0];
    }
  }
}

// performing the intersect of the results (matched cases)
$all_cases = call_user_func_array('array_intersect', $all_cases);
$all_samples = call_user_func_array('array_intersect', $all_samples);
$return_data["count_cases"] = count($all_cases);
$return_data["count_samples"] = count($all_samples);
$return_data["cases"] = $all_cases;
$return_data["samples"] = $all_samples;

echo json_encode($return_data);
exit;

?>
