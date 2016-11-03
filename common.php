<?php

/**
 * Created on 2016/11/02 by Gary Liu
 * $Id: common.php,v 1.13 2016/10/02 11:38:46 alpha Exp $
 */

/*
 * For PHP 5.4 or below
 * Ref:http://php.net/manual/en/function.json-last-error-msg.php
 */
if (!function_exists('json_last_error_msg')) {

	function json_last_error_msg() {
		static $ERRORS = array(
			JSON_ERROR_NONE => 'No error',
			JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
			JSON_ERROR_STATE_MISMATCH => 'State mismatch (invalid or malformed JSON)',
			JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
			JSON_ERROR_SYNTAX => 'Syntax error',
			JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded'
		);

		$error = json_last_error();
		return isset($ERRORS[$error]) ? $ERRORS[$error] : 'Unknown error';
	}
}

function failAndExit($failedMsg = '') {
	global $config;

	if (strlen($failedMsg) > 0) {
		$msg = "FAILED: " . $failedMsg;
		if ($config['DEBUG']) {
			echo $msg;
		}
		error_log($msg);
	}
	exit();
}

