<?php 

class Master extends ApertureConnectorAppModel {
	public $useDbConfig = 'aperture';
	public $useTable = 'RKMaster';
	public $primaryKey = 'uuid';
	public $recursive = -1;
	public $actsAs = array('Containable');
	
	public $belongsTo = array(
			'ImportGroup' => array(
					'className'    => 'ApertureConnector.ImportGroup',
					'foreignKey'   => 'importGroupUuid'
			)
	);
}

?>