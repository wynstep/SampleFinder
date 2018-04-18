<?php

// This page is the hub for visualising the type of results page, based on the type of selected samples

// importing variables file
include('scripts/vars.php'); // from this point it's possible to use the variables present inside 'var.php' file
// reading vars
//$ac = $_GET["ac"];
$analysis_type = $_GET["analysis_type"]; // this can be WGS (whole genome sequencing), RNA-seq or mixed

// importing variables
$iframe_directory = "$relative_root_dir/queries/data_return/";
$result_directory = "$absolute_root_dir/queries/data_return/";

echo <<< EOT
<html>
  <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
      <title>Data return results for SF2.0 -- Powered by BCNTB Analytics</title>
        <!-- CSS LINKS -->
        <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap4.min.css"/>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.3.1/css/buttons.dataTables.min.css"/>
          <!-- SELECT2 PLUGIN -->
          <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

        <!-- MAIN CSS -->
        <link rel="stylesheet" type="text/css" href="styles/dr.css">
        <link rel="stylesheet" type="text/css" href="styles/datatables_additional.css">

        <!-- JS LINKS -->
        <!-- Loading Jquery -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.15/js/jquery.dataTables.js"></script>

        <!-- SELECT2 PLUGIN -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.full.js"></script>

        <!-- PLUGINS FOR EXPORTING TABLE DATA -->
        <script src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/pdfmake.min.js"></script>
        <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>

        <!-- Loading personal scripts -->
        <script type="text/javascript" src="js/data_return.js"></script>
        <script type="text/javascript" src="js/network.js"></script>
  </head>

  <body>
EOT;

// filtering type of results based on the type of analysis
if ($analysis_type == "wgs") {
  include("scripts/res_wgs.php");
} elseif ($analysis_type=="mixed") {
  include("scripts/res_mixed.php");
} elseif ($analysis_type=="rnaseq") {
  include("scripts/res_rnaseq.php");
}

echo <<<EOT
    <!-- DIV for LOADING -->
    <div id="loading" class="hideable"></div>
    <!-- --------------- -->
  </body>
</html>
EOT;
?>
