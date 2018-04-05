/* Javascript methods for the Tissue Finder 2.0 *
 * Coder: Stefano Pirro'
 * Institution: Barts Cancer Institute
 * Details: all the javascript behaviours of the SF2.0 portal, are included here. */

 var loading = "<div>\
                  <img id='loading' src=\"images/loading.svg\">\
                  <br>\
                  <h2> Loading the results... </h2>\
                </div>";
  var maintenance = "<div> <h1> Sorry, the portal is currently under maintenance from 11.30am to 1.30pm</h1> </div>";

// function to sort the JSON to a desired order
function sortJSON(data, key, way) {
 return data.sort(function(a, b) {
   var x = a[key]; var y = b[key];
   if (way === 'desc' ) { return ((x < y) ? -1 : ((x > y) ? 1 : 0)); }
   if (way === 'asc') { return ((x > y) ? -1 : ((x < y) ? 1 : 0)); }
 });
}

// function to load filters accordions
function LoadFilterAccordion() {
  $( "div#features" ).accordion({
    collapsible: true,
    autoHeight: true,
    active: false,
    heightStyle: "content"
  });
}

function LoadOutputAccordion() {
  $( "div#output" ).accordion({
    collapsible: true,
    autoHeight: true,
    active: 0,
    heightStyle: "content"
  })
  $()
}

function LoadCounter(number, el_name) {
  var newCounter = new Odometer({
    el: $(""+el_name+"")[0],
    theme: "train-station",
    value: 0,
    duration: 3000,
  });
  newCounter.render();

  // dynamically increasing value
  newCounter.update(number);
}

// default function for loading candlestick elements
function LoadCSFilters(id, off, on, nc) {
  $("input[type=checkbox]#"+id+"").candlestick({
    "mode":"options",
    'on': "'"+on+"'", // for on value
    'off': "'"+off+"'", // for off value
    'nc': nc, // for none/default value
    'swipe': {
        enabled: false, // for swipe
        transition: true // On value to off value / *true* On value to Default value to Off value
    },
    // initialise at null value
    "default":"",
    'allowManualDefault': true,

    // callbacks
    afterSetting: function() {
      var gender = $("input[type=hidden]#"+id+"").val();
      //ParseParams();
    }
  });
}

function LoadSliderFilters(id, min, max) {
  $( "div#"+id+"" ).slider({
    range: true,
    min: min,
    max: max,
    values: [min, max],
    slide: function( event, ui ) {
      $( "#min_age_label" ).val( "" + ui.values[ 0 ] + "");
      $( "#max_age_label" ).val( "" + ui.values[ 1 ] + "");
    },
    change: function() {
      //ParseParams();
    }
  });
}

function LoadSelect2Filters(id) {
  // loading selector
  if (id != "molsubtype" && id != "stype" && id != "technology") { // filters with custom values
    $("#"+id+"").select2({
      ajax: {
        url: "scripts/RetrieveTableFeatures.php",
        dataType: "json",
        type: "POST",
        data: function (params) {
          return {
            //q: params.term, // search term
            type: id
          };
        },
        processResults: function (data, params) {
          return {
            results: data
          };
          //var q = '';
        },
        cache: false
      },
      width: '100%'
    });
  } else {
    $("#"+id+"").select2({
      width: '100%'
    });
  }

  // action on click
  $("#"+id+"").on("change", function() {
    //ParseParams();
  });
}

function LoadMatchedSamplesSel() {
  // action on checkbox click
  $("input[type=checkbox]").unbind("click").click( function() {
    //ParseParams();
  });
}

function ParseParams() {
  // hide all hideable DIVs
  $(".hideable").hide();

  var params = {};
  $("input[type=hidden]").each(function() {
    // get the name of all the hidden params
    params[$(this).attr("name")] = $(this).val();
  });
    // getting age params
    params["min_age"] = $( "div#age_filter" ).slider("values", 0);
    params["max_age"] = $( "div#age_filter" ).slider("values", 1);

  // getting select2 values
  $("select.select2_multiple").each(function() {
    if ($(this).select2('val') !== null) {
      params[$(this).attr("id")] = "'"+$(this).select2('val').join("','")+"'";
    }
  });

  // getting matched cases checkbox values
  $("input[type=checkbox]").each(function() {
    // get the name of all the checkbox params
    params[$(this).attr("name")] = 0;
  });
  // update 1 value just for checked
  $("input[type=checkbox]:checked").each(function() {
    // get the name of all the checkbox params
    params[$(this).attr("name")] = 1;
  });

  // hiding stats container
  $("fieldset.statistics_container").hide();
  UpdateSampleCount(params);
  UpdatePubsCount(params);
  UpdateTCGACount(params);
  UpdateCCLECount(params);
  UpdateDRCount(params);
  UpdateTrackingSamples(params);
}

// function for updating the filters on the left of the page
function UpdateTrackingSamples(params) {
  $("div#track_filters").empty();
  for (var item_name in params) {
    if (params[item_name] != "null" && params[item_name] != 0) {
      //$("a#no_filter").style.visibility = "hidden";
      var values = params[item_name].toString().split(",");
      for (var i in values) {
        var a_id = ""+item_name+"";
        $("div#track_filters").append("<span class=\"ptrack\"><a id='"+a_id+"'><b>"+item_name+"</b> : "+values[i]+"</a></span><br>");
      }
    }
  }
}

function UpdateCountOnClick() {
  $("button#update_samples").unbind("click").click( function() {
    ParseParams();
  });
}

function UpdateSampleCount(params) {
  // perform query to mysql DB and update the number
  // here we perform an ajax call to the database. When completed, the retrieved number is taken by the
  // LoadCounter function to update the number
  $.ajax( {
    url:"scripts/ExecQuery.php",
    type:"post",
    // reading the parameters
    dataType: "json",
    data: params,
    beforeSend: function() {
      $("div#loading").html(loading);
      $("div#loading").show();
      $("div.perform_request").hide();
    },
    success: function(data) {
      //console.log(data);
      // update the counter number
      LoadCounter(data.count_cases, "div#counter_cases");
      LoadCounter(data.count_samples, "div#counter_samples");
      setTimeout(function(){
        // hiding the loading div
        $("div#loading").hide();
        // showing perform request div
        $("div.perform_request").show();
      },2000);

      // give the possibility to perform requests
      LoadRequestPage(data.count_cases, data.cases);

      // loading statistical details for the counted cases and samples
      LoadStatisticalDetails(data.cases);
    }
  });
}

function UpdatePubsCount(params) {
  // perform query to mysql DB and update the number
  // here we perform an ajax call to the database. When completed, the retrieved number is taken by the
  // LoadCounter function to update the number
  $.ajax( {
    url:"scripts/RetrievePublications.php",
    type:"post",
    // reading the parameters
    dataType: "json",
    data: params,
    beforeSend: function() {
      //console.log(params);
      $("div.perform_request").hide();
      $("div#counter_pubs.counter").css('opacity', '0.1');
    },
    success: function(data) {
      //console.log(data);
      // update the counter number
      LoadCounter(data.count_pubs, "div#counter_pubs");
      $("div#counter_pubs.counter").css('opacity', '1');

      $("button#view_pubs").unbind("click").click( function() {
        LoadPubsTablePopup(data.url);
      });
    }
  });
}

function UpdateTCGACount(params) {
  // perform query to mysql DB and update the number
  // here we perform an ajax call to the database. When completed, the retrieved number is taken by the
  // LoadCounter function to update the number
  $.ajax( {
    url:"scripts/RetrieveTCGADatasets.php",
    type:"post",
    // reading the parameters
    dataType: "json",
    data: params,
    beforeSend: function() {
      $("div.perform_request").hide();
      $("div#counter_tcga.counter").css('opacity', '0.1');
    },
    success: function(data) {
      //console.log(data);
      // update the counter number
      LoadCounter(data.count_tcga, "div#counter_tcga");
      $("div#counter_tcga.counter").css('opacity', '1');

      $("button#an_tcga").unbind("click").click( function() {
        if (data.count_tcga > 0) {
          LoadTCGAAnalyses(data.tcga);
        } else {
          alert("Sorry, there are no TCGA samples matching your criteria");
        }
      });
    }
  });
}

function UpdateDRCount(params) {
  // perform query to mysql DB and update the number
  // here we perform an ajax call to the database. When completed, the retrieved number is taken by the
  // LoadCounter function to update the number
  $.ajax( {
    url:"scripts/RetrieveDRDatasets.php",
    type:"post",
    // reading the parameters
    dataType: "json",
    data: params,
    beforeSend: function() {
      //console.log(params);
      $("div.perform_request").hide();
      $("div#counter_dr.counter").css('opacity', '0.1');
    },
    success: function(data) {
      // update the counter number
      LoadCounter(data.count_dr, "div#counter_dr");
      $("div#counter_dr.counter").css('opacity', '1');

      $("button#an_dr").unbind("click").click( function() {
        if (data.count_dr > 0) {
          LoadDRAnalyses(data.dr);
        } else {
          alert("Sorry, there are no TCGA samples matching your criteria");
        }
      });
    }
  });
}

function UpdateCCLECount(params) {
  // perform query to mysql DB and update the number
  // here we perform an ajax call to the database. When completed, the retrieved number is taken by the
  // LoadCounter function to update the number
  $.ajax( {
    url:"scripts/RetrieveCCLEDatasets.php",
    type:"post",
    // reading the parameters
    dataType: "json",
    data: params,
    beforeSend: function() {
      $("div.perform_request").hide();
      $("div#counter_ccle.counter").css('opacity', '0.1');
    },
    success: function(data) {
      // update the counter number
      LoadCounter(data.count_ccle, "div#counter_ccle");
      $("div#counter_ccle.counter").css('opacity', '1');

      $("button#an_ccle").unbind("click").click( function() {
        if (data.count_ccle > 0) {
          LoadCCLEAnalyses(data.ccle);
        } else {
          alert("Sorry, there are no CCLE samples matching your criteria");
        }
      });
    }
  });
}

function LoadCCLEAnalyses(ccle_codes) {
  // launch TCGA analyses and open results in a new popup
  // create random string for generating the analysis code
  var analysis_code = Math.random().toString(36).substring(7);
  $.ajax( {
    url:"scripts/LoadCCLEAnalysis.php",
    type:"post",
    // reading the parameters
    data: { ac: analysis_code, ccle:ccle_codes.join(",") },
    beforeSend: function() {
      $("div#loading").html(loading);
      $("div#loading").show("slow");
    },
    error : function(request, status, error) {
        var val = request.responseText;
        console.log("error"+val);
    },
    success: function() {
      $("div#loading").hide("slow");

      // when completed, open popup with the results
      window.open("http://bioinformatics.breastcancertissuebank.org:9003/sample_finder/ccle.php?ac="+analysis_code+"",
                  "_blank", "scrollbars=1,resizable=1,height=600,width=650");
    }
  });
}

function LoadTCGAAnalyses(tcga_codes) {
  // launch TCGA analyses and open results in a new popup
  // create random string for generating the analysis code
  var analysis_code = Math.random().toString(36).substring(7);
  $.ajax( {
    url:"scripts/LoadTCGAAnalysis.php",
    type:"post",
    // reading the parameters
    data: { ac: analysis_code, tcga:tcga_codes.join(",") },
    beforeSend: function() {
      $("div#loading").html(loading);
      $("div#loading").show("slow");
    },
    error : function(request, status, error) {
        var val = request.responseText;
        console.log("error"+val);
    },
    success: function() {
      $("div#loading").hide("slow");

      // when completed, open popup with the results
      window.open("http://bioinformatics.breastcancertissuebank.org:9003/sample_finder/tcga.php?ac="+analysis_code+"",
                  "_blank", "scrollbars=1,resizable=1,height=600,width=650");
    }
  });
}

function LoadDRAnalyses(dr_codes) {
  // launch TCGA analyses and open results in a new popup
  // create random string for generating the analysis code
  var analysis_code = Math.random().toString(36).substring(7);
  var params = { ac: analysis_code, dr:dr_codes.join(",") };
  $.ajax( {
    url:"scripts/LoadDRAnalysis.php",
    type:"post",
    dataType: 'json',
    // reading the parameters
    data: params,
    beforeSend: function() {
      //console.log(params);
      $("div#loading").html(loading);
      $("div#loading").show("slow");
    },
    error : function(request, status, error) {
        var val = request.responseText;
        console.log("error"+val);
    },
    success: function(data) {
      //console.log(data);
      $("div#loading").hide("slow");

      // when completed, open popup with the results, depending on the kind of analysed data
      window.open("http://bioinformatics.breastcancertissuebank.org:9003/sample_finder/data_returned.php?ac="+analysis_code+"&analysis_type="+data+"",
                    "_blank", "scrollbars=1,resizable=1,height=600,width=650");
    },
    timeout: 600000 // sets timeout to 600 seconds (10 mins)
  });
}

function LoadPubsTable(pubs) {
  $('table#papers').DataTable().destroy();
  var papersTable = $('table#papers').DataTable( {
    dom: 'Bfrtip',
    buttons: [
          'copyHtml5',
          'excelHtml5',
          'csvHtml5',
          'pdfHtml5'
    ],
    "order": [[ 0, "asc" ]],
    "processing": false,
    "aaData": pubs,
    "aoColumns": [
                  { mData: 'Title' },
                  { mData: 'Author(s)' },
                  { mData: 'PMID' }
                ]
  });
  $('div#pub_details').show();
  scrollSmoothToBottom('div#pub_details');
}

