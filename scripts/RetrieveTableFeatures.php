<?php

/* Retrieve table features for the Tissue Finder 2.0 *
 * Coder: Stefano Pirro'
 * Institution: Barts Cancer Institute */

// defining the parameters for database connection
include("conn_details.php");

$type = $_POST["type"];
//$query = strtoupper($_POST["q"]); // query for the search field

// starting connection
$db_conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($db_conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} else {
    // perform a query based on the type
    switch ($type) {
      case 'eth_group':
        $query = "SELECT DISTINCT ETHNICGROUP item FROM contact";
        break;
      case 'men_status':
        $query = "SELECT DISTINCT MENOPAUSALSTATUS item FROM diseasebreastcancerepisode";
        break;
      case 'stage':
        $query = "SELECT DISTINCT STAGE item FROM diseasebreastcancerepisode";
        break;
      case 'grade':
        $query = "SELECT DISTINCT GRADE item FROM diseasebreastcancerepisode";
        break;
      case 'tot_in':
        $query = "SELECT DISTINCT TYPE item FROM therapy";
        break;
      case 'tot_ex':
        $query = "SELECT DISTINCT TYPE item FROM therapy";
        break;
      case 'stype':
        $query = "SELECT DISTINCT SAMPLETYPE item FROM sample";
        break;
      case 'ttype':
        $query = "SELECT DISTINCT TISSUETYPE item FROM sample";
        break;
      case 'ctype':
        $query = "SELECT DISTINCT TYPEOFTUMOR item FROM diseasebreastcancerepisode";
        break;
    }

  // performing mySQL query
  $results = mysqli_query($db_conn, $query);

  // retrieving items
  $items = [];
  while ($row = mysqli_fetch_assoc($results)) {
    $nestedData["id"] = $row["item"];
    $nestedData["text"] = $row["item"];
    $items[] = $nestedData;
  }

  echo json_encode($items);
  exit;
}

?>
