<?php
echo <<<EOT
  <!-- Results Section -->
  <div class="container" id="dr_results">
    <ul>
      <li><a href="#pca">PCA</a></li>
      <li><a href="#tumour_purity">Tumour purity</a></li>
      <li><a href="#molecular_classification">Molecular classification</a></li>
      <li><a href="#receptor_status">Receptor status</a></li>
      <li><a href="#expression_profiles">Gene expression</a></li>
      <li><a href="#co_expression_analysis">Correlations</a></li>
      <li><a href="#survival">Survival analysis</a></li>
      <li><a href="#mutation">Mutations</a></li>
      <li><a href="#gene_networks">Gene networks</a></li>
    </ul>
    <div id="mutation">
      <div class='description'>
        <p class='pub_det'>
          Description of the oncoprint
        </p>
        <br><br>
        <h4> Please select at least 2 genes of interest (max 50 genes)</h4>
        <br>
        <u class="note"> Just the genes present in the specific study are listed and taken into account for the analysis! </u>
        <br><br>
        <!-- putting gene selector -->
        <select multiple id="mut_dr_sel"> </select>
        <button id="mut_dr_run" class="run"> Run analysis </button>
      </div>

      <!-- Loading div -->
      <div id="top_mut_genes">
        <div>
          <table>
            <tr>
              <h4> Number of top-mutated genes to show: </h4><br>
              <td style="padding-right:10px;"><input type="radio" id="10" name="selector" value="" checked onclick=LoadOncoPrint("$ac","10"); />10</td>
              <td style="padding-right:10px;"><input type="radio" id="20" name="selector" onclick=LoadOncoPrint("$ac",'20'); />20</td>
              <td style="padding-right:10px;"><input type="radio" id="30" name="selector" onclick=LoadOncoPrint("$ac",'30'); />30</td>
              <td style="padding-right:10px;"><input type="radio" id="40" name="selector" onclick=LoadOncoPrint("$ac",'40'); />40</td>
              <td style="padding-right:10px;"><input type="radio" id="50" name="selector" onclick=LoadOncoPrint("$ac",'50'); />50</td>
              <td style="padding-right:10px;"><input type="radio" id="75" name="selector" onclick=LoadOncoPrint("$ac",'75'); />75</td>
              <td style="padding-right:10px;"><input type="radio" id="100" name="selector" onclick=LoadOncoPrint("$ac",'100'); />100</td>
            </tr>
          </table>
        </div>
        <center>
          <img id="oncoprint" src="$iframe_directory/$ac.oncoprint_top10.pdf.png" style="width:800px; height:auto"/>
        </center>
      </div>
      <div id="mut_dr_sel" style="display:none" class='results'></div>
      <!-- Calling Javascripts -->
      <script>LoadGeneSelector("mut_dr_sel", "", "", "dr_mut")</script>
      <script>LoadAnalysis("mut_dr_sel","mut_dr_run","","","dr_mutations","$ac")</script>
    </div>
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
    <div id="tumour_purity">
      <div class='description'>
        <p class='pub_det'> Cancer samples frequently contain a small proportion of infiltrating stromal and immune cells that might not
            only confound the tumour signal in molecular analyses but may also have a role in tumourigenesis and progression.
            We apply an algorithm<sup><a href='https://www.ncbi.nlm.nih.gov/pubmed/24113773' target=null>1</a></sup> that infers the tumour purity and the presence of infiltrating stromal/immune cells from gene expression data.
            A tumour purity value between 0 and 1 is inferred from the calculated stromal score,
            immune score and estimate score. All of these values are presented as a scatterplot,
            with a breakdown of scores for each sample available in tabular format from the target file.
        </p>
      </div>
      <iframe class='results' scrolling='no' src='$iframe_directory/$ac.estimate.html'></iframe>
      <table id='$ac-estimate' class='table table-bordered results' cellspacing='0' width='100%'>
        <thead>
          <tr>
            <th>Sample name</th>
            <th>Specimen</th>
            <th>Tumour purity</th>
        </thead>
      </table>
      <!-- LOADING ESTIMATE DATATABLE -->
      <script>LoadJsonDT("$ac-estimate", "estimate", "$ac")</script>
    </div>
    <div id="molecular_classification">
      <div class='description'>
        <p class='pub_det'> The PAM50 single sample predictor model
            <sup><a href='https://www.ncbi.nlm.nih.gov/pubmed/19204204' target=null>1</a></sup>, assigns samples into intrinsic tumour types,
            with distinct transcriptomic signatures, based on the expression of key breast cancer-specific genes.
            These subgroups comprise the oestrogen receptor positive subtypes (Luminal A and Luminal B) and the oestrogen
            receptor negative subtypes (Basal-like, Her2-enriched and Normal breast-like).
            Here, we present the molecular subtype calls for all tumour samples.
        </p>
      </div>
      <iframe class='results' scrolling='no' src='$iframe_directory/$ac.pam50.html'></iframe>
      <table id='$ac-pam50' class='table table-bordered results' cellspacing='0' width='100%'>
        <thead>
          <tr>
            <th>Sample name</th>
            <th>Subtype</th>
        </thead>
      </table>
      <!-- LOADING PAM50 DATATABLE -->
      <script>LoadJsonDT("$ac-pam50", "pam50", "$ac")</script>
    </div>
    <div id="receptor_status">
      <div class='description'>
        <p class='pub_det'> Gaussian finite mixture modelling is implemented to define
            oestrogen (ER), progesterone (PR) and Her2 receptor status
            <sup><a href='https://www.ncbi.nlm.nih.gov/pubmed/27818791' target=null>1</a></sup>.
            Here, we present the receptor status of the samples based on the gene expression of ER, PR and Her2.
            These classifications are used to define triple negative samples.
        </p>
      </div>
      <iframe class='results' scrolling='no' src='$iframe_directory/$ac.mclust.html'></iframe>
      <table id='$ac-mclust' class='table table-bordered results' cellspacing='0' width='100%'>
        <thead>
          <tr>
            <th>Sample name</th>
            <th>ER</th>
            <th>PR</th>
            <th>HER2</th>
            <th>TripleNegative</th>
        </thead>
      </table>
      <!-- LOADING MCLUST DATATABLE -->
      <script>LoadJsonDT("$ac-mclust", "mclust", "$ac")</script>
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
        <select id="gea_dr_sel"> </select>
        <button id="gea_dr_run" class="run"> Run analysis </button>
      </div>

      <!-- Loading div -->
      <div class='gea_dr' id='gea_dr'>
        <iframe class='results' id='gea_dr_sel_box'></iframe>
        <iframe class='results' id='gea_dr_sel_bar'></iframe>
      </div>

      <!-- Calling Javascripts -->
      <script>LoadGeneSelector("gea_dr_sel", "", "", "dr")</script>
      <script>LoadAnalysis("gea_dr_sel","gea_dr_run","","","dr_gene_expression","$ac")</script>
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
        <select multiple id="cea_dr_sel"> </select>
        <br>
        <h4> or paste your gene list here (separated by any wide space character [tab, space, new line])</h4>
        <br>
        <textarea id='textcea_dr_sel' rows='10' cols='80'></textarea>
        <br>
        <button id="cea_dr_run" class="run"> Run analysis </button>
      </div>

      <!-- Loading div -->
      <div class='cea_de' id='cea_dr'>
        <iframe class='results' id='cea_dr_sel_hm'></iframe>
      </div>

      <!-- Calling Javascripts -->
      <script>LoadGeneSelector("cea_dr_sel", "", "", "dr")</script>
      <script>LoadAnalysis("cea_dr_sel","cea_dr_run","dr","","dr_co_expression","$ac")</script>
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
        <select multiple id="surv_dr_sel"> </select>
        <button id="surv_dr_run" class="run"> Run analysis </button><br>
      </div>
      <center> <img src='$iframe_directory/$ac.KM_subtype.png'> </center>

      <!-- loading graph container when result launched -->
      <div class='surv_dr_container' id='surv_dr_container'>

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
      <script>LoadGeneSelector("surv_dr_sel", "", "", "dr")</script>
      <script>LoadAnalysis("surv_dr_sel","surv_dr_run","dr","","dr_survival","$ac")</script>
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
                <select multiple id='dr_net_sel'></select>
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
                <button id='dr_run_net' class='run'> Run analysis </button>
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
      <script>LoadGeneSelector('dr_net_sel','','','dr')</script>
      <script>LoadAnalysis('dr_net_sel','dr_run_net','','','dr_gene_network','$ac')</script>
      <script>LoadDRTabs()</script>
    </div>
  </div>
EOT;

 ?>
