<?php

App::uses('AppController', 'Controller');

class VersionsController extends ApertureConnectorAppController {

	public $uses = array('ApertureConnector.Version', 'ApertureConnector.Folder', 'ApertureConnector.Album', 'AlbumComment', 'ApertureConnector.IptcProperty', 'ApertureConnector.OtherProperty', 'ApertureConnector.ExifStringProperty', 'ApertureConnector.ExifNumberProperty');

	public $components = array('ApertureConnector.Properties');
			
	public $helpers = array('Form');

	public $paginate = array(
			'limit' => 16,
			'fields' => array('modelId', 'uuid', 'name'), //tableau de champs nommés
			'recursive' => -1
	);

	
	public function viewInAlbum($uuid, $albumUuid) {
		$decodedAlbumUuid = $this->decodeUuid($albumUuid);
		$decodedVersionUuid = $this->decodeUuid($uuid);
		$rate = isset($this->params['named']['rate']) ? $this->params['named']['rate'] : 0;
		
		if($decodedAlbumUuid && $album = $this->Album->findByUuid($decodedAlbumUuid)){
			$imageIdx = FALSE;

			$versionConditions = array(
					'joins' => array(array(
							'table' => 'RKAlbumVersion',
							'alias' => 'AlbumVersion',
							'type' => 'INNER',
							'conditions' => array(
									'AlbumVersion.albumid' => $album['Album']['modelId'],
									'AlbumVersion.versionid = Version.modelid'
							)
					)),
					'recursive' => -1,
					'conditions' => array(
						'Version.showInLibrary' => 1,
						'Version.isHidden' => 0,
						'Version.isInTrash' => 0,
						'Version.mainRating >=' => $rate,), //tableau de conditions
					'fields' => array('modelId', 'uuid', 'name', 'imageDate'), //tableau de champs nommés
					'order' => array('Version.imageDate ASC'),
			);
			
			
			$versions = $this->Version->find('all', $versionConditions);
			foreach ($versions as $idx => $version){
				if(strcmp($version['Version']['uuid'], $decodedVersionUuid) == 0){
					$imageIdx = $idx;
					break;
				}
			}
			
// 			if($imageIdx === FALSE){
// 				throw new NotFoundException();
// 			}

			$version = $this->Version->findByUuid($decodedVersionUuid);	
			$comments = $this->AlbumComment->findAllByAlbumIdAndVersionId($album['Album']['modelId'], $version['Version']['modelId']);
			$properties = $this->Properties->getProperties($version);
			
			$this->set(compact('album', 'version', 'versions', 'comments', 'properties', 'imageIdx'));
			$this->set('_serialize', array('album', 'version', 'versions', 'comments', 'properties', 'imageIdx'));
				
		}
		else{
			throw new NotFoundException();
		}		
		
		$this->render('version');
	}
	

	
	public function viewInProject($uuid, $projectUuid) {
		$decodedProjectUuid = $this->decodeUuid($projectUuid);
		$decodedVersionUuid = $this->decodeUuid($uuid);
		$rate = isset($this->params['named']['rate']) ? $this->params['named']['rate'] : 0;
		
		$findOptions = array(
				'conditions' => array(
						'Version.Projectuuid' => $decodedProjectUuid,
						'Version.showInLibrary' => 1,
						'Version.isHidden' => 0,
						'Version.isInTrash' => 0,
						'Version.mainRating >=' => $rate,
						
				),
				'order' => array('Version.imageDate ASC'),
				//'fields' => array('modelId', 'uuid', 'name', 'imageDate')
		);
		
		$versions = $this->Version->find('all', $findOptions);
		
		if($versions){
			if($decodedVersionUuid){
				$version = $this->Version->findByUuid($decodedVersionUuid);
		
				$project = $this->Folder->findByUuid($version['Version']['projectUuid']);
				if($project){
					$date = $version['Version']['imageDate']+mktime(0, 0, 0, 1, 1, 2001);
					$reltavivePath = date('Y', $date).'/'.date('Y-m-d ', $date).$project['Folder']['name'].'/'.$version['Version']['name'].'.jpg';
					if(file_exists(Configure::read('exportedPath').$reltavivePath)){
						$version['Version']['exportedJpg'] = (Configure::read('exportedUrl').$reltavivePath);
					}
				}
			}
// 			else {
// 				throw new NotFoundException();
// 			}
			
			foreach ($versions as $idx => $version){
				if(strcmp($version['Version']['uuid'], $decodedVersionUuid) == 0){
					$imageIdx = $idx;
					break;
				}
			}
				
			
			$properties = $this->Properties->getProperties($version);
			$comments = $this->AlbumComment->findAllByVersionId($version['Version']['modelId']);
		}
		else{
			throw new NotFoundException();
		}
		$projectUuid = $decodedProjectUuid;/**/
		
		$this->set(compact('project', 'version', 'versions', 'comments', 'properties', 'imageIdx'));
		$this->set('_serialize', array('project', 'version', 'versions', 'comments', 'properties', 'imageIdx'));
		
		$this->render('version');
		
	}
}
?>
