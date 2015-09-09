<?php 

class PlaceForVersion extends ApertureConnectorAppModel {
	public $useDbConfig = 'aperture';
	public $useTable = 'RKPlaceForVersion';
	public $primaryKey = 'modelId';
	public $recursive = -1;
	public $actsAs = array('Containable');

	public $belongsTo = array(
			'Version' => array(
					'className' => 'ApertureConnector.Version',
					'foreignKey' => 'versionid'
			)
	);
	
}

?>
