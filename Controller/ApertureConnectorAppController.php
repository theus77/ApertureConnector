<?php

App::uses('AppController', 'Controller');

class ApertureConnectorAppController extends AppController {

	public static function decodeUuid($inUuid){
		return str_replace('_', '%', $inUuid);
	}
	
	public static function convertAppleDate($appleDate){
		return $appleDate+mktime(0, 0, 0, 1, 1, 2001);
	}

	public static function encodeUuid($inUuid){
		return str_replace('%', '_', $inUuid);
	}
}
