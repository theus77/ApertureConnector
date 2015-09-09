<?php 

class OtherProperty extends ApertureConnectorAppModel {
	public $useDbConfig = 'apertureProperties';
	public $useTable = 'RKOtherProperty';
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