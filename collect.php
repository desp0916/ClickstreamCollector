<?php

/**
 * Created on 2016/11/02 by Gary Liu
 * $Id: collect.php,v 1.13 2016/10/02 11:38:46 alpha Exp $
 */

if (isset($_GET['id']) && in_array($_GET['id'], $config['CC_IDS']) && isset($_GET['json'])) {

	require_once('modules/LogUtil.php');
	require_once('modules/Collector.php');

	Collector::process($_GET['json']);
}

?>
