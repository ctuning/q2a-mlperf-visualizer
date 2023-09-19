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

class qa_show_results
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
		return $request == 'show-results';
	}


	public function process_request($request)
	{

		require_once QA_INCLUDE_DIR.'qa-db.php';

		$qa_content=qa_content_prepare();
		$qa_content['title']=qa_lang_html('visualizer/page_title');


		$query = "select distinct concat(version,': ', platform) as platform from ^mlcommons_inference_results order by platform desc";
		$raw_result = qa_db_query_sub($query);
		$platforms1 = qa_db_read_all_values($raw_result);
		$platforms = array("Select All Platforms");
		for($i = 0; $i < count($platforms1); $i++) {
			array_push($platforms, $platforms1[$i]);
		}

		$query = "select distinct concat(accelerator_model_name, ' x ', accelerators_per_node) as device from ^mlcommons_inference_results where accelerators_per_node > 0  order by device";
		$raw_result = qa_db_query_sub($query);
		$devices1 = qa_db_read_all_values($raw_result);
		$devices = array("Select All Devices");
		for($i = 0; $i < count($devices1); $i++) {
			array_push($devices, $devices1[$i]);
		}

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
		$metrics = array(
			"0" => "Peak Performance",
			"1" => "Power efficiency",
			"2" => "Performance per accelerator",
			"3" => "Performance per core",
		);
		$versions = array(
			"0" => "v3.1",
			"1" => "v3.0"
		);
		$models = array(
			"0" => "resnet",
			"1" => "retinanet",
			"2" => "bert-99",
			"3" => "bert-99.9",
			"4" => "rnnt",
			"5" => "gptj-99",
			"6" => "gptj-99.9",
			"7" => "dlrm-v2-99",
			"8" => "dlrm-v2-99.9",
			"9" => "3d-unet-99",
			"10" => "3d-unet-99.9",
		);

		if (qa_clicked('okthen'))
		{
			/*PROCESS!!!!!!*/
			$category=$categories[qa_post_text('category')];
			$scenario=$scenarios[qa_post_text('scenario')];
			$model=$models[qa_post_text('model')];
			$division=$divisions[qa_post_text('division')];
			$metric=$metrics[qa_post_text('metric')];
			$version=$versions[qa_post_text('version')];

			$selected_platform_ids = $_POST['platforms'];
			$platformdata = array();
			if(in_array(0, $selected_platform_ids)) {
				$platformfilterstring = "";
			}
			else {
				$platformfilterstring = " and platform in ('";
				$selectedplatforms = array();
				foreach($selected_platform_ids as $platform_id) {
					array_push($platformdata, $platforms[$platform_id]);
					$selectedplatforms[] = trim(explode(":", $platforms[$platform_id])[1]);
				}
				$platformfilterstring .= implode("','",$selectedplatforms);;
				$platformfilterstring .= "')";
			}

			$selected_device_id = qa_post_text('device');;
			if(0 == $selected_device_id) {
				$devicefilterstring = "";
			}
			else {
				$device = $devices[$selected_device_id];
				$device_parts = explode(" x ", $device);
				$accelerator_model_name = trim($device_parts[0]);
				$accelerators_per_node = trim($device_parts[1]);
				$devicefilterstring = " and accelerator_model_name = '$accelerator_model_name' and accelerators_per_node = $accelerators_per_node";
			}
		}
		else {
			$category = "edge";
			$division = "closed";
			$model = "resnet";
			$scenario = "Offline";
			$metric = "Performance";
			$version = "v3.1";
		}
		//	$version = "v3.1";
		$charttitlesuffix = " for $model $scenario scenario in $division division $category category";

		if(($scenario == "Offline") || ($scenario == "Server")) {
			$chart1ytitle = "Samplpes per second";
			$sortorder = "desc";
			$perfsortorder = 1;
		}
		else {
			$sortorder = "asc";
			$perfsortorder = 0;
			if($scenario == "SingleStream") {
				$chart1ytitle = "Latency per sample";
			}
			elseif($scenario == "MultiStream") {
				$chart1ytitle = "Latency per query of 8 samples";
			}
		}
		$orderby = " order by performance_result $sortorder";
		$device_column_name = "Device";
		$device_count_column_name = "Devices per node";
		$sortcolumnindex = 6;
		$perfcolumnindex = 7; //starting from 1
		if($metric == 'Performance') {
			$filter = "";
			$additional_metric_column_name = "";
		}
		elseif($metric == 'Power efficiency') {
			$filter = " and power_result > 0";
			$additional_metric_column_name = "Samples per Joule";
			$chart2title = "Power efficiency $charttitlesuffix";
			$chart2ytitle= "Samples per Joule";
			$sortcolumnindex = 7;
			$perfsortorder = 1;
		}
		elseif($metric == 'Performance per accelerator') {
			$filter = " and accelerators_per_node > 0";
			if($scenario == "Offline") {
				$additional_metric_column_name = "Performance per accelerator";
				$chart2title = "Performance per accelerator $charttitlesuffix";
				$chart2ytitle= "Samples per second per accelerator";
				$sortcolumnindex = 7;
			}

		}
		elseif($metric == 'Performance per core') {
			$filter = " and accelerators_per_node = 0";
			$device_column_name = "Processor";
			$device_count_column_name = "Total Physical Cores";
			if($scenario == "Offline") {
				$additional_metric_column_name = "Performance per core";
				$chart2title = "Performance per core $charttitlesuffix";
				$chart2ytitle= "Samples per second per core";
				$sortcolumnindex = 7;
			}

		}
		if($division == "open") {
			$sortcolumnindex+=2;
			$perfcolumnindex+=2;
			$openmodel = "true";
		}
		else{
			$openmodel = "false";
		}
		$qa_content['custom_0'] =  "
			<script type='text/javascript'>
var chart1title = 'Performance $charttitlesuffix', chart2title = '$chart2title', chart3title = 'Accuracy vs Performance', chart1ytitle = '$chart1ytitle', chart2ytitle = '$chart2ytitle', chart3ytitle = 'Performance', perfsortorder = $perfsortorder, sortcolumnindex = $sortcolumnindex, perfcolumnindex = $perfcolumnindex, openmodel=$openmodel, model='$model';
</script>";
		$query = "select * from ^mlcommons_inference_results where version = '$version' and scenario = '$scenario' and division='$division' and mlperfmodel='$model' and systemtype like '%$category%' $platformfilterstring $devicefilterstring $filter $orderby";
		//$query = "select * from ^mlcommons_inference_results where scenario = '$scenario' and division='$division' and mlperfmodel='$model' and systemtype like '%$category%' $filter $orderbyc";
		//echo "$query";
		$raw_result = qa_db_query_sub($query);
		$result = qa_db_read_all_assoc($raw_result);

		$html = '		<div class="pager"> 
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
			</div>
';


		$theader = "<th>Platform</th>
			<th>Submitter</th>
			<th>System</th>
			<th>$device_column_name</th>
			<th>$device_count_column_name</th>
			<th>Framework</th>";
		if($division == "open") {
			$theader .= "<th> Model </th>";
			$theader .= "<th> Accuracy </th>";
		}
		$theader .= "<th>Performance</th>";
		if($additional_metric_column_name) {
			$theader .= "<th>$additional_metric_column_name</th>";
		}
		$html .= "<table class=\"tablesorter\" id=\"results\">";
		$html .= "<thead> <tr> $theader";
			$html .= "</tr>
			</thead>";
		$html .= "<tfoot> <tr>
			$theader
			</tfoot>";

		$performance_title = "Samples per Second";
		foreach($result as $row) {
			$html.= "<tr>";
			$platform = $row['platform'];
			$resultid = $row['result_id'];
			$location = "https://github.com/mlcommons/inference_results_".$version."/tree/main/".$row['Location'];
			$html .= "<td title=\"$resultid\" class='location'>". "<a target=\"_blank\" href=\"$location\">$platform</a>". "</td>";
			$html .= "<td>". $row['organization']. "</td>";
			$html .= "<td>". $row['systemname']. "</td>";
			if ( $row['accelerators_per_node'] == 0) {
			$cores = $row['number_of_nodes'] * $row['host_processors_per_node'] * $row['host_processor_core_count'];
			$html .= "<td>". $row['host_processor_model_name']. "</td>";
			$html .= "<td>". $cores. "</td>";
			}	
			else{
			$html .= "<td>". $row['accelerator_model_name']. "</td>";
			$html .= "<td>". $row['accelerators_per_node']. "</td>";
			}
			$html .= "<td>". $row['framework']. "</td>";
			if($division == "open") {
				$html .= "<td>". $row['model']. "</td>";
				$html .= "<td>". $row['accuracy']. "</td>";
			}
			$html .= "<td class='performance' title='$performance_title'>". $row['performance_result']. "</td>";
			if ($additional_metric_column_name) {
				if($metric == "Power efficiency") {
					if($scenario == "Offline" || $scenario == "Server")
					$power_efficiency = round($row['performance_result']/$row['power_result'], 2);
					elseif($scenario == "SingleStream")
					$power_efficiency = round(1000/$row['power_result'], 2);
					elseif($scenario == "MultiStream")
					$power_efficiency = round(8000/$row['power_result'], 2);
					$html .= "<td class='power' title='Total Watts: ". $row['power_result']. "'>". $power_efficiency  ."</td>";
				}
				elseif($metric == "Performance per accelerator") {
					if ($row['accelerators_per_node'] > 0) {
						$value = round($row['performance_result']/$row['accelerators_per_node'], 2);
					}
					else {
						$value = "0";
					}
					$html .= "<td class='power'> ". $value  ."</td>";
				}
				elseif($metric == "Performance per core") {
					$value = round($row['performance_result']/$cores, 2);
					//$value = 0;//round($row['performance_result']/$row['power_result'], 2);
					$html .= "<td class='power'>". $value  ."</td>";
				}

			}
			$html .= "</tr>";

		}
		$html .= "</table>";

		$html .= '<!-- pager --> 
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

		$qa_content['custom'] = $html;

		$qa_content['custom_1'] = '<div id="chartContainer1" class="bgtext" style="height: 370px; width: 100%;"></div>
<button class="btn btn-primary"  id="printChart1">Download</button>';

		if ($additional_metric_column_name) {
			$qa_content['custom_2'] = '<div id="chartContainer2" class="bgtext" style="height: 370px; width: 100%;"></div>
<button class="btn btn-primary"  id="printChart2">Download</button>';
		}
		if ($openmodel) {
			$qa_content['custom_3'] = '<div id="chartContainer3" class="bgtext" style="height: 370px; width: 100%;"></div>
<button class="btn btn-primary"  id="printChart3">Download</button>';
		}

		//$qa_content['custom_9'] = $html2;

		$user_level = qa_get_logged_in_level();

		$ok = null;
		$fields = array();
		$fields[] = array(
		    'label' => 'Submission Version',
		    'type'=>'select',
		    'tags' => "id='version_selector' name='version' class='col'",
		    'options' => $versions,
		    'value' => $version
		);
		$fields[] = array(
		    'label' => 'Submission Category',
		    'type'=>'select',
		    'tags' => "id='category_selector' name='category' class='col'",
		    'options' => $categories,
		    'value' => $category
		);
		$fields[] = array(
		    'label' => 'Submission Division',
		    'type'=>'select',
		    'tags' => "id='division_selector' name='division' class='col'",
		    'options' => $divisions,
		    'value' => $division
		);
		$fields[] = array(
		    'label' => 'Model',
		    'type'=>'select',
		    'tags' => "id='model_selector' name='model' class='col'",
		    'options' => $models,
		    'value' => $model
		);
		$fields[] = array(
		    'label' => 'Scenario',
		    'type'=>'select',
		    'tags' => "id='scenario_selector' name='scenario' class='col'",
		    'options' => $scenarios,
		    'value' => $scenario
		);
		$fields[] = array(
		    'label' => 'Metric',
		    'type'=>'select',
		    'tags' => "id='metric_selector' name='metric' class='col'",
		    'options' => $metrics,
		    'value' => $metric
		);
		if(!isset($platformdata) || false) {
			$platformdata = "Select All Platforms";
		}
		$platformdata = "Select All Platforms";

		$fields[] = array(
			'label' => 'Filter Platforms',
			'type'=>'select',
			'tags' => "id='platforms' name='platforms[]' class='col' multiple size='30'",
			'options' => $platforms,
			'value' => $platformdata
		);
		if(!isset($device)) {
			$device = "Select All Devices";
		}

		$fields[] = array(
			'label' => 'Filter Devices',
			'type'=>'select',
			'tags' => "id='device' name='device' class='col'",
			'options' => $devices,
			'value' => $device
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
}
