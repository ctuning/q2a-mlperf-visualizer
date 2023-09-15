<?php

class qa_html_theme_layer extends qa_html_theme_base {
	function head_custom()
	{
		$url=qa_request();
		if($url == 'show-results' || $url == 'compare-results'){
			$version = 1.4548213;
			$this->output('
<link rel="stylesheet" rel="preload" as="style" media="all" href="'.QA_HTML_THEME_LAYER_URLTOROOT.'tablesorter-master/dist/css/jquery.tablesorter.pager.min.css">
<link rel="stylesheet" rel="preload" as="style" media="all" href="'.QA_HTML_THEME_LAYER_URLTOROOT.'tablesorter-master/dist/css/theme.blackice.min.css">
<!-- load jQuery and tablesorter scripts -->
<script type="text/javascript" src="'.QA_HTML_THEME_LAYER_URLTOROOT.'tablesorter-master/dist/js/jquery.tablesorter.js"></script>

<!-- tablesorter widgets (optional) -->

<script type="text/javascript" src="'.QA_HTML_THEME_LAYER_URLTOROOT.'tablesorter-master/dist/js/jquery.tablesorter.widgets.js"></script>
<script type="text/javascript" src="'.QA_HTML_THEME_LAYER_URLTOROOT.'tablesorter-master/dist/js/extras/jquery.tablesorter.pager.min.js"></script>


<script src="https://cdn.canvasjs.com/jquery.canvasjs.min.js"></script>
 <style>
        .bgtext1 {
            position: relative;
        }
  
        .bgtext1:after {
            margin: 8rem;
            content: "CK Playground";
            position: absolute;
            transform: rotate(0deg);
            -webkit-transform: rotate(0deg);
            color: rgb(187, 182, 182);
            top: 0;
            left: 0;
            z-index: 1000;
        }
    </style>
');

		}
qa_html_theme_base::head_custom();
}

// this is an example, the share buttons may be moved elsewhere by changing this function.

function body_suffix()
{

	qa_html_theme_base::body_suffix();
	$url=qa_request();
	$version = '1.13a91v9g5'.strtotime("now");
		if($url == 'show-results'){
			$this->output('
<script type="text/javascript" src="'.QA_HTML_THEME_LAYER_URLTOROOT.'chartperf.js?version='.$version.'"></script>
<script type="text/javascript">
var drawCharts = drawPerfCharts;
</script>
<script type="text/javascript" src="'.QA_HTML_THEME_LAYER_URLTOROOT.'script.js?version='.$version.'"></script>
');
	}
		if($url == 'compare-results'){
			$this->output('
<script type="text/javascript" src="'.QA_HTML_THEME_LAYER_URLTOROOT.'chartcompare.js?version='.$version.'"></script>
<script type="text/javascript">
var drawCharts = drawCompareCharts;
</script>
<script type="text/javascript" src="'.QA_HTML_THEME_LAYER_URLTOROOT.'script.js?version='.$version.'"></script>
');
	}
}


}
