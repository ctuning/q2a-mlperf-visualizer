function drawCompareCharts() {

	var chartdata1a = [], chartdata2a = [];	
	var chartdata1b = [], chartdata2b = [];	
	var chartdata1c = [], chartdata2c = [];	
	var models = [], val1a=[], val2a=[], val1b=[], val2b=[], val1c=[], val2c=[];

	$("#results tbody tr td:nth-child(1)").each( function(){
		if (!($(this).is(":hidden")) ) {
			models.push( $(this).text() );
		}
	});

	$("#results tbody tr td:nth-child(2)").each( function(){
		if (!($(this).is(":hidden")))
			val1a.push( $(this).text() );       
	});
	$("#results tbody tr td:nth-child(3)").each( function(){
		if (!($(this).is(":hidden")))
			val2a.push( $(this).text() );       
	});
	$("#results tbody tr td:nth-child(5)").each( function(){
		if (!($(this).is(":hidden")))
			val1b.push( $(this).text() );       
	});
	$("#results tbody tr td:nth-child(6)").each( function(){
		if (!($(this).is(":hidden")))
			val2b.push( $(this).text() );       
	});
	$("#results tbody tr td:nth-child(8)").each( function(){
		if (!($(this).is(":hidden")))
			val1c.push( $(this).text() );       
	});
	$("#results tbody tr td:nth-child(9)").each( function(){
		if (!($(this).is(":hidden")))
			val2c.push( $(this).text() );       
	});
	var count=0;
	for(var i = 0; i < models.length; i++) {
		chartdata1a.push({
			x: count,
			//parseFloat(performance[i]),
			y: parseFloat(val1a[i]),
			label: models[i]
		});
		chartdata2a.push({
			x: count,
			//parseFloat(performance[i]),
			y: parseFloat(val2a[i]),
			label: models[i]
		});
		
		chartdata1b.push({
			x: count,
			//parseFloat(performance[i]),
			y: parseFloat(val1b[i]),
			label: models[i]
		});
		chartdata2b.push({
			x: count,
			//parseFloat(performance[i]),
			y: parseFloat(val2b[i]),
			label: models[i]
		});
		chartdata1c.push({
			x: count,
			//parseFloat(performance[i]),
			y: parseFloat(val1c[i]),
			label: models[i]
		});
		chartdata2c.push({
			x: count,
			//parseFloat(performance[i]),
			y: parseFloat(val2c[i]),
			label: models[i]
		});
		count++;
	}
	chart1 = new CanvasJS.Chart("chartContainer1", {
		title: {
			text: "Performance Comparison"
		},
		legend: {
			cursor: "pointer",
			//itemclick: toggleDataSeries,
		},
		axisX:{
			intervalType: String,
			valueFormatString: " ",
			labelFormatter: function(e) {
				return (""+e.label).substring(0,25);
			},
		},
		axisY: {
			crosshair: {
				enabled: true
			},
			title: "Samples per Second",
		},
		data: [
			{
				showInLegend: true,
				type: "column",
				name: data1,
				dataPoints: chartdata1a
			},
			{
				showInLegend: true,
				type: "column",
				name: data2,
				dataPoints: chartdata2a
			},

		]
	});
	chart1.render();
if(draw_power) {	
chart2 = new CanvasJS.Chart("chartContainer2", {

	title: {
		text: "Power Comparison"
	},
	legend: {
		cursor: "pointer",
			//itemclick: toggleDataSeries,
	},
	axisX:{
		intervalType: String,
		valueFormatString: " ",
		labelFormatter: function(e) {
			return (""+e.label).substring(0,25);
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
			name: data1,
			dataPoints: chartdata1b
		},
		{
			showInLegend: true,
			type: "column",
			name: data2,
			dataPoints: chartdata2b
		},

	]
});
chart2.render();
}

if(draw_power_efficiency) {

chart3 = new CanvasJS.Chart("chartContainer3", {

	title: {
		text: "Power Efficiency"
	},
	legend: {
		cursor: "pointer",
			//itemclick: toggleDataSeries,
	},
	axisX:{
		intervalType: String,
		valueFormatString: " ",
		labelFormatter: function(e) {
			return (""+e.label).substring(0,25);
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
			name: data1,
			dataPoints: chartdata1c
		},
		{
			showInLegend: true,
			type: "column",
			name: data2,
			dataPoints: chartdata2c
		},

	]
});
chart3.render();
}
}

document.getElementById("printChart1").addEventListener("click",function(){
	chart1.exportChart({format: "png"});
}); 

if(draw_power) {
document.getElementById("printChart2").addEventListener("click",function(){
	chart2.exportChart({format: "png"});
}); 
}

if(draw_power_efficiency) {
document.getElementById("printChart3").addEventListener("click",function(){
	chart3.exportChart({format: "png"});
});
}

$( document ).on( "click", "athead th", function() {
	drawCompareCharts();
});
