<?php

App::uses('AppModel', 'Model');

class ApertureConnectorAppModel extends AppModel {
	/**
	 * Convert an url encoded uuid in to an Aperture encoded uuid
	 * http://stackoverflow.com/questions/695438/safe-characters-for-friendly-url
	 * http://www.ietf.org/rfc/rfc3986.txt
	 * @param  url encoded $inUuid (string)
	 * @return an Aperture encoded uuid (string)
	 */
	public static function decodeUuid($inUuid){
		return str_replace('-', '+', str_replace('_', '%', $inUuid));
	}
	
	/**
	 * Convert a Aperture encoded uuid in to a url encoded uuid
	 * http://stackoverflow.com/questions/695438/safe-characters-for-friendly-url
	 * http://www.ietf.org/rfc/rfc3986.txt
	 * @param  an Aperture encoded uuid $inUuid (string)
	 * @return url encoded uuid (string)
	 */
	public static function encodeUuid($inUuid){
		return str_replace('+', '-', str_replace('%', '_', $inUuid));
	}
	
	/**
	 * Convert an apple timestamp in to a UNIX timestamp
	 * @param unknown $appleDate
	 * @return UNIX timestamp (integer)
	 */
	public static function convertFromAppleDate($appleDate){
		return $appleDate+mktime(0, 0, 0, 1, 1, 2001);
	}
	
	/**
	 * Convert an apple timestamp in to a UNIX timestamp
	 * @param unknown $appleDate
	 * @return UNIX timestamp (integer)
	 */
	public static function convertToAppleDate($date){
		return $date-mktime(0, 0, 0, 1, 1, 2001);
	}
}
