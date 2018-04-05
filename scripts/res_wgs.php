<?php
echo <<< EOT
  <!-- Results Section -->
  <div class="container" id="dr_results">
    <ul>
      <li><a href="#mutation">Mutation</a></li>
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
      <script>LoadDRTabs()</script>
    </div>
  </div>
EOT;
 ?>
