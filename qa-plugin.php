<?php
/*
	Question2Answer by Gideon Greenspan and contributors
	http://www.question2answer.org/

	File: qa-plugin/example-page/qa-plugin.php
	Description: Initiates example page plugin


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

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

        qa_register_plugin_layer('qa-visualizer-layer.php', 'Visualizer Layer');
qa_register_plugin_module('page', 'qa-show-results.php', 'qa_show_results', 'Show Results');
qa_register_plugin_module('page', 'qa-compare-results.php', 'qa_compare_results', 'Compare Results');
qa_register_plugin_phrases('qa-visualizer-lang-*.php', 'visualizer');



