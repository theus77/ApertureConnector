<?php 

class Version extends ApertureConnectorAppModel {
	public $useDbConfig = 'aperture';
	public $useTable = 'RKVersion';
	public $primaryKey = 'modelId';
	public $actsAs = array('Containable');
	public $recursive = -1;

	public $virtualFields = array(
		'encodedUuid' => "replace(replace(version.uuid, '+', '-'), '%', '_')",
		'unixImageDate' => 'version.imageDate + 978303600'
	);
	
	public $belongsTo = array(
			'Master' => array(
					'className'    => 'ApertureConnector.Master',
					'foreignKey'   => 'masterUuid'
			),
			'ImportGroup' => array(
					'className'    => 'ApertureConnector.ImportGroup',
					'foreignKey'   => 'modelId'
			)
	);
	
	public $hasAndBelongsToMany = array(
			'Keyword' => array(
					'className' => 'ApertureConnector.Keyword',
					'joinTable' => 'RKKeywordForVersion',
					'foreignKey' => 'versionId',
					'associationForeignKey' => 'keywordId',
			),
			'Album' => array(
					'className' => 'ApertureConnector.Album',
					'joinTable' => 'RKAlbumVersion',
					'foreignKey' => 'versionId',
					'associationForeignKey' => 'albumId',
			)
	);
	
	public $hasMany = array(
			'PlaceForVersion' => array(
					'className' => 'ApertureConnector.PlaceForVersion',
					'foreignKey' => 'versionid',
					//'dependent' => false
			),
			'AlbumVersion' => array(
					'className' => 'ApertureConnector.AlbumVersion',
					'foreignKey' => 'versionid',
					//'dependent' => false
			),
				
			
	);
	
	
}

?>