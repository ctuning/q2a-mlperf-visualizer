<?php
require_once '/var/www/html/ck/qa-include/qa-base.php';
require_once QA_INCLUDE_DIR.'qa-db.php';
require_once QA_INCLUDE_DIR.'qa-db-notices.php';
require_once QA_INCLUDE_DIR.'db/metas.php';
require_once QA_INCLUDE_DIR.'app/emails.php';

$version="v3.0";
$results = file_get_contents('summary.json');
$decoded_json = json_decode($results, true);
//print_r($decoded_json);
$insert_query = "insert into ^mlcommons_inference_results (organization, avaiability, division, systemtype, systemname, platform, model, mlperfmodel, number_of_nodes, host_processor_model_name, host_processors_per_node, host_processor_core_count, accelerator_model_name, accelerators_per_node, Location, framework, operating_system, scenario, accuracy, performance_result, performance_units, power_result, power_units, version) values ($,$, $,$,$,$,$,$,#,$,#,#,$,#,$,$,$,$,#,#,$,#,$, $) on duplicate key update ";
for($i = 0; $i < count($decoded_json); $i++) {
	//qa_db_query_sub($insert_query,
	$result = $decoded_json[$i];

	$organization = $result['Organization'];
	$availability = $result['Availability'];
	$division = $result['Division'];
	$systemtype = $result['SystemType'];
	$systemname = $result['SystemName'];
	$platform = $result['Platform'];
	$model = $result['Model'];
	$mlperfmodel = $result['MlperfModel'];
	$number_of_nodes = $result['number_of_nodes'];
	$host_processor_model_name = $result['host_processor_model_name'];
	$host_processors_per_node = $result['host_processors_per_node'];
	$host_processor_core_count = $result['host_processor_core_count'];
	if($host_processor_core_count == "4 (big); 4 (LITTLE)") {
		$host_processor_core_count = 8;
	}
	elseif($host_processor_core_count == "6 (4 big; 2 LITTLE)") {
		$host_processor_core_count = 6;
	}

	$accelerator_model_name = $result['accelerator_model_name'];
	$accelerators_per_node = $result['accelerators_per_node'];
	$location = $result['Location'];
	$framework = $result['framework'];
	$operating_system = $result['operating_system'];
	$scenario = $result['Scenario'];
	$raw_result = $result['Result'];
	$units = $result['Units'];
	$accuracy = $result['Accuracy'];
	$version = $result['version'];
	if (strpos($units, "Watts") !== false or strpos($units, "joules") !== false) {
		//power result
		$power_result = $raw_result;
		$power_units = $units;
		$performance_result = null;
		$performance_units = null;
		$onduplicate_string = " power_result=#, power_units = $";
		$onduparg1 = $power_result;
		$onduparg2 = $power_units;
	}
	else{
		$power_result = null;
		$power_units = null;
		$performance_result = $raw_result;
		$performance_units = $units;
		$onduplicate_string = " performance_result=#, performance_units = $";
		$onduparg1 = $performance_result;
		$onduparg2 = $performance_units;
	}

	qa_db_query_sub($insert_query.$onduplicate_string,
	$organization,
	$availability,
	$division,
	$systemtype,
	$systemname,
	$platform,
	$model,
	$mlperfmodel,
	$number_of_nodes,
	$host_processor_model_name,
	$host_processors_per_node,
	$host_processor_core_count,
	$accelerator_model_name,
	$accelerators_per_node,
	$location,
	$framework,
	$operating_system,
	$scenario,
	$accuracy,
	$performance_result,
	$performance_units,
	$power_result,
	$power_units,
	$version,
	$onduparg1,
	$onduparg2);


}
