<?php 

class AlbumVersion extends ApertureConnectorAppModel {
	public $useDbConfig = 'aperture';
	public $useTable = 'RKAlbumVersion';
	public $primaryKey = 'modelId';
	//public $recursive = -1;
	//public $actsAs = array('Containable');
}

?>