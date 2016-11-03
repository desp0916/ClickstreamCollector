<?php

/**
 * Created on 2016/11/02 by Gary Liu
 * $Id: Collector.php,v 1.13 2016/10/02 11:38:46 alpha Exp $
 */

class Collector {

	public static function process($input) {
		$jsonObj = LogUtil::jsonStrToObj($input);
		LogUtil::addUserAgent($jsonObj);
		LogUtil::addRemoteAddress($jsonObj);
		$jsonStr = LogUtil::jsonObjToStr($jsonObj);
		LogUtil::sendLogToKafka($jsonStr);
	}

}

