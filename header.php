<?php
/* Header section for the Sample Finder 2.0 *
 * Coder: Stefano Pirro'
 * Institution: Barts Cancer Institute
 * Details: This header contains all the links to css styles and javascript functions */

// printing the whole HTML block code
echo <<< EOT
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>SF2.0 -- Sample Finder for BCNTB</title>

        <!-- CSS LINKS JQUERY AND DATATABLES-->
        <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap4.min.css"/>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.3.1/css/buttons.dataTables.min.css"/>
            <!-- SELECT2 PLUGIN -->
            <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
            <!-- ODOMETER PLUGIN -->
            <link href="styles/odometer-theme-train-station.css" rel="stylesheet" />
            <!-- SWITCH PLUGIN -->
            <link href="styles/candlestick.css" rel="stylesheet" />

        <!-- MAIN CSS -->
        <link rel="stylesheet" type="text/css" href="styles/sf.css">
        <link rel="stylesheet" type="text/css" href="styles/datatables_additional.css">

        <!-- JS LINKS -->
        <!-- Loading Jquery -->
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.15/js/jquery.dataTables.js"></script>

        <!-- SELECT2 PLUGIN -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

        <!-- ODOMETER PLUGIN -->
        <script src="http://github.hubspot.com/odometer/odometer.js"></script>

        <!-- SWITCH PLUGIN -->
        <script type="text/javascript" src="js/candlestick.js"></script>

        <!-- CANVASJS PLUGIN -->
        <script type="text/javascript" src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>

        <!-- Loading personal scripts -->
        <script type="text/javascript" src="js/sf.js"></script>
</head>

EOT;

?>
