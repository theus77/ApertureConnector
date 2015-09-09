<?php 

class PLace extends ApertureConnectorAppModel {
	public $useDbConfig = 'apertureProperties';
	public $useTable = 'RKPLace';
	public $primaryKey = 'modelId';
	public $recursive = -1;
	public $actsAs = array('Containable');
	
	public $hasMany = array(
			'PlaceName' => array(
					'className' => 'ApertureConnector.PlaceName',
					'foreignKey' => 'placeid',
					'dependent' => false
			)
	);

}

?>