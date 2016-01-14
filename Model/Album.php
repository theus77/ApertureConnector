<?php 

class Album extends ApertureConnectorAppModel {
	public $useDbConfig = 'aperture';
	public $useTable = 'RKAlbum';
	public $primaryKey = 'modelId';
	//public $recursive = -1;
	//public $actsAs = array('Containable');
	
	public $virtualFields = array(
			'encodedUuid' => "replace(replace(album.uuid, '+', '-'), '%', '_')",
			//'unixCreateDate' => 'version.createDate + 978303600'
	);
}

?>