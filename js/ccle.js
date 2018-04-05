/* Javascript methods for the Tissue Finder 2.0 *
 * Coder: Stefano Pirro'
 * Institution: Barts Cancer Institute
 * Details: all the javascript behaviours of the BOB portal, are included here. */

// loading frame
var loading = "<div>\
                 <img id='loading' src=\"images/loading.svg\">\
                 <br>\
                 <h2> Loading the results... </h2>\
               </div>";

// webiste for loading iframe
var ccle_iframe_url = "http://bioinformatics.breastcancertissuebank.org:9003/sample_finder/queries/ccle/";

function LoadCCLETabs() {
  $("#ccle_results.container").tabs();
}

function LoadScoreSlider(el_name) {
  $( "div#"+el_name+"" ).slider({
      range: true,
      step: 0.1,
      min: 0,
      max: 1,
      values: [ 0.4, 0.8],
      slide: function( event, ui ) {
        $( "#min_thr_label" ).val( "" + ui.values[ 0 ] + "");
        $( "#max_thr_label" ).val( "" + ui.values[ 1 ] + "");
      }
  });
  // initilizing values on load
  $( "#min_thr_label" ).val(""+$("div#"+el_name+"").slider("values",0)+"");
  $( "#max_thr_label" ).val(""+$("div#"+el_name+"").slider("values",1)+"");
}

// function to load MultiGeneSelector
// this function takes as input the name of html element to call,
// the array express id and PMID id (to call the right expression matrix)
function LoadGeneSelector(el_name, ae, pmid, type_analysis) {

  $.fn.select2.defaults.set("theme", "classic");
  $.fn.select2.defaults.set("ajax--cache", false);

  // we decided to implement an ajax-based search
  $( "#"+el_name+"" ).select2({
    width:'50%',
    ajax: {
      url: "scripts/RetrieveGeneList.php?ae="+ae+"&pmid="+pmid+"&type_analysis="+type_analysis+"",
      dataType: "json",
      delay: 250,
      data: function (params) {
        if (params.term === undefined) {
          q = "A";
        } else {
          return {
            q: params.term, // search term
          };
        }
      },
      processResults: function (data, params) {
        return {
          results: data
        };
        var q = '';
      },
      cache: false
    }
  });
}

// this function launch the Rscript to create the expression profile plot for the selected gene
// the function takes three parameter
// -- el_name: html element to call
// -- ae: array_express id
// -- pmid : pubmed id
// Please note, for security reasons, the launch of Rscript and all system commands are delegated to
// a php function ("LaunchCommand.php")
function LoadAnalysis(genebox, el_name, ae, pmid, type_analysis, random_code) {
  if (type_analysis == "ccle_gene_expression") {
    $("#"+el_name+"").click(function() {
      var gene = $("#"+genebox+"").val();
      // launching ajax call to retrieve the expression plot for the selected gene
      $.ajax( {
        url:"scripts/LaunchCommand.php?TypeAnalysis="+type_analysis+"&Genes="+gene+"&rc="+random_code+"",
        type:"get",
        beforeSend: function()
        {
          $("div#loading").html(loading);
          $("div#loading").show();
        },
        success: function(data) {
          //console.log(data);
          $("iframe#"+genebox+"_box.results").attr("src", ""+ccle_iframe_url+random_code+".live.box.html");
          $("iframe#"+genebox+"_bar.results").attr("src", ""+ccle_iframe_url+random_code+".live.bar.html");
          $(".gea_ccle").show();
          $("div#loading").hide();
          $("div#loading").empty();
        }
      });
    });
  } else if (type_analysis == "ccle_co_expression") {
    $("#"+el_name+"").click(function() {
      var genes_sel = $("#"+genebox+"").val();

      // loading text area (gene list selector)
      var genes_list = $("#text"+genebox+"").val().toUpperCase();
      // splitting genes list by wide space chars
      var genes_list_array = genes_list.split(/\s+/);

      // pushing gene list into the genes array
      var genes = genes_sel.concat(genes_list_array);

      // checking the length of the uploaded genes
      if (genes.length > 2 && genes.length <= 50) {
        var genes_string = genes.join(",");

        // launching ajax call to retrieve the expression plot for the selected gene
        $.ajax( {
          url:"scripts/LaunchCommand.php?TypeAnalysis="+type_analysis+"&Genes="+genes_string+"&rc="+random_code+"",
          type:"get",
          beforeSend: function()
          {
            $("div#loading").html(loading);
            $("div#loading").show();
          },
          success: function(data) {
            $("iframe#"+genebox+"_hm.results").attr("src", ""+ccle_iframe_url+random_code+".live.corr_hm.html");
            $(".cea_ccle").show();
            $("div#loading").hide();
            $("div#loading").empty();
          },
          error: function(data) {
            alert("Sorry, there is an error in the analysis...\n\
                  Probably the number of genes submitted to the analysis is less then 3.");
            $("div#loading").hide();
            $("div#loading").empty();
          }
        });
      } else {
        alert("Please select at least 3 genes (max 50)");
        return false
      }
    });
  } else if (type_analysis == "ccle_expression_layering") {
    $("#"+el_name+"").click(function() {
      var gene = $("#"+genebox+"").val();
      // launching ajax call to retrieve the expression plot for the selected gene
      $.ajax( {
        url:"scripts/LaunchCommand.php?TypeAnalysis="+type_analysis+"&Genes="+gene+"&rc="+random_code+"",
        type:"get",
        beforeSend: function()
        {
          $("div#loading").html(loading);
          $("div#loading").show();
        },
        success: function(data) {
          $("iframe#"+genebox+"_boxel.results").attr("src", ""+ccle_iframe_url+random_code+"_mRNA_vs_CN_boxplot.html");
          $("iframe#"+genebox+"_el.results").attr("src", ""+ccle_iframe_url+random_code+"_mRNA_vs_CN_plot.html");
          $("iframe#"+genebox+"_boxel_mut.results").attr("src", ""+ccle_iframe_url+random_code+"_mRNA_vs_CN_mut_boxplot.html");
          $("iframe#"+genebox+"_el_mut.results").attr("src", ""+ccle_iframe_url+random_code+"_mRNA_vs_CN_mut_plot.html");
          $(".el_ccle").show();
          $("div#loading").hide();
          $("div#loading").empty();
        }
      });
    });
  } else if (type_analysis == "ccle_cn_alterations") {
    $("#"+el_name+"").click(function() {
      var genes = $("#"+genebox+"").val();
      var genes_string = genes.join(",");
      if (genes.length > 1 && genes.length <= 20) {
        // launching ajax call to retrieve the expression plot for the selected gene
        $.ajax( {
          url:"scripts/LaunchCommand.php?TypeAnalysis="+type_analysis+"&Genes="+genes_string+"&rc="+random_code+"",
          type:"get",
          beforeSend: function()
          {
            $("div#loading").html(loading);
            $("div#loading").show();
          },
          success: function(data) {
            console.log(data);
            $("iframe#cna_hm").attr("src", ""+ccle_iframe_url+random_code+"_hm.html");
            $("div#download").html("<center><a class='papers_link' href='"+ccle_iframe_url+random_code+"_cna.txt' target='null'> Download the results </a></center>")
            $("div#loading").hide();
            $("div#loading").empty();
          }
        });
      } else {
        alert("Please select at least 2 genes (max 20)");
        return false
      }
    });
  } else if (type_analysis == "ccle_gene_network") {
    $("#"+el_name+"").click(function() {
      // getting genes of interest
      var genes = $("#"+genebox+"").val();
      var genes_string = genes.join(",");

      // getting min and max score thresolds
      var min_thr = $("input#min_thr_label").val();
      var max_thr = $("input#max_thr_label").val();

      // checking the length of the uploaded genes
        if (genes.length >= 1 && genes.length < 6) {
          // launching ajax call to retrieve the expression plot for the selected gene
          $.ajax( {
            url:"scripts/LaunchCommand.php?TypeAnalysis="+type_analysis+"&Genes="+genes_string+"&rc="+random_code+"&min_thr="+min_thr+"&max_thr="+max_thr+"",
            type:"get",
            beforeSend: function()
            {
              $("div#loading").html(loading);
              $("div#loading").show();
            },
            success: function(data) {
              //console.log(data);
              $("div#loading").hide();
              $("div#loading").empty();
              $("input#random_code").val(random_code);
              $("iframe#network_container.results").attr("src", ""+ccle_iframe_url+random_code+".live.network0.html");
              var table = $('table#network_details').DataTable( {
                dom: 'Bfrtip',
                buttons: [
                      'copyHtml5',
                      'excelHtml5',
                      'csvHtml5',
                      'pdfHtml5'
                ],
                "processing": false,
                "serverSide": false,
                "destroy": true,
                "ajax": {
                  "url": ""+ccle_iframe_url+random_code+".live.network0.json", // we visualize the first network as default
                }
              });
              $(".network_container").show();
            }
          });
        } else {
          alert("Please select a maximum number of 5 genes");
          return false
        }
    });
  }
}

// function for scrolling page to the bottom
function scrollSmoothToBottom (id) {
  var div = document.getElementById(id);
  $('body').animate({
    scrollTop: document.body.scrollHeight
  }, 1000);
}

// function for loading the results accordion
function LoadResultAcc() {
  $("#res_acc").accordion({
    heightStyle: "content",
    active: false,
    collapsible: true
  });
}

// function to adjust the size of the results iframe according to the content
function resizeIframe(obj){
  obj.style.height = 0;
  obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
}

function LoadNetworkGraph(random_code, index) {
  $("iframe#network_container.results").attr("src", ""+ccle_iframe_url+random_code+".live.network"+index+".html");
  $('table#network_details').DataTable().ajax.url(""+ccle_iframe_url+random_code+".live.network"+index+".json").load();
}

function LoadLegend() {
  $( "#net_legend" ).dialog();
  $( "#net_legend" ).show();
}
