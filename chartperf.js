function drawPerfCharts() {

	var mylocation = [], system_names = [],framework = [], performance=[], additional_metric = [], models=[];
	var locationIndex = {}, locCount = 0;
	$("#results tbody tr td:nth-child(1)").each( function(){
		if (!($(this).is(":hidden")) ){
			var x = $(this).text();
			mylocation.push(x );
			if (! (x in locationIndex)) {
				locationIndex[x] = locCount++;
			}
		}
	});
	$("#results tbody tr td:nth-child(3)").each( function(){
		if (!($(this).is(":hidden")) )
			system_names.push( $(this).text() );       
	});
	$("#results tbody tr td:nth-child(6)").each( function(){
		if (!($(this).is(":hidden")))
			framework.push( $(this).text() );       
	});

	if(openmodel) {
		$("#results tbody tr td:nth-child(7)").each( function(){
			if (!($(this).is(":hidden")))
				models.push( $(this).text() );       
		});

	}
	else {
		models.push(model);
	}


	$("#results tbody tr td:nth-child("+perfcolumnindex+")").each( function(){
		if (!($(this).is(":hidden")))
			performance.push( $(this).text() );       
	});

	$("#results tbody tr td:nth-child("+ (perfcolumnindex + 1) + ")").each( function(){
		if (!($(this).is(":hidden")))
			additional_metric.push( $(this).text() );       
	});
	var count=0;
	var  modelsData = {}, modelsData2 = {},  modelsCount = [];
	for(var i = 0; i < models.length; i++) {
		modelsData[models[i]] = [];
			if(additional_metric[i]) {
		modelsData2[models[i]] = [];
			}
		modelsCount[models[i]] = 0;
	}
	for(var i = 0; i < mylocation.length; i++) {
		var chart1data = {
			showInLegend: true,
			name: "",
		};
		if(openmodel){
			x =  modelsCount[models[i]];
		}
		else {
			x =  modelsCount[models[0]];
		}
		var datap = {
			x: locationIndex[mylocation[i]],
			y: parseFloat(performance[i]),
			//label: system_names[i],
			label: mylocation[i],
			name: mylocation[i],
			//indexLabel: framework[i],
			//indexLabel: mylocation[i],
			indexLabelPlacement: "inside",
			indexLabelOrientation: "vertical",
			indexLabelMaxWidth: 200,
			indexLabelWrap: true
		};

		if(openmodel){
			modelsData[models[i]].push(datap); 
			if(additional_metric[i]) {
				var datap2 = structuredClone(datap);
				datap2['y'] = parseFloat(additional_metric[i]);
				modelsData2[models[i]].push(datap2);
			}
			modelsCount[models[i]]++;
		}
		else{
			modelsData[models[0]].push(datap);
			if(additional_metric[i]) {
				var datap2 = structuredClone(datap);
				datap2['y'] = parseFloat(additional_metric[i]);
				modelsData2[models[0]].push(datap2);
			}
			modelsCount[models[0]]++;
		}
		count++;
	}
	chart1Data = [], chart2Data = [];
	for(var key in modelsData) {
		var chart1data = {
			showInLegend: true,
			name: key,
		};
		chart1data['dataPoints'] = modelsData[key];
		chart1Data.push(chart1data);
	}
	for(var key in modelsData2) {
		var chart2data = {
			showInLegend: true,
			name: key,
		};
		chart2data['dataPoints'] = modelsData2[key];
		chart2Data.push(chart2data);
	}
	chart1 = new CanvasJS.Chart("chartContainer1", {

		title: {
			text: chart1title
		},
		subtitles: [{
    text: "Powered by CK Playground",
    fontSize: 40,
    verticalAlign: "center",
    dockInsidePlotArea: true,
    fontColor: "rgba(0,0,0,0.1)"
}],
		legend: {
			cursor: "pointer",
			//itemclick: toggleDataSeries,
		},
		axisX:{
			intervalType: String,
			valueFormatString: " ",
			labelAngle: 0,
			labelTextAlign: "center",
			labelFormatter: function(e) {
				return (""+e.label).substring(0,100);
			},
		},
		axisY: {
			crosshair: {
				enabled: true
			},
			title: chart1ytitle,
		},
		data: chart1Data
	});
	chart1.render();

	chart2 = new CanvasJS.Chart("chartContainer2", {

		title: {
			text: chart2title
		},
		subtitles: [{
    text: "Powered by CK Playground",
    fontSize: 40,
    verticalAlign: "center",
    dockInsidePlotArea: true,
    fontColor: "rgba(0,0,0,0.1)"
}],
		legend: {
			cursor: "pointer",
			//itemclick: toggleDataSeries,
		},
		axisX:{
			intervalType: String,
			valueFormatString: " ",
			labelAngle: 0,
			labelTextAlign: "center",
			labelFormatter: function(e) {
				return (""+e.label).substring(0,100);
			},
		},
		axisY: {
			crosshair: {
				enabled: true
			},
			title: chart2ytitle,
		},
		data: chart2Data
	});
	chart2.render();

}

for(i=1; i<= 2; i++) {
	if(document.getElementById("printChart"+i)) {
		document.getElementById("printChart"+i).addEventListener("click",function(){
			chart1.exportChart({format: "png"});
		});
	}
}


$( document ).on( "click", "athead th", function() {
	drawPerfCharts();
});
