<?php 

class UniqueString extends ApertureConnectorAppModel {
	public $useDbConfig = 'apertureProperties';
	public $useTable = 'RKUniqueString';
	public $primaryKey = 'modelId';	
// 	public $recursive = -1;
// 	public $actsAs = array('Containable');
}

?>