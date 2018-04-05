<?php
/* Header section for the Sample Finder 2.0 *
 * Coder: Stefano Pirro'
 * Institution: Barts Cancer Institute
 * Details: The body section contains the menu container and all the other divs */

// printing the whole HTML block code
echo <<< EOT
  <body>
    <div>
      <!-- LOGO PORTION -->
      <div id='logo'>
        <a href='http://www.breastcancertissuebank.org' target='null'>
          <img src='images/front_logo.svg'/>
        </a>
      </div>

      <!-- BREADCRUMB -->
      <div id="breadcrumb">
         <ul class="breadcrumb">
          <li><a href="http://www.breastcancertissuebank.org/">Main Page</a></li>
          <li><img src="images/icons/breadcrumb-arrow.png" alt=""></li>
          <li><a href="http://bioinformatics.breastcancertissuebank.org:9003/sample_finder">SampleFinder 2.0</a></li>
        </ul>
      </div>

      <div id="central">
        <!-- INTEST -->
        <div id="inner-head-top">
          <h1 id="head-top"> SampleFinder 2.0 </h1>
        </div>

        <!-- Container for the filters -->
        <!-- <table class="filter" id="left">
          <tr>
            <td style="text-align:center; padding-bottom:10px;">
              <h2> Filtered by: </h2>
            </td>
          </tr>
          <tr>
            <td>
              <div id="track_filters">
                <a id="no_filter"> No filter </a>
              </div>
              <br><br>
              <a id="reset_filter" href="http://bioinformatics.breastcancertissuebank.org:9003/sample_finder/">Reset all</a>
            </td>
          </tr>
        </table> -->
        <table class="filter" id="center">
          <tr>
            <td style="text-align:left; padding-bottom:10px;">
              <h2> Clinical characteristics </h2>
            </td>
          </tr>
          <tr>
            <td>
              <div id="features">
                <h3> Donor </h3>
                <div id="patient_feature">
                  <table class="patient_filter">
                    <tr>
                      <td><p class="filter">Gender:</p></td>
                      <td>Male</td>
                      <td><input class="js-candlestick" type="checkbox" name="gender" id="gender" value="null"></td>
                      <td>Female</td>
                    </tr>
                    <tr>
                      <td><p class="filter">Age:</p></td>
                      <td colspan=3>
                        <div id="age_filter"></div>
                        <!-- loading age labels -->
                        <input type='text' id='min_age_label' readonly value="1">
                        <input type='text' id='max_age_label' readonly value="120">
                      </td>
                    </tr>
                    <tr>
                      <td><p class="filter">Survival:</p></td>
                      <td>Alive</td>
                      <td><input type="checkbox" name="surv_state" id="surv_state" value="null"></td>
                      <td>Deceased</td>
                    </tr>
                    <tr>
                      <td><p class="filter">Ethnic group</p></td>
                      <td colspan=3><select data-placeholder="Choose a group..." id="eth_group" class="select2_multiple" multiple></td>
                    </tr>
                    <tr>
                      <td><p class="filter">Family History:</p></td>
                      <td>Yes</td>
                      <td><input type="checkbox" name="fam_hist" id="fam_hist" value="null"></td>
                      <td>No</td>
                    </tr>
                    <tr>
                      <td><p class="filter">Menopausal status:</p></td>
                      <td colspan=3><select data-placeholder="" id="men_status" class="select2_multiple" multiple></td>
                    </tr>
                    <tr>
                      <td  class="section" colspan=4><h2 style="font-size:14px; font-weight:bold;">Receptor status</h2></td>
                    </tr>
                    <!-- <tr>
                      <td><p class="filter">BRCA1</p></td>
                      <td>Positive</td>
                      <td><input type="checkbox" name="brca1" id="brca1" value="null"></td>
                      <td>Negative</td>
                    </tr>
                    <tr>
                      <td><p class="filter">BRCA2</p></td>
                      <td>Positive</td>
                      <td><input type="checkbox" name="brca2" id="brca2" value="null"></td>
                      <td>Negative</td>
                    </tr> -->
                    <tr>
                      <td><p class="filter">HER2</p></td>
                      <td>Positive</td>
                      <td><input type="checkbox" name="her2" id="her2" value="null"></td>
                      <td>Negative</td>
                    </tr>
                    <tr>
                      <td><p class="filter">ER</p></td>
                      <td>Positive</td>
                      <td><input type="checkbox" name="er" id="er" value="null"></td>
                      <td>Negative</td>
                    </tr>
                    <tr>
                      <td><p class="filter">PR</p></td>
                      <td>Positive</td>
                      <td><input type="checkbox" name="pr" id="pr" value="null"></td>
                      <td>Negative</td>
                    </tr>
                  </table>
                </div>
                <h3> Sample </h3>
                <div id="sample_feature">
                  <table class="sample_filter">
                    <tr>
                      <td><p class="filter">Stage</p></td>
                      <td colspan=3><select data-placeholder="Choose a stage..." class="select2_multiple" id="stage" multiple></td>
                    </tr>
                    <tr>
                      <td><p class="filter">Grade</p></td>
                      <td colspan=3><select data-placeholder="Choose a tumour grade..." class="select2_multiple" id="grade" multiple></td>
                    </tr>
                    <tr>
                      <td><p class="filter">Specimen</p></td>
                      <td colspan=3>
                        <select data-placeholder="" class="select2_multiple" id="stype" multiple>
                          <optgroup label="Surgical">
                            <option value="Fresh Tissue">Fresh Tissue</option>
                            <option value="Frozen Tissue">Frozen Tissue</option>
                            <option value="Paraffin Embedded">Paraffin Embedded</option>
                          </optgroup>
                          <optgroup label="Body Fluids">
                            <option value="Blood DNA">Blood DNA</option>
                            <option value="Whole Blood">Whole Blood</option>
                            <option value="Plasma">Plasma</option>
                            <option value="Heparin Plasma">Heparin Plasma</option>
                            <option value="RBCs (EDTA)">RBCs (EDTA)</option>
                            <option value="RBCs (heparin)">RBCs (heparin)</option>
                            <option value="Serum">Serum</option>
                          </optgroup>
                        </select>
                        <br>
                        <input type="checkbox" id="stype_matched" name="stype_matched">
                        <label for="stype_matched">Matched cases</label>
                      </td>
                    </tr>
                    <tr>
                      <td><p class="filter">Tissue</p></td>
                      <td colspan=3>
                        <select data-placeholder="" class="select2_multiple" id="ttype" multiple>
                        <br>
                        <input type="checkbox" id="ttype_matched" name="ttype_matched">
                        <label for="ttype_matched">Matched cases</label>
                      </td>
                    </tr>
                    <tr>
                      <td><p class="filter">Cancer type</p></td>
                      <td colspan=3>
                        <select data-placeholder="" class="select2_multiple" id="ctype" multiple>
                        <br>
                        <input type="checkbox" id="ctype_matched" name="ctype_matched">
                        <label for="ctype_matched">Matched cases</label>
                      </td>
                    </tr>
                  </table>
                </div>
                <h3> Therapy </h3>
                <div id="therapy_feature">
                  <table class="therapy_filter">
                    <tr>
                      <td><p class="filter">Therapy (Including)</p></td>
                      <td>
                        <select data-placeholder="" id="tot_in" class="select2_multiple" multiple>
                        <br>
                        <input type="checkbox" id="tot_in_matched" name="tot_in_matched">
                        <label for="tot_in_matched">Matched cases</label>
                      </td>
                    </tr>
                    <tr>
                      <td><p class="filter">Therapy (Excluding)</p></td>
                      <td>
                        <select data-placeholder="" id="tot_ex" class="select2_multiple" multiple>
                        <br>
                        <input type="checkbox" id="tot_ex_matched" name="tot_ex_matched">
                        <label for="tot_ex_matched">Matched cases</label>
                      </td>
                    </tr>
                  </table>
                </div>
                <h3> In silico (demo)</h3>
                <div id="in_silico_feature">
                  <table class="in_silico_filter">
                    <tr>
                      <td><p class="filter">Technology </p></td>
                      <td colspan=3>
                        <select data-placeholder="" class="select2_multiple" id="technology" multiple>
                          <option value="wgs">Whole Genome Sequencing</option>
                          <option value="rnaseq">RNA Sequencing</option>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td><p class="filter">Molecular subtype </p></td>
                      <td colspan=3>
                        <select data-placeholder="" class="select2_multiple" id="molsubtype" multiple>
                          <option value="Normal">Normal</option>
                          <option value="LumA">LumA</option>
                          <option value="LumB">LumB</option>
                          <option value="Basal">Basal</option>
                          <option value="Her2">Her2</option>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td><p class="filter"> TP53 mutations</p></td>
                      <td>Yes</td>
                      <td><input type="checkbox" name="tp53_mut" id="tp53_mut" value="null"></td>
                      <td>No</td>
                    </tr>
                  </table>
                </div>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <button class="analysis" id="update_samples" style="margin-top: 20px;"> View available samples </button>
            </td>
          </tr>
        </table>
        <table class="filter" id="right">
          <tr>
            <td style="text-align:left; padding-bottom:10px;">
              <h2> Our collection </h2>
            </td>
          </tr>
          <tr>
            <td>
              <div class="counter_container">
                <table style="table-layout:fixed; width:100%; margin-bottom:-15px;">
                  <tr>
                    <td>
                      <p class="output">Cases</p>
                    </td>
                    <td>
                      <div id="counter_cases" class="counter">0</div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <p class="output">Samples</p>
                    </td>
                    <td>
                      <div id="counter_samples" class="counter">0</div>
                    </td>
                  </tr>
                  <tr>
                    <td colspan=2>
                      <button id="statistics" class="analysis" style="margin-bottom:20px; margin-top:10px;"> View data summary </button>
                    </td>
                  </tr>
                </table>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div id="output">
                <h3> Bioinformatics </h3>
                <div class="counter_container">
                  <table id="bcntb_dr">
                    <tr>
                      <td colspan=3>
                        <p><a href="http://bioinformatics.breastcancertissuebank.org:9003/bcntb_bioinformatics/pages/" style="font-size:14px;" target="new">BCNTB analytics</a></p>
                      </td>
                    </tr>
                    <tr>
                      <td style="width:70%">
                        <p class="output">BCNTB data return</p>
                      </td>
                      <td style="width:30%">
                        <div id="counter_dr" class="counter">0</div>
                      </td>
                      <td>
                        <button id="an_dr" class="analysis"> Analyse </button>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <p class="output">CCLE</p>
                      </td>
                      <td>
                        <div id="counter_ccle" class="counter">0</div>
                      </td>
                      <td>
                        <button id="an_ccle" class="analysis"> Analyse </button>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <p class="output">TCGA</p>
                      </td>
                      <td>
                        <div id="counter_tcga" class="counter">0</div>
                      </td>
                      <td>
                        <button id="an_tcga" class="analysis"> Analyse </button>
                      </td>
                    </tr>
                    <tr>
                      <td colspan=3>
                        <p><a href="http://bioinformatics.breastcancertissuebank.org/martwizard/#!/import?mart=hsapiens_gene_breastCancer_config&step=2" style="font-size:14px;" target="new">BCNTB miner</a></p>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <p class="output">PubMed</p>
                      </td>
                      <td>
                        <div id="counter_pubs" class="counter">0</div>
                      </td>
                      <td>
                        <button id="view_pubs" class="analysis"> View </button>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
            </td>
          </tr>
        </table>
        <div id="footer">
          <div class="perform_request">
            <button id="perform_request"> Perform a request </button>
          </div>
        </div>
      </div>

      <!-- Request DIV -->
      <div id="request"></div>

      <!-- Loading DIV (to show while loading) -->
      <div id="loading" class="hideable"></div>

      <!-- STATISTICS CONTAINER -->
      <div class="statistics_container">
        <fieldset class="statistics_container">
          <legend><b>Donors</b> statistics</legend>
            <div class="stat_graph" id="gender_stat"></div>
            <div class="stat_graph" id="age_stat"></div>
            <div class="stat_graph" id="surv_stat"></div>
            <div class="stat_graph" id="eth_stat"></div>
            <div class="stat_graph" id="fam_stat"></div>
        </fieldset>
        <fieldset class="statistics_container">
          <legend><b>Sample</b> statistics</legend>
            <div class="stat_graph" id="pr_stat"></div>
            <div class="stat_graph" id="her2_stat"></div>
            <div class="stat_graph" id="er_stat"></div>
            <div class="stat_graph" id="men_stat"></div>
            <div class="stat_graph" id="grade_stat"></div>
            <div class="stat_graph" id="stage_stat"></div>
        </fieldset>
        <fieldset class="statistics_container">
          <legend><b>Treatment</b> statistics</legend>
            <td><div class="stat_graph" id="tot_stat"></div></td>
        </fieldset>
      </div>

    </div>
  </body>

  <!-- Load Filters -->
  <script>LoadCSFilters("gender","Male","Female","null");</script>
  <script>LoadSliderFilters("age_filter", 1, 120);</script>
  <script>LoadCSFilters("surv_state","Alive","Dead","null");</script>
  <script>LoadCSFilters("fam_hist","Yes","No","null");</script>
  <script>LoadCSFilters("her2","Positive","Negative","null");</script>
  <script>LoadCSFilters("er","Positive","Negative","null");</script>
  <script>LoadCSFilters("pr","Positive","Negative","null");</script>
  <script>LoadCSFilters("tp53_mut","Yes","No","null");</script>
  <script>LoadSelect2Filters("eth_group");</script>
  <script>LoadSelect2Filters("men_status");</script>
  <script>LoadSelect2Filters("stage");</script>
  <script>LoadSelect2Filters("grade");</script>
  <script>LoadSelect2Filters("tot_in");</script>
  <script>LoadSelect2Filters("tot_ex");</script>
  <script>LoadSelect2Filters("ttype");</script>
  <script>LoadSelect2Filters("stype");</script>
  <script>LoadSelect2Filters("ctype");</script>
  <script>LoadSelect2Filters("molsubtype");</script>
  <script>LoadSelect2Filters("technology");</script>
  <!-- <script>LoadSelect2Filters("ni_char");</script>
  <script>LoadSelect2Filters("i_char");</script> -->
  <script>LoadMatchedSamplesSel();</script>
  <!---------------------->

  <!-- Load Javascripts -->
  <script>LoadCounter("0", "div#counter_cases");</script>
  <script>LoadCounter("0", "div#counter_samples");</script>
  <script>LoadCounter("0", "div#counter_pubs");</script>
  <script>LoadCounter("0", "div#counter_tcga");</script>
  <script>LoadCounter("0", "div#counter_ccle");</script>
  <script>LoadCounter("0", "div#counter_dr");</script>
  <script>ParseParams();</script>
  <script>UpdateCountOnClick();</script>
  <script>LoadFilterAccordion();</script>
  <script>LoadOutputAccordion();</script>
  <!---------------------->


EOT;

?>