function LoadPubsTablePopup(url) {
  window.open(url,
              "_blank", "scrollbars=1,resizable=1,height=600,width=650");
}

function LoadRequestPage(count_cases, cases) {
  $("button#perform_request").unbind("click").click( function() {
    // show request section
    $.ajax( {
      url:"request.php",
      type:"post",
      data: {count_cases:count_cases, cases:cases},
      // reading the parameters
      beforeSend: function() {
        // show loading div
        $("div#loading").html(loading);
        $("div#loading").show("slow");
        // hide central section
        $("div#central").hide();
      },
      success: function(data) {
        //console.log(data);
        // hiding the loading div
        $("div#loading").hide("slow");
        $("div#request").html(data);
      }
    });
  });
}

function LoadStatisticalDetails(cases_ids) {
  $( "button#statistics" ).unbind("click").click(function(e) {
      $.ajax( {
        dataType: "json",
        url:"scripts/LoadStatisticalDetails.php",
        type:"post",
        data: {cases:cases_ids},
        // reading the parameters
        beforeSend: function() {
          $("div#loading").html(loading);
          $("div#loading").show();
          $("div.perform_request").hide();
          $("fieldset.statistics_container").hide();
        },
        success: function(data) {
          // showing the charts
          $("div#loading").hide("slow");
          $("div.perform_request").show();
          StatGraph(data["SEX"], "Gender", "pie", "gender_stat");
          StatGraph(data["AGE"], "Age", "bar", "age_stat");
          StatGraph(data["SURVIVALSTATUS"], "Survival state", "pie", "surv_stat");
          StatGraph(data["ETHNICGROUP"], "Ethnic groups", "bar", "eth_stat");
          StatGraph(data["FAMILYHISTORY"], "Family history", "pie", "fam_stat");
          StatGraph(data["HER2"], "HER2", "pie", "her2_stat");
          StatGraph(data["ERSTATUS"], "ER", "pie", "er_stat");
          StatGraph(data["PRSTATUS"], "PR", "pie", "pr_stat");
          StatGraph(data["MENOPAUSALSTATUS"], "Menopausal status", "pie", "men_stat");
          //StatGraph(data["STAGE"], "Stage", "pie", "stage_stat");
          StatGraph(data["GRADE"], "Grade", "pie", "grade_stat");
          StatGraph(data["TYPE"], "Type of therapy", "pie", "tot_stat");
          $("fieldset.statistics_container").show();
          $("div.statistics_container").dialog({
            modal: true,
            position: { my: "center", at: "center", of: window },
            resizable: true,
            title: "Statistics details",
            width: 1200,
            height: 800
          });
          scrollSmoothToBottom('fieldset.statistics_container');
        }
      });
  });
}

function StatGraph(data, title, type_g, container) {
  // iterating on the generated data
  sorted_data = sortJSON(data, "label", "asc");
  // prepare data for the plot
  var dataPoints = [];
  for (var i = 0; i <= sorted_data.length - 1; i++) {
    dataPoints.push({
      label: sorted_data[i].label,
      y: parseInt(sorted_data[i].y)
    });
  }

  // different options based on the kind of graph
  if (type_g == "bar") {
    var options = {
      backgroundColor: "transparent",
      width: 300,
      height:300,
  		title: {
  			text: title,
  		},
      animationEnabled: true,
  		data: [{
  			type: type_g, //change it to line, area, bar, pie, etc
        color: "#014D65",
  			dataPoints: dataPoints
  		}]
  	};
  } else {
    var options = {
      backgroundColor: "transparent",
      width: 300,
      height:300,
  		title: {
  			text: title
  		},
      legend: {
        horizontalAlign: "right", // "center" , "right"
        verticalAlign: "bottom",  // "top" , "bottom"
        fontSize: 10
		  },
      animationEnabled: true,
  		data: [{
  			type: type_g, //change it to line, area, bar, pie, etc
        showInLegend: false,
        legendText: "{label}",
  			dataPoints: dataPoints
  		}]
  	};
  }
  $("div#"+container+"").CanvasJSChart(options);
  return;
}

// loading sample counter spinner
function LoadSpinner(maxvalue) {
  $( "input#ncases" ).spinner({
    spin: function( event, ui ) {
      if ( ui.value > maxvalue ) {
        $( this ).spinner( "value", 1 );
        return false;
      } else if ( ui.value < 1 ) {
        $( this ).spinner( "value", 1 );
        return false;
      }
    }
  });
}

// function for scrolling page to the bottom
function scrollSmoothToBottom (id) {
  var div = document.getElementById(id);
  $('body').animate({
    scrollTop: document.body.scrollHeight
  }, 1000);
}
