<?php 

class ExifNumberProperty extends ApertureConnectorAppModel {
	public $useDbConfig = 'apertureProperties';
	public $useTable = 'RKExifNumberProperty';
	public $primaryKey = 'modelId';
	//public $recursive = -1;
	//public $actsAs = array('Containable');	
}

?>