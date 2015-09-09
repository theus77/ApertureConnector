<?php
App::uses('ApertureConnectorAppModel', 'ApertureConnector.Model');
/**
 * ImageProxyState Model
 *
 */
class ImageProxyState extends ApertureConnectorAppModel {
	public $useDbConfig = 'apertureImageProxies';
	public $useTable = 'RKImageProxyState';
	public $primaryKey = 'modelId';
	//public $recursive = -1;
	//public $actsAs = array('Containable');
}
