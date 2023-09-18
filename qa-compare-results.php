<?php
/*
   Question2Answer by Gideon Greenspan and contributors
   http://www.question2answer.org/

   File: qa-plugin/example-page/qa-example-page.php
   Description: Page module class for example page plugin


   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   as published by the Free Software Foundation; either version 2
   of the License, or (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   More about this license: http://www.question2answer.org/license.php
 */

class qa_compare_results
{
	private $directory;
	private $urltoroot;
	private $new;

	public function load_module($directory, $urltoroot)
	{
		$this->directory=$directory;
		$this->urltoroot=$urltoroot;
	}


	public function match_request($request)
	{
		return $request == 'compare-results';
	}


	public function process_request($request)
	{

		require_once QA_INCLUDE_DIR.'qa-db.php';

		$qa_content=qa_content_prepare();
		$query = "select distinct concat(version,': ', platform) as platform from ^mlcommons_inference_results order by platform";// where scenario = '$scenario' and division='$division' and mlperfmodel='$model' and systemtype like '%$category%'";
		$raw_result = qa_db_query_sub($query);
		$platforms1 = qa_db_read_all_values($raw_result);
		$platforms = array();
		for($i = 0; $i < count($platforms1); $i++) {
			$platforms["$i"] = $platforms1[$i];
		}
		#print_r($platforms);

		$qa_content['title']=qa_lang_html('visualizer/page_title');

		$categories = array(
			"0" => "edge",
			"1" => "datacenter"
		);
		$divisions = array(
			"0" => "closed",
			"1" => "open",
			"2" => "network",
		);
		$scenarios = array(
			"0" => "Offline",
			"1" => "Server",
			"2" => "SingleStream",
			"3" => "MultiStream",
		);
		$models_all = array(
			"0" => "All models",
			"1" => "resnet",
			"2" => "retinanet",
			"3" => "bert-99",
			"4" => "bert-99.9",
			"5" => "rnnt",
			"6" => "gptj-99",
			"7" => "gptj-99.9",
			"8" => "dlrm_v2-99",
			"9" => "dlrm_v2-99.9",
			"10" => "3d-unet-99",
			"11" => "3d-unet-99.9",
		);

		if (qa_clicked('okthen'))
		{
			$selected_model_ids = $_POST['models'];
			if(in_array(0, $selected_model_ids)) {
				$modelfilterstring = "";
			}
			else {
				$modelfilterstring = " and mlperfmodel in ('";
				$selectedmodels = array();
				foreach($selected_model_ids as $model_id) {
					$selectedmodels[] = $models_all[$model_id];
				}
				$modelfilterstring .= implode("','",$selectedmodels);;
				$modelfilterstring .= "')";
			}
			/*PROCESS!!!!!!*/
			$system1=$platforms[qa_post_text('system1')];
			$system2=$platforms[qa_post_text('system2')];
			$sys1array=explode(":", $system1);
			$system1=trim($sys1array[1]);
			$sysversion1 = trim($sys1array[0]);
			$sys2array=explode(":", $system2);
			$system2=trim($sys2array[1]);
			$sysversion2 = trim($sys2array[0]);

		}
		else {
			$system1 = "L4x1_TRT";
			$system2 = "L4x1_TRT";
			$sysversion1 = "v3.0";
			$sysversion2 = "v3.1";
		}
		$scenarios  = array(
			"Offline",
			"Server",
			"SingleStream",
			"MultiStream"
		);
		$ytitle_scenarios = array(
			"Offline" => "Samples per Second",
			"Server" => "Samples per Second",
			"SingleStream" => "Latency per sample in milliseconds",
			"MultiStream" => "Latency per query of 8 samples in milliseconds",
		);
		$qa_content['custom_0'] =  "
			<script type='text/javascript'>
var data1 = {}, data2 = {}, draw_power = {}, draw_power_efficiency = {}, ytitle = {}, sortcolumnindex, perfsortorder;
</script>
";
		$customid = 1;
		foreach ($scenarios as $scenario) {

			$query = "select * from ^mlcommons_inference_results where platform = '$system1' and scenario='$scenario' and version='$sysversion1' $modelfilterstring"; //and division='$division' and mlperfmodel='$model' and systemtype like '%$category%'";
			$raw_result = qa_db_query_sub($query);
			$result1 = qa_db_read_all_assoc($raw_result);
			if(count($result1) == 0) continue;
			$query = "select * from ^mlcommons_inference_results where platform = '$system2' and scenario='$scenario' and version='$sysversion2' $modelfilterstring"; //and division='$division' and mlperfmodel='$model' and systemtype like '%$category%'";
			$raw_result = qa_db_query_sub($query);
			$result2 = qa_db_read_all_assoc($raw_result);
			if(count($result2) == 0) continue;

			$is_power = ($result2[0]['power_result'] != 0);
			if(!$is_power)
			{
				$power_string = "false";
			}
			else {
				$power_string = "true";
			}
			// = $result2[0]['power_result'] != 0;


			$data1 = "$sysversion1: $system1";
			$data2 = "$sysversion2: $system2";
			$ytitle = $ytitle_scenarios[$scenario];
			$qa_content['custom_'.$customid] =  "
<script type='text/javascript'>
data1['$scenario'] = '$data1', data2['$scenario'] = '$data2', draw_power['$scenario'] = $power_string, draw_power_efficiency['$scenario'] = $power_string,
ytitle['$scenario'] = '$ytitle',
sortcolumnindex = 4, perfsortorder = 1;
</script>";

			//print_r($result2);
			$models = array();
			$result2_models = array();
			$result_scenarios = array();
			for($i = 0; $i < count($result2); $i++) {
				$row = $result2[$i];
				if(!in_array($row['model'], $result2_models)) {
					array_push($result2_models, $row['model']);
				}
			}
			for($i = 0; $i < count($result1); $i++) {
				$row = $result1[$i];
				if(!in_array($row['model'], $models) and in_array($row['model'], $result2_models)) {
					array_push($models, $row['model']);
				}
			}
			//print_r($models);
			//print_r($result1);
			$results1 = array();
			$results2 = array();
			foreach($models as $model) {
			    foreach($result1 as $row) {
				if($row['model'] == $model)
				    $results1[$model] = $row;
			    }
			    foreach($result2 as $row) {
				if($row['model'] == $model)
					$results2[$model] = $row;
			    }
			}
			$tableposthtml =       	'<!-- pager --> 
			    <div class="pager"> 
			    <img src="https://mottie.github.io/tablesorter/addons/pager/icons/first.png" class="first"/> 
			    <img src="https://mottie.github.io/tablesorter/addons/pager/icons/prev.png" class="prev"/> 
			    <span class="pagedisplay"></span> <!-- this can be any element, including an input --> 
			    <img src="https://mottie.github.io/tablesorter/addons/pager/icons/next.png" class="next"/> 
			    <img src="https://mottie.github.io/tablesorter/addons/pager/icons/last.png" class="last"/> 
			    <select class="pagesize" title="Select page size"> 
			    <option selected="selected" value="10">10</option> 
			    <option value="20">20</option> 
			    <option value="30">30</option> 
			    <option value="all">All</option> 
			    </select>
			    <select class="gotoPage" title="Select page number"></select>
			    </div>';
			$html = "<h2>Comparing $scenario scenario for $data1 and $data2 </h2>".$tableposhtml;


			$htmltable  = $this -> construct_table($scenario, $models, $data1, $data2, $is_power, $results1, $results2);
			$html .= $htmltable;
			$html .= $tableposthtml;
			$qa_content['custom_'.$customid] .= $html;

			$resultjson = json_encode($result);

			$qa_content['custom_'.$customid] .= '
			    <div id="chartContainer'.$scenario.'1" class="bgtext" style="height: 370px; width: 100%;"></div>
			    <button class="btn btn-primary"  id="printChart'.$scenario.'1">Download</button>';
			if($is_power) {
			    $qa_content['custom_'.$customid] .= '

				<div id="chartContainer'.$scenario.'2" class="bgtext" style="height: 370px; width: 100%;"></div>
				<button class="btn btn-primary"  id="printChart'.$scenario.'2">Download</button>
				<div id="chartContainer'.$scenario.'3" class="bgtext" style="height: 370px; width: 100%;"></div>
				<button class="btn btn-primary"  id="printChart'.$scenario.'3">Download</button>

';
			}
			$customid++;
		}

		$user_level = qa_get_logged_in_level();
		$ok = null;
		$fields = array();
		/*$systemshtml = "<select name='systems[]' class='col' multiple>\n";
		foreach ($platforms as $key=> $value){
			$systemshtml .= "<option value='$key'>$value</option>\n";
		}
		$systemshtml .= "</select>";

		$modelshtml = "<select name='models' class='col' multiple>\n";
		foreach ($models as $key=> $value){
			$modelshtml .= "<option value='$key'>$value</option>\n";
		}
		$modelshtml .= "</select>";
		 */
		/*$fields[] = array(
			'label' => 'Systems',
			'type'=>'custom',
			'html' => $systemshtml,
		);*/
		$fields[] = array(
			'label' => 'System 1',
			'type'=>'select',
			'tags' => "id='system1' name='system1' class='col'",
			'options' => $platforms,
			'value' => $data1
		);
		$fields[] = array(
			'label' => 'System 2',
			'type'=>'select',
			'tags' => "id='system2' name='system2' class='col'",
			'options' => $platforms,
			'value' => $data2
		);
		if(!isset($modelsdata)) {
			$modelsdata = "All models";
		}
		$fields[] = array(
			'label' => 'Models',
			'type'=>'select',
			'tags' => "id='models' name='models[]' class='col' multiple",
			'options' => $models_all,
			'value' => $modelsdata
		);


		$qa_content['form']=array(
			'tags' => 'method="post" action="'.qa_self_html().'"',

			'style' => 'wide',
			'ok' => ($ok && !isset($error)) ? $ok : null,

			'title' => qa_lang_html('visualizer/form_title'),

			'fields' => $fields,

			'buttons' => array(
				'ok' => array(
					'tags' => 'name="okthen"',
					'label' => 'Show Results',
					'value' => '1',
				),
			),

		);

		return $qa_content;
	}
	function construct_table($scenario, $models, $data1, $data2, $is_power, $results1, $results2) {
		$html = "<table class=\"tablesorter\" id=\"results_$scenario\">";
		$html .= "<thead> <tr>";
		$tableheader = "

			<th>Model</th>
			<th>$data1</th>
			<th>$data2</th>
			<th>Performance Delta</th>";

		if($is_power) {
			$tableheader .= "
			<th>Power 1</th>
			<th>Power 2</th>
			<th>Power Delta</th>
			<th>Power Efficiency 1</th>
			<th>Power Efficiency 2</th>
			<th>Power Efficiency Delta</th>";
		}
		$tableheader .="
			</tr>";
		$html .= $tableheader."
			</thead>";
		$html .= "<tfoot> <tr>".$tableheader."
			</tr>
			</tfoot>";

		$performance_title = "Samples per Second";
		foreach($models as $row) {
			$html.= "<tr>";
			$html .= "<td class='model'>".$row  ."</td>";
			$perf1 = $results1[$row]['performance_result'];
			$perf2 = $results2[$row]['performance_result'];
			if($perf2)
				$perfdelta = round(((1-$perf1/ $perf2)), 4) * 100;
				$html .= "<td>". $perf1. "</td>";
				$html .= "<td>". $perf2. "</td>";
				$html .= "<td>". $perfdelta. "</td>";
			if($is_power) {
				$pow1 = $results1[$row]['power_result'];
				$pow2 = $results2[$row]['power_result'];
				if($pow2)
					$powdelta = round(((1-$pow1/ $pow2)), 4) * 100;
				if($pow1)
					$peff1 = round($perf1/$pow1, 5);
				else
					$peff1 = "";	
				if($pow2)
					$peff2 = round($perf2/$pow2, 5);
				else
					$peff2 = "";	
				$html .= "<td>". $pow1. "</td>";
				$html .= "<td>". $pow2. "</td>";
				$html .= "<td>". $powdelta. "</td>";

				$html .= "<td>". $peff1. "</td>";
				$html .= "<td>". $peff2. "</td>";
				if($peff2)
					$peffdelta = round(((1-$peff1/ $peff2)),4) * 100;
				else $peff2="";
				$html .= "<td>". $peffdelta. "</td>";
			}
			$html .= "</tr>";

		}
		$html .= "</table>";
		return $html;
	}
}
