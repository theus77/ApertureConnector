<?php 

//App::uses('ApertureModel', 'ApertureConnector.ApertureModel');

class Folder extends ApertureConnectorAppModel {
	public $useDbConfig = 'aperture';
	public $useTable = 'RKFolder';
	public $primaryKey = 'uuid';
	//public $recursive = -1;
	public $actsAs = array('Containable');

	public $virtualFields = array('encodedUuid' => "replace(folder.uuid, '%', '_')");
	
	public $hasMany = array(
			'Version' => array(
					'className' => 'ApertureConnector.Version',
					'foreignKey' => 'Projectuuid',
					//'dependent' => false
			)	
	);
}
?>