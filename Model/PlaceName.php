<?php 

class PLaceName extends ApertureConnectorAppModel {
	public $useDbConfig = 'apertureProperties';
	public $useTable = 'RKPlaceName';
	public $primaryKey = 'modelId';
	public $recursive = -1;
	public $actsAs = array('Containable');
	
	public $belongsTo = array(
			'Place' => array(
					'className' => 'ApertureConnector.Place',
					'foreignKey' => 'placeid'
			)
	);

}

?>