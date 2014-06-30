<?php

function generate_self_url($change_rule) {
	$url = Config::get('site_url') . $_SERVER['PHP_SELF'] . '?';
	$param_list = array('name', 'frequency', 'range', 'data_type', 'cumulate');

	$first = true;
	foreach ($param_list as $param) {
		if ($first) {
			$first = false;
		} else {
			$url .= '&';
		}
		if (isset($change_rule[$param])) {
			$url .= $param . '=' . $change_rule[$param];
		} else {
			$url .= $param . '=' . Input::get($param);
		}
	}

	return $url;
}