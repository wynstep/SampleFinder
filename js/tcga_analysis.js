/* Javascript methods for the Tissue Finder 2.0 *
 * Coder: Stefano Pirro'
 * Institution: Barts Cancer Institute
 * Details: all the javascript behaviours of the SF2.0 portal, are included here. */

// function to sort the JSON to a desired order
function sortJSON(data, key, way) {
 return data.sort(function(a, b) {
   var x = a[key]; var y = b[key];
   if (way === 'desc' ) { return ((x < y) ? -1 : ((x > y) ? 1 : 0)); }
   if (way === 'asc') { return ((x > y) ? -1 : ((x < y) ? 1 : 0)); }
 });
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
      ParseParams();
    }
  });
}

function LoadSliderFilters(id, min, max) {
  $( "div#"+id+"" ).slider({
    range: true,
    min: min,
    max: max,
    values: [min, max],
    change: function() {
      ParseParams();
    }
  });
}

function LoadSelect2Filters(id) {
  // loading selector
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

  // action on click
  $("#"+id+"").on("change", function() {
    ParseParams();
  });
}

function ParseParams() {
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
    if ($(this).select2('val') != null) {
      params[$(this).attr("id")] = "'"+$(this).select2('val').join("','")+"'";
    }
  });

  // hiding stats container
  $("fieldset.statistics_container").hide();
  UpdateSampleCount(params);
  UpdatePubsCount(params);
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
      $("div.perform_request").hide();
      $("div.counter").css('opacity', '0.1');
    },
    success: function(data) {
      // update the counter number
      //console.log(data.queries);
      LoadCounter(data.count_cases, "div#counter_cases");
      LoadCounter(data.count_samples, "div#counter_samples");
      $("div.perform_request").show();
      $("div.counter").css('opacity', '1');
      // give the possibility to perform requests
      LoadRequestPage(data.count_cases, data.cases);

      // loading statistical details for the counted cases and samples
      var new_params;
      new_params = params;
      LoadStatisticalDetails(new_params, data.count_cases);
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
      $("div.perform_request").hide();
      $("div#counter_pubs.counter").css('opacity', '0.1');
    },
    success: function(data) {
      //console.log(data.pubs);
      // update the counter number
      LoadCounter(data.count_pubs, "div#counter_pubs");
      $("div#counter_pubs.counter").css('opacity', '1');

      $("button#view_pubs").unbind("click").click( function() {
        LoadPubsTable(data.pubs);
      });
    }
  });
}

function LoadPubsTable(pubs) {
  console.log(pubs);
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
        $("div#loading").html("<h1> Loading... </h1>");
        $("div#loading").show("slow");
        // hide central section
        $("div#central").hide();
      },
      success: function(data) {
        // hiding the loading div
        $("div#loading").hide("slow");
        $("div#request").html(data);
      }
    });
  });
}

function LoadStatisticalDetails(params, count_cases) {
  $( "img#statistics" ).unbind("click").click(function(e) {
    if (count_cases <= 300) {
      $.ajax( {
        dataType: "json",
        url:"scripts/LoadStatisticalDetails.php",
        type:"post",
        data: params,
        // reading the parameters
        beforeSend: function() {
          $("div.perform_request").hide();
          $("fieldset.statistics_container").hide();
          $("div#loading").html("<h1> Loading... </h1>");
          $("div#loading").show("slow");
        },
        success: function(data) {
          console.log(data);
          // showing the charts
          $("div#loading").hide("slow");
          $("div.perform_request").show();
          StatGraph(data["c.SEX"], "Gender", "pie", "gender_stat");
          StatGraph(data["c.AGE"], "Age", "bar", "age_stat");
          StatGraph(data["c.SURVIVALSTATUS"], "Survival state", "pie", "surv_stat");
          StatGraph(data["c.ETHNICGROUP"], "Ethnic groups", "bar", "eth_stat");
          StatGraph(data["c.FAMILYHISTORY"], "Family history", "pie", "fam_stat");
          StatGraph(data["d.HER2"], "HER2", "pie", "her2_stat");
          StatGraph(data["d.ERSTATUS"], "ER", "pie", "er_stat");
          StatGraph(data["d.PRSTATUS"], "PR", "pie", "pr_stat");
          StatGraph(data["d.MENOPAUSALSTATUS"], "Menopausal status", "pie", "men_stat");
          StatGraph(data["d.STAGE"], "Stage", "pie", "stage_stat");
          StatGraph(data["d.GRADE"], "Grade", "pie", "grade_stat");
          StatGraph(data["t.TYPE"], "Type of therapy", "pie", "tot_stat");
          $("fieldset.statistics_container").show("slow");
        }
      });
    } else {
      alert("Sorry, cannot perform request for more than 300 samples");
    }
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
  $("#"+container+"").CanvasJSChart(options);
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
