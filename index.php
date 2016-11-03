<?php

/**
 * Created on 2016/11/02 by Gary Liu
 * $Id: collect.php,v 1.13 2016/10/02 11:38:46 alpha Exp $
 */

if ( isset($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'], $_SERVER['PATH_INFO'])) {

	require_once('config.php');
	require_once('common.php');

	switch ($_SERVER['PATH_INFO']) {
		case '/0.0.1/collector.js':
			require_once('0.0.1/collector_js.php');
			break;
		case '/collect':
			require_once('collect.php');
			break;
		default:
			echo '';
	}
}

