<?php

/* Query builder for the Tissue Finder 2.0 *
 * Coder: Stefano Pirro'
 * Institution: Barts Cancer Institute
 * Details: PHP page for extracting statistics of samples according to specific parameters */

// defining the parameters for database connection
include("conn_details.php");

// starting connection
$db_conn = new mysqli($servername, $username, $password, $dbname);

// getting posted variables
$cases_ids = $_POST["cases"];
$cases_ids_string = "'" . implode("','", $cases_ids) . "'";

// initialising array which will contain all the results
$cases_stats = [
  "AGE" => [],
  "SEX" => [],
  "SURVIVALSTATUS" => [],
  "FAMILYHISTORY" => [],
  "ETHNICGROUP" => [],
  "HER2" => [],
  "ERSTATUS" => [],
  "PRSTATUS" => [],
  "MENOPAUSALSTATUS" => [],
  "STAGE" => [],
  "GRADE" => [],
  "TYPE" => []
];



// Check connection
if ($db_conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {
  // filling the required statistical fields
  foreach ($cases_stats as $feature => $stat_content) {
    switch ($feature) {
      case 'AGE':
        $query = "SELECT AGE, COUNT(CONTACTNO) as COUNT FROM contact WHERE CONTACTNO IN ($cases_ids_string) GROUP BY AGE";
        break;
      case 'SEX':
        $query = "SELECT SEX, COUNT(CONTACTNO) as COUNT FROM contact WHERE CONTACTNO IN ($cases_ids_string) GROUP BY SEX";
        break;
      case 'SURVIVALSTATUS':
        $query = "SELECT SURVIVALSTATUS, COUNT(CONTACTNO) as COUNT FROM contact WHERE CONTACTNO IN ($cases_ids_string) GROUP BY SURVIVALSTATUS";
        break;
      case 'FAMILYHISTORY':
        $query = "SELECT FAMILYHISTORY, COUNT(CONTACTNO) as COUNT FROM contact WHERE CONTACTNO IN ($cases_ids_string) GROUP BY FAMILYHISTORY";
        break;
      case 'ETHNICGROUP':
        $query = "SELECT ETHNICGROUP, COUNT(CONTACTNO) as COUNT FROM contact WHERE CONTACTNO IN ($cases_ids_string) GROUP BY ETHNICGROUP";
        break;
      case 'HER2':
        $query = "SELECT HER2, COUNT(CONTACTNO) as COUNT FROM diseasebreastcancerepisode WHERE CONTACTNO IN ($cases_ids_string) GROUP BY HER2";
        break;
      case 'ERSTATUS':
        $query = "SELECT ERSTATUS, COUNT(CONTACTNO) as COUNT FROM diseasebreastcancerepisode WHERE CONTACTNO IN ($cases_ids_string) GROUP BY ERSTATUS";
        break;
      case 'PRSTATUS':
        $query = "SELECT PRSTATUS, COUNT(CONTACTNO) as COUNT FROM diseasebreastcancerepisode WHERE CONTACTNO IN ($cases_ids_string) GROUP BY PRSTATUS";
        break;
      case 'MENOPAUSALSTATUS':
        $query = "SELECT MENOPAUSALSTATUS, COUNT(CONTACTNO) as COUNT FROM diseasebreastcancerepisode d WHERE CONTACTNO IN ($cases_ids_string) GROUP BY MENOPAUSALSTATUS";
        break;
      case 'STAGE':
        $query = "SELECT STAGE, COUNT(CONTACTNO) as COUNT FROM diseasebreastcancerepisode WHERE CONTACTNO IN ($cases_ids_string) GROUP BY STAGE";
        break;
      case 'GRADE':
        $query = "SELECT GRADE, COUNT(CONTACTNO) as COUNT FROM diseasebreastcancerepisode WHERE CONTACTNO IN ($cases_ids_string) GROUP BY GRADE";
        break;
      case 'TYPE':
        $query = "SELECT TYPE, COUNT(CONTACTNO) as COUNT FROM therapy WHERE CONTACTNO IN ($cases_ids_string) GROUP BY TYPE";
        break;
    }

    // performing mySQL query
    $results_cases = mysqli_query($db_conn, $query);
    while($row = mysqli_fetch_assoc($results_cases)) {
      $cases_stats[$feature]["$row[$feature]"] = $row["COUNT"];
    }
  }

  // transforming statistics into json-comaptible format
  $json_cases_stats = [];
  foreach ($cases_stats as $class => $stat) {
    $temp_stat = [];
    foreach ($stat as $name => $y) {
      $temp_stat[] = [ "label" => $name, "y" => $y];
    }
    $json_cases_stats[$class] = $temp_stat;
  }

  // filling the return data array
  echo json_encode($json_cases_stats);
  mysqli_close($db_conn);
  exit;
}

?>
