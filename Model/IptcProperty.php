<?php 

class IptcProperty extends ApertureConnectorAppModel {
	public $useDbConfig = 'apertureProperties';
	public $useTable = 'RKIptcProperty';
	public $primaryKey = 'modelId';
	public $recursive = -1;
	public $actsAs = array('Containable');
		
	public $belongsTo = array(
			'UniqueString' => array(
					'className' => 'ApertureConnector.UniqueString',
					'foreignKey' => 'stringid'
			)
	);
	
}

?>