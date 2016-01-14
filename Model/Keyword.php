<?php 

class Keyword extends ApertureConnectorAppModel {
	public $useDbConfig = 'aperture';
	public $useTable = 'RKKeyword';
	public $primaryKey = 'modelId';
	public $recursive = -1;
	public $actsAs = array('Containable');

	public $parent = 'parentId';
	
	public $virtualFields = array(
			'encodedUuid' => "replace(replace(keyword.uuid, '+', '-'), '%', '_')"
	);
	
	public $hasMany = array(
			'Children' => array(
					'className' => 'Keyword',
					'foreignKey' => 'parentId'
			)
	);
	
	public $hasAndBelongsToMany = array(
			'Version' =>
			array(
					'className' => 'ApertureConnector.Version',
					'joinTable' => 'RKKeywordForVersion',
					'foreignKey' => 'keywordId',
					'associationForeignKey' => 'versionId',
			)
	);

	
}

?>