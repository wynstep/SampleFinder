<?php

/* Functions for Research Portal resource *
 * Coder: Stefano Pirro'
 * Institution: Barts Cancer Institute
 * Details: This page lists all the functions necessary for the SF2.0 portal*/

// function to make flat a recursive array
function array_flatten_recursive($array) {
   if (!$array) return false;
   $flat = array();
   $RII = new RecursiveIteratorIterator(new RecursiveArrayIterator($array));
   foreach ($RII as $value) $flat[] = $value;
   return $flat;
 }

function createHTMLMessage($name, $surname, $institution, $email, $project, $ncases, $cases) {
   $html_message = "This is a test message. <br>
                    These are some user details: <br>
                    <ul>
                      <li><b>Name:</b> $name</li>
                      <li><b>Surname:</b> $surname</li>
                      <li><b>Institute:</b> $institution</li>
                      <li><b>Email address:</b> $email</li>
                      <li><b>Project:</b> $project</li>
                      <li><b>Number of desired cases:</b> $ncases</li>
                    </ul>
                    <b>Available cases ids:</b><br>".implode("<br>",$cases);
    return $html_message;
 }

// given a specific expression file, the function extracts all the gene names
// maps all the needed information and return this as an hash
function retrieveGeneList($expr_file) {
  $GeneDetailsContainer = array();

  // extracting list of genes from the expression file
  // opening the connection to the file
  $stream = fopen("$expr_file", "r");

  // removing first line
  fgetcsv($stream, 1000, "\t");
  while (($file = fgetcsv($stream, 1000, "\t")) !== FALSE) {
    $GeneDetailsContainer[] = $file[0];
  }

  return($GeneDetailsContainer);
}

function MatchedQuery($query, $type) {
  // defining the parameters for database connection
  include("conn_details.php");

  // starting connection
  $db_conn = new mysqli($servername, $username, $password, $dbname);
  $records = mysqli_query($db_conn, $query);
  $all_records = [];
  while ($row = mysqli_fetch_assoc($records)) {
    if ($type == 'cases') {
      array_push($all_records, $row["CONTACTNO"]);
    } else {
      array_push($all_records, $row["CONTACTNO"]);
    }
  }

  // closing the connection
  mysqli_close($db_conn);

  if ($type == "samples") {
    // return the samples names as the contactno + suffix (num iteration)
    $all_records = RenameSamples($all_records);
    return $all_records;
  } else {
    return $all_records;
  }
}

// function to rename sample names according to the contactno + suffix (num iteration)
function RenameSamples($records) {
  // counting occurrences
  $records_statistics = array_count_values($records);

  // renaming samples according to the times of repeating
  $samples_names = array();
  foreach ($records_statistics as $sample_num => $occurrences) {
    for ($i = 0; $i < $occurrences; $i++) {
      $samples_names[] = "".$sample_num.".$i";
    }
  }
  return $samples_names;
}

function NullPubsData() {
  $return_data = [];
  $return_data["count_pubs"] = 0;
  $return_data["pubs"] = none;
  $return_data["url"] = "";

  return $return_data;
}

function LaunchSOAPQuery($rc, $soap_q) {
  $remote_file_call = "<!DOCTYPE Query><Query client='true' processor='TSV' limit='-1' header='1'>
                      <Dataset name='hsapiens_gene_breastCancer' config='hsapiens_gene_breastCancer_config'>
                      $soap_q
                      <Attribute name='hsapiens_gene_breastCancer__resultsData__dm__pubmed_copy_copy2'/>
                      </Dataset></Query>";
  // save remote file call into tmp file
  file_put_contents("../queries/tmp".$rc.".txt", $remote_file_call);
  // executing cURL call
  $last_line = exec("curl --data-urlencode query@../queries/tmp".$rc.".txt http://bioinformatics.breastcancertissuebank.org/martservice/results", $list_papers);
  // uniquing list of papers and removing intest
  if (($key = array_search('Pubmed', $list_papers)) !== false) {
    unset($list_papers[$key]);
  }
  $list_papers = array_values(array_unique($list_papers));
  return $list_papers;
}

function RunStandardAnalyses($absolute_root_dir, $dr_target_file, $dr_filtered_exp_file, $ac) {
  // run PCA analysis
  $outfile = $absolute_root_dir."/queries/data_return/".$ac."";
  exec("Rscript R/PCA.R --exp_file $dr_filtered_exp_file --target $dr_target_file --colouring MOLECULAR_SUBTYPE -p 1 --outfile $outfile");

  // upload receptor status plot
  exec("Rscript R/mclust.R --exp_file $dr_filtered_exp_file --target $dr_target_file --outfile $outfile");

  // upload molecular subtyping plot
  exec("Rscript R/pam50.R --exp_file $dr_filtered_exp_file --target $dr_target_file --outfile $outfile");

  // upload tumor purity analysis
  exec("Rscript R/estimate.R --exp_file $dr_filtered_exp_file --target $dr_target_file --outfile $outfile");

  // create survival analysis
  exec("Rscript R/Survival.R --target $dr_target_file --outfile $outfile");
}

function RunWGSAnalysis($absolute_root_dir, $dr_target_file, $dr_filtered_mut_file, $ac) {
  $outfile = $absolute_root_dir."/queries/data_return/".$ac."";
  exec("Rscript R/Mutations.R --mut_file $dr_filtered_mut_file --target $dr_target_file --outfile $outfile");
  // converting produced pdf file into high-res png
  exec("for file in $(ls ".$outfile.".oncoprint_top*.pdf); do convert -geometry 3600x3600 -density 300x300 -quality 100 \$file \$file.png; done");
}

?>
