
var scenarios = [ "Offline", "Server", "SingleStream", "MultiStream" ];
function drawCompareCharts() {
	for( scenario_id in scenarios) {
		var chartdata1a = [], chartdata2a = [];	
		var chartdata1b = [], chartdata2b = [];	
		var chartdata1c = [], chartdata2c = [];	
		var models = [], val1a=[], val2a=[], val1b=[], val2b=[], val1c=[], val2c=[];
		var scenario = scenarios[scenario_id];

		var tableid = "#results_"+scenario;
		if(!$(tableid)) continue;
		$(tableid +" tbody tr td:nth-child(1)").each( function(){
			if (!($(this).is(":hidden")) ) {
				models.push( $(this).text() );
			}
		});

		$(tableid + " tbody tr td:nth-child(2)").each( function(){
			if (!($(this).is(":hidden")))
				val1a.push( $(this).text() );       
		});
		$(tableid + " tbody tr td:nth-child(3)").each( function(){
			if (!($(this).is(":hidden")))
				val2a.push( $(this).text() );       
		});
		$(tableid + " tbody tr td:nth-child(5)").each( function(){
			if (!($(this).is(":hidden")))
				val1b.push( $(this).text() );       
		});
		$(tableid + " tbody tr td:nth-child(6)").each( function(){
			if (!($(this).is(":hidden")))
				val2b.push( $(this).text() );       
		});
		$(tableid + " tbody tr td:nth-child(8)").each( function(){
			if (!($(this).is(":hidden")))
				val1c.push( $(this).text() );       
		});
		$(tableid + " tbody tr td:nth-child(9)").each( function(){
			if (!($(this).is(":hidden")))
				val2c.push( $(this).text() );       
		});
		var count=0;
		for(var i = 0; i < models.length; i++) {
			chartdata1a.push({
				x: count,
				y: parseFloat(val1a[i]),
				label: models[i]
			});
			chartdata2a.push({
				x: count,
				y: parseFloat(val2a[i]),
				label: models[i]
			});

			chartdata1b.push({
				x: count,
				y: parseFloat(val1b[i]),
				label: models[i]
			});
			chartdata2b.push({
				x: count,
				y: parseFloat(val2b[i]),
				label: models[i]
			});
			chartdata1c.push({
				x: count,
				y: parseFloat(val1c[i]),
				label: models[i]
			});
			chartdata2c.push({
				x: count,
				y: parseFloat(val2c[i]),
				label: models[i]
			});
			count++;
		}
		chart1 = new CanvasJS.Chart("chartContainer"+scenario+"1", {
			title: {
				text: "Performance Comparison"
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
				labelMaxWidth: 60,
				labelFormatter: function(e) {
					return (""+e.label).substring(0,75);
				},
			},
			axisY: {
				crosshair: {
					enabled: true
				},
				title: ytitle[scenario],
			},
			data: [
				{
					showInLegend: true,
					type: "column",
					name: data1[scenario],
					dataPoints: chartdata1a
				},
				{
					showInLegend: true,
					type: "column",
					name: data2[scenario],
					dataPoints: chartdata2a
				},

			]
		});
		chart1.render();
		if(draw_power[scenario]) {	
			chart2 = new CanvasJS.Chart("chartContainer"+scenario+"2", {

				title: {
					text: "Power Comparison"
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
					labelMaxWidth: 60,
					labelFormatter: function(e) {
						return (""+e.label).substring(0,75);
					},
				},
				axisY: {
					crosshair: {
						enabled: true
					},
					title: "Average Power (Watts)",
				},
				data: [
					{
						showInLegend: true,
						type: "column",
						name: data1[scenario],
						dataPoints: chartdata1b
					},
					{
						showInLegend: true,
						type: "column",
						name: data2[scenario],
						dataPoints: chartdata2b
					},

				]
			});
			chart2.render();
		}

		if(draw_power_efficiency[scenario]) {

			chart3 = new CanvasJS.Chart("chartContainer"+scenario+"3", {

				title: {
					text: "Power Efficiency"
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
					labelMaxWidth: 60,
					labelFormatter: function(e) {
						return (""+e.label).substring(0,75);
					},
				},
				axisY: {
					crosshair: {
						enabled: true
					},
					title: "Samples per Watt",
				},
				data: [
					{
						showInLegend: true,
						type: "column",
						name: data1[scenario],
						dataPoints: chartdata1c
					},
					{
						showInLegend: true,
						type: "column",
						name: data2[scenario],
						dataPoints: chartdata2c
					},

				]
			});
			chart3.render();
		}
	}
}

for( scenario_id in scenarios) {
	var scenario = scenarios[scenario_id];
	document.getElementById("printChart"+scenario+"1").addEventListener("click",function(){
		chart1.exportChart({format: "png"});
	}); 

	if(draw_power[scenario]) {
		document.getElementById("printChart"+scenario+"2").addEventListener("click",function(){
			chart2.exportChart({format: "png"});
		}); 
	}

	if(draw_power_efficiency[scenario]) {
		document.getElementById("printChart"+scenario+"3").addEventListener("click",function(){
			chart3.exportChart({format: "png"});
		});
	}
}

$( document ).on( "click", "athead th", function() {
	drawCompareCharts();
});
