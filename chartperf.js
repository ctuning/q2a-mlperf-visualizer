function drawPerfCharts() {

	var chart1data = [];	
	var chart2data = [];	
	var chart3data = [];	
	var chart4data = [];	
	var mylocation = [], system_names = [],framework = [], performance=[], additional_metric = [];

	$("#results tbody tr td:nth-child(1)").each( function(){
		if (!($(this).is(":hidden")) )
			mylocation.push( $(this).text() );       
	});

	$("#results tbody tr td:nth-child(3)").each( function(){
		if (!($(this).is(":hidden")) )
			system_names.push( $(this).text() );       
	});
	$("#results tbody tr td:nth-child(6)").each( function(){
		if (!($(this).is(":hidden")))
			framework.push( $(this).text() );       
	});
	

	$("#results tbody tr td:nth-child(7)").each( function(){
		if (!($(this).is(":hidden")))
			performance.push( $(this).text() );       
	});
	
	$("#results tbody tr td:nth-child(8)").each( function(){
		if (!($(this).is(":hidden")))
			additional_metric.push( $(this).text() );       
	});
	var count=0;
	var chart1datapoints = [], chart2datapoints = [];
	for(var i = 0; i < mylocation.length; i++) {
		var chart1data = {
			showInLegend: true,
			name: "",
		};
		var datap = { 
			y: parseFloat(performance[i]),
			label: system_names[i],
			name: system_names[i],
			indexLabel: framework[i],
			indexLabelPlacement: "inside",
			indexLabelOrientation: "vertical",
			indexLabelMaxWidth: 200,
    indexLabelWrap: true
			 };

		chart1data['dataPoints'] = [
		];
		chart1datapoints.push(chart1data);
		chart1datapoints[0]['dataPoints'].push(datap);
		if(additional_metric[i])
		chart2data.push({
			y: parseFloat(additional_metric[i]),
			label: system_names[i],
			name: system_names[i],
			showInLegend: true,
			legendText: system_names[i],
			indexLabel: framework[i],
			indexLabelPlacement: "inside",
			indexLabelOrientation: "vertical",
			indexLabelMaxWidth: 200,
			//label: mylocation[i]
		});
		count++;
	}

	chart1 = new CanvasJS.Chart("chartContainer1", {

		title: {
			text: chart1title
		},
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
		data: [chart1datapoints[0] ]
	});
	chart1.render();

	chart2 = new CanvasJS.Chart("chartContainer2", {

		title: {
			text: chart2title
		},
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
		data: [
			{
				showInLegend: true,
				type: "column",
				indexLabelPlacement: "outside",
				label: "Platforms",
			name: "",
				dataPoints: chart2data
			},

		]
	});
	chart2.render();

}

for(i=1; i<= 2; i++) {
	document.getElementById("printChart"+i).addEventListener("click",function(){
		chart1.exportChart({format: "png"});
	});
}


$( document ).on( "click", "athead th", function() {
	drawPerfCharts();
});
