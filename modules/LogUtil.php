<?php

/**
 * Created on 2016/11/02 by Gary Liu
 * $Id: LogUtil.php,v 1.13 2016/10/02 11:38:46 alpha Exp $
 */

class LogUtil {

	public static function jsonStrToObj($jsonStr) {
		if (is_string($jsonStr) && strlen($jsonStr) > 0) {
			$jsonObj = json_decode(urldecode($jsonStr));
			if (json_last_error() != 0) {
				failAndExit("LogUtil::jsonStrToObj() Error: " . json_last_error_msg());
			}
			return $jsonObj;
		}
		failAndExit("LogUtil::jsonStrToObj() Error: parameter 'jsonStr' is not a string or its length is 0.");
	}

	public static function jsonObjToStr($jsonObj) {
		if (is_object($jsonObj) && $jsonObj != null) {
			$jsonStr = json_encode($jsonObj);
			if (json_last_error() != 0) {
				failAndExit("LogUtil::jsonObjToStr() Error: " . json_last_error_msg());
			}
			return $jsonStr;
		}
		failAndExit("LogUtil::jsonObjToStr() Error: parameter 'jsonObj' is not an object or its value is null.");
	}

	public static function addUserAgent($jsonObj) {
		if (is_object($jsonObj) && $jsonObj != null &&
			isset($_SERVER['HTTP_USER_AGENT']) &&
			strlen($_SERVER['HTTP_USER_AGENT']) > 0) {
			$jsonObj->userAgent =& $_SERVER['HTTP_USER_AGENT'];
			return true;
		} 
		failAndExit("LogUtil::addUserAgent() Error: parameter 'jsonObj' is not an object or its value is null.");
	}

	public static function addRemoteAddress($jsonObj) {
		if (is_object($jsonObj) && $jsonObj != null &&
			isset($_SERVER['REMOTE_ADDR']) &&
			strlen($_SERVER['REMOTE_ADDR']) > 0) {
			$jsonObj->remoteAddress =& $_SERVER['REMOTE_ADDR'];
			return true;
		} 
		failAndExit("LogUtil::addRemoteAddress() Error: parameter 'jsonObj' is not an object or its value is null.");
	}

	public static function sendLogToKafka($jsonStr) {
		global $config;

		$rk = new RdKafka\Producer();
		$rk->setLogLevel(LOG_DEBUG);
		$rk->addBrokers($config['KAFKA_BROKERS']);
		$topic = $rk->newTopic($config['KAFKA_TOPIC']);
		$topic->produce(RD_KAFKA_PARTITION_UA, 0, $jsonStr);
	}

}

