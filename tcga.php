<?php

// importing variables file
include('scripts/vars.php'); // from this point it's possible to use the variables present inside 'var.php' file
// reading vars
$ac = $_GET["ac"];

// importing variables
$iframe_directory = "$relative_root_dir/queries/tcga/";
$result_directory = "$absolute_root_dir/queries/tcga/";

echo <<< EOT
<html>
  <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
      <title>TCGA Results for SF2.0 -- Powered by BCNTB Analytics</title>

          <!-- CSS LINKS -->
          <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
          <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.css">
          <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap4.min.css"/>
          <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.3.1/css/buttons.dataTables.min.css"/>
              <!-- SELECT2 PLUGIN -->
              <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

          <!-- MAIN CSS -->
          <link rel="stylesheet" type="text/css" href="styles/tcga.css">
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
          <script type="text/javascript" src="js/tcga.js"></script>
          <script type="text/javascript" src="js/network.js"></script>
  </head>

  <body>
    <!-- Results Section -->
    <div class="container" id="tcga_results">
      <ul>
        <li><a href="#pca">PCA</a></li>
        <li><a href="#expression_profiles">Gene expression</a></li>
        <li><a href="#co_expression_analysis">Correlations</a></li>
        <li><a href="#survival">Survival analysis</a></li>
        <li><a href="#gene_networks">Gene networks</a></li>
      </ul>

      <div id="pca">
        <div class='description'>
          <p class='pub_det'> Principal component analyses (PCA) transforms the data into a coordinate system and presenting it as an orthogonal projection.
              This reduces the dimensionality of the data, allowing for the global structure and key “components” of variation of the data to be viewed.
              Each point represents the orientation of a sample in the transcriptional space projected on the PCA,
              with different colours representing the biological group of the sample.
          </p>
        </div>

        <iframe class='results' scrolling='no' src='$iframe_directory/$ac.PCA3d.html'></iframe>
        <iframe class='results' scrolling='no' src='$iframe_directory/$ac.PCA2d.html'></iframe>
        <iframe class='results' scrolling='no' src='$iframe_directory/$ac.PCAbp.html'></iframe>

      </div>
      <div id="expression_profiles">
        <div class='description'>
          <p class='pub_det'>
            The expression profile of selected gene(s) across comparative groups are presented as both summarised and a
            sample views (boxplots and barplots, respectively).
          </p>
          <br><br>
          <h4> Please select a gene of interest </h4>
          <br>
          <u class="note"> Just the genes present in the specific study are listed and taken into account for the analysis! </u>
          <br><br>
          <!-- putting gene selector -->
          <select id="gea_tcga_sel"> </select>
          <button id="gea_tcga_run" class="run"> Run analysis </button>
        </div>

        <!-- Loading div -->
        <div class='gea_tcga' id='gea_tcga'>
          <iframe class='results' id='gea_tcga_sel_box'></iframe>
          <iframe class='results' id='gea_tcga_sel_bar'></iframe>
        </div>

        <!-- Calling Javascripts -->
        <script>LoadGeneSelector("gea_tcga_sel", "", "", "tcga")</script>
        <script>LoadAnalysis("gea_tcga_sel","gea_tcga_run","","","tcga_gene_expression","$ac")</script>
      </div>

      <div id="co_expression_analysis">
        <div class='description'>
          <p class='pub_det'>
            We offer users the opportunity to identify genes that are co-expressed with their gene(s) of interest.
            This value is calculated using the Pearson Product Moment Correlation Coefficient (PMCC) value.
            Correlations for the genes specified by the user are presented in a heatmap.
          </p>
          <br><br>
          <h4> Please select at least 3 genes of interest (max 50 genes)</h4>
          <br>
          <u class="note"> Just the genes present in the specific study are listed and taken into account for the analysis! </u>
          <br><br>
          <!-- putting gene selector -->
          <select multiple id="cea_tcga_sel"> </select>
          <br>
          <h4> or paste your gene list here (separated by any wide space character [tab, space, new line])</h4>
          <br>
          <textarea id='textcea_tcga_sel' rows='10' cols='80'></textarea>
          <br>
          <button id="cea_tcga_run" class="run"> Run analysis </button>
        </div>

        <!-- Loading div -->
        <div class='cea_tcga' id='cea_tcga'>
          <iframe class='results' id='cea_tcga_sel_hm'></iframe>
        </div>

        <!-- Calling Javascripts -->
        <script>LoadGeneSelector("cea_tcga_sel", "", "", "tcga")</script>
        <script>LoadAnalysis("cea_tcga_sel","cea_tcga_run","tcga","","tcga_co_expression","$ac")</script>
      </div>

      <div id="survival">
        <div class='description'>
          <p class='pub_det'>
            The relationship between molecular subtype/gene(s) of interest and survival can be assessed.<br>
            <i>Molecular subtype:</i> Each sample is assigned to a risk group according to molecular subtype designation.<br>
            <i>Defined gene(s):</i> A univariate model is applied to the survival data and samples are assigned to risk groups
            based on the median dichotomisation of mRNA expression intensities of the selected gene(s).
            <br>Kaplan Meier plots are generated for each gene and a summary of survival covariates are presented in tabular format.
          </p>
          <br><br>
          <h4> Please select a maximum of 3 genes of interest</h4>
          <br>
          <u class="note"> Just the genes present in the specific study are listed and taken into account for the analysis! </u>
          <br><br>
          <!-- putting gene selector -->
          <select multiple id="surv_tcga_sel"> </select>
          <button id="surv_tcga_run" class="run"> Run analysis </button><br>
        </div>
        <center> <img src='$iframe_directory/$ac.KM_subtype.png'> </center>

        <!-- loading graph container when result launched -->
        <div class='surv_tcga_container' id='surv_tcga_container'>

          <!-- Loading table with results -->
          <table id='survival_details' class='table table-bordered results' cellspacing='0' width='100%'>
            <thead>
              <tr>
                <th>Gene</th>
                <th>P-value (5 yrs)</th>
                <th>HR (5 yrs)</th>
                <th>P-value (10 yrs)</th>
                <th>HR (10 yrs)</th>
                <th>P-value (max yrs)</th>
                <th>HR (max yrs)</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>

        <!-- Calling Javascripts -->
        <script>LoadGeneSelector("surv_tcga_sel", "", "", "tcga")</script>
        <script>LoadAnalysis("surv_tcga_sel","surv_tcga_run","tcga","","tcga_survival","$ac")</script>
      </div>

      <div id='gene_networks'>
        <div class='description'>
          <p class='pub_det'>
            Here we present an interactive tool to explore interactions among proteins of interest.
          </p>
          <br><br>
            <table id='network_parameters_container'>
              <tr style='height:70px; vertical-align:top'>
                <td colspan=2>
                  <h4> Please select the genes of interest (maximum 5 genes) </h4>
                  <br>
                  <u class="note"> Just the genes present in the specific study are listed and taken into account for the analysis! </u>
                  <br><br>
                  <select multiple id='tcga_net_sel'></select>
                </td>
              </tr>
              <tr>
                <td>
                  <h4> Please select the interaction score threshold </h4>
                  <div id='mentha-score'></div>
                  <!-- loading threshold labels -->
                  <input type='text' id='min_thr_label' readonly>
                  <input type='text' id='max_thr_label' readonly>
                </td>
                <td>
                  <button id='tcga_run_net' class='run'> Run analysis </button>
                </td>
              </tr>
            </table>
            <!-- load legend div -->
            <div id='net_legend' title='Network legend' style='display:none'>
              <img src='images/net_legend.svg'
            </div>
        </div>
        <!-- loading graph container when result launched -->
        <div class='network_container' id='GraphContainerNET'>
          <!-- initializing hidden value for random code (useful for changing graph later) -->
          <input type='hidden' id='random_code'/>
          <table>
            <tr>
              <h4> Tumour stages available in the dataset: </h4><br>
EOT;
      // loading multiple radio buttons according to the speciments into the target file
      $target_io = fopen("$result_directory/tmp".$ac.".target.txt", "r");
      // initilizing array with speciments
      $all_specimens = array();
      // removing first line
      $headers = fgetcsv($target_io, 1000, "\t");
      while (($target = fgetcsv($target_io, 1000, "\t")) !== FALSE) {
        $target = array_combine($headers, $target);
        // here we change the target column to see if the dataset has been curated by Ema or not
        $all_specimens[] = $target["MOLECULAR_SUBTYPE"];
      }
      fclose($target_io);
      // uniquing specimens
      $all_specimens = array_unique($all_specimens);

      // listing speciments
      $cont_specimen = 0;
      foreach ($all_specimens as &$specimen) {
        if ($cont_specimen==0) {
          echo "<td style=\"padding-right:10px;\"><input type=\"radio\" name=\"selector\" checked onclick=LoadNetworkGraph('$ac','".$cont_specimen."'); />".$specimen."</td>";
        } else {
          echo "<td style=\"padding-right:10px;\"><input type=\"radio\" name=\"selector\" style=\"margin-right:10px;\" onclick=LoadNetworkGraph('$ac','".$cont_specimen."'); />".$specimen."</td>";
        }
        $cont_specimen++;
      }

  echo "   </tr>
        </table>";

  echo <<< EOT

          <!-- inserting legend for color nodes -->
          <br><br>
          <img src='images/icons/question_mark.png' onClick='LoadLegend()' style='width:30px; height:30px'>
          <iframe class='results' id='network_container' onload='resizeIframe(this);'></iframe>

          <!-- loading table with interactions -->
          <table id='network_details' class='table table-bordered results' cellspacing='0' width='100%'>
            <thead>
              <tr>
                <th>Source Gene (SG)</th>
                <th>Expression SG</th>
                <th>Target Gene (TG)</th>
                <th>Expression TG</th>
                <th>PMIDs</th>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
        <!-- loading javascripts -->
        <script>LoadScoreSlider('mentha-score')</script>
        <script>LoadGeneSelector('tcga_net_sel','','','tcga')</script>
        <script>LoadAnalysis('tcga_net_sel','tcga_run_net','','','tcga_gene_network','$ac')</script>
        <script>LoadTCGATabs()</script>
      </div>
    </div>
    <!-- DIV for LOADING -->
    <div id="loading" class="hideable"></div>
    <!-- --------------- -->
  </body>
</html>
EOT;
?>
