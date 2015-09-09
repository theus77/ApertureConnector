<?php
//echo Configure::read('libsPath')."PHPZip-master/ZipStream.php";
//exit;
include_once(dirname(__FILE__)  . '/../lib/PHPZip-master/ZipStream.php');

class FoldersController extends ApertureConnectorAppController {
	
	public $uses = array('ApertureConnector.CustomSortOrder', 'ApertureConnector.Folder', 'ApertureConnector.Album');
	public $components = array('ApertureConnector.Properties');
	
	public $paginate = array(
			'limit' => 16,
			'fields' => array('modelId', 'uuid', 'name'), //tableau de champs nommés
			'recursive' => -1
	);
	
	public function index() {
		$albumUuid = "TopLevelAlbums";
		$projectUuid = "AllProjectsItem";
		
		$albums = $this->getTree($albumUuid);		
		$projects = $this->getTree($projectUuid);
		
		$folders = array($albums, $projects);
		
		$this->set('folders', $folders);
		
		$this->set('_serialize', array('folders'));
		
	}
	
	private function getTree($root){
		$findOptions = array(
				'conditions' => array(
						'Folder.uuid' => $root,
						'Folder.isHidden' => 0,
						'Folder.isInTrash' => 0),
		);
		
		$tree = $this->Folder->find('first',$findOptions);
		
		$this->fillFolder($tree);
		
		if(!$tree){
			throw new NotFoundException();
		}
		return $tree;
	}
	
	private function fillFolder(&$parent){
		$findFolderOptions = array(
				'conditions' => array(
						'Folder.parentFolderUuid' => $parent['Folder']['uuid'],
						'Folder.isHidden' => 0,
						'Folder.isInTrash' => 0),
				'joins' => array(
				    array('table' => 'RKCustomSortOrder',
				        'alias' => 'CustomSortOrder',
				        'type' => 'LEFT',
				        'conditions' => array(
				            'CustomSortOrder.objectUuid = Folder.uuid',
				        	'CustomSortOrder.containerUuid = Folder.parentFolderUuid',
				        	'CustomSortOrder.purpose' => substr($parent['Folder']['sortKeyPath'], 7)
				        )
				    )
				),
				'order' => 'ifnull(CustomSortOrder.orderNumber,999999999) '.($parent['Folder']['sortAscending'] == 1?'ASC':'DESC'),
				'fields' => array("Folder.modelId", "Folder.uuid", "Folder.folderType", "Folder.name", "Folder.parentFolderUuid", "Folder.implicitAlbumUuid", "Folder.posterVersionUuid", "Folder.automaticallyGenerateFullSizePreviews", "Folder.versionCount", "Folder.minImageDate", "Folder.maxImageDate", "Folder.folderPath", "Folder.createDate", "Folder.isExpanded", "Folder.isHidden", "Folder.isFavorite", "Folder.isInTrash", "Folder.isMagic", "Folder.colorLabelIndex", "Folder.sortAscending", "Folder.sortKeyPath", "Folder.isHiddenWhenEmpty", "Folder.minImageTimeZoneName", "Folder.maxImageTimeZoneName", 'ifnull(CustomSortOrder.orderNumber,999999999) as "Folder.orderNumber"', 'replace(Folder.uuid, \'%\', \'_\') as "Folder.encodedUuid"'),
		);
		$folders = $this->Folder->find('all',$findFolderOptions);

		
		$findAlbumOptions = array(
				'conditions' => array(
						'Album.folderUuid' => $parent['Folder']['uuid'],
						'Album.isHidden' => 0,
						'Album.isInTrash' => 0,
						'Album.isMagic' => 0,
						'Album.name IS NOT NULL',
				),
				'joins' => array(
				    array('table' => 'RKCustomSortOrder',
				        'alias' => 'CustomSortOrder',
				        'type' => 'LEFT',
				        'conditions' => array(
				            'CustomSortOrder.objectUuid = Album.uuid',
				        	'CustomSortOrder.containerUuid = Album.folderUuid',
				        	'CustomSortOrder.purpose' => substr($parent['Folder']['sortKeyPath'], 7)
				        )
				    )
				),
				'order' => 'ifnull(CustomSortOrder.orderNumber,999999999) '.($parent['Folder']['sortAscending'] == 1?'ASC':'DESC'),
				'fields' => array("Album.name", "Album.uuid", "Album.isMagic", "Album.albumType", "Album.albumSubclass", 'ifnull(CustomSortOrder.orderNumber,999999999) as "Album.orderNumber"', 'replace(Album.uuid, \'%\', \'_\') as "Album.encodedUuid"'),
		);	
		$albums = $this->Album->find('all',$findAlbumOptions);
		
		$parent['Children'] = array();

		
		$folderIdx = 0;
		$albumIdx = 0;
		while ($folderIdx < count($folders)){
			
			while ($albumIdx < count($albums) && ($parent['Folder']['sortAscending'] == 1?$albums[$albumIdx]['Album']['orderNumber']<=$folders[$folderIdx]['Folder']['orderNumber']:$albums[$albumIdx]['Album']['orderNumber']>=$folders[$folderIdx]['Folder']['orderNumber'])){
				$parent['Children'][] = $albums[$albumIdx];
				++$albumIdx;	
			}			
			$this->fillFolder($folders[$folderIdx]);
			$parent['Children'][] = $folders[$folderIdx];
			++$folderIdx;
		}
		
		while ($albumIdx < count($albums)){
			$parent['Children'][] = $albums[$albumIdx];
			++$albumIdx;
				
		}

	}
	
	public function view($uuid){
		
		$decodedProjectUuid = $this->decodeUuid($uuid);
		
		
		$rate = isset($this->params['named']['rate']) ? $this->params['named']['rate'] : 0;
		
		$project = $this->Folder->findByUuid($decodedProjectUuid);
		if($project){
			
			if( $project['Folder']['folderType'] == 2 ){
				$this->paginate['conditions'] = array(
						'Version.showInLibrary' => 1,
						'Version.isHidden' => 0,
						'Version.isInTrash' => 0,
						'Version.mainRating >=' => $rate,
						'Version.Projectuuid' => $decodedProjectUuid);
				$this->paginate['order'] = array('Version.imageDate' => 'asc');
				
				
				$versions = $this->paginate('Version');
				$this->set(compact('project', 'versions'));
				$this->set('_serialize', array('project', 'versions'));
				$this->render('thumbnails');
			}
			else {
				$this->fillFolder($project);
				$folders = array($project);
				$this->set(compact('folders'));
				$this->set('_serialize', array('folders'));
				$this->render('index');
			}
				
		}		
		else{
			throw new NotFoundException();
		}

		$this->response->cache('-1 minute', '+5 days');
	}
	
	private function generateZip($versions, $name){

		$extime = ini_get('max_execution_time');
		ini_set('max_execution_time', 600);
		
		$fileTime = date("D, d M Y H:i:s T");
		
		$zip = new ZipStream("ZipFolders.zip");
		
		$missingFiles = "Images manquantes de ".$name." : \n";
		$hasMissingFiles = false;
		foreach ($versions as $version){
			$properties = $this->Properties->getProperties($version);
			if(isset($properties['ExportedJpg'])){
				$zip->addLargeFile(Configure::read('exportedPath').$properties['ExportedJpg'], $properties['ExportedJpg']);
			}
			else{
				$hasMissingFiles = true;
				$missingFiles .= "\t-".$properties['ProjectName'].' > '.$version['Version']['name']."\n";
			}
		}
		if($hasMissingFiles){
			$zip->addFile($missingFiles, 'missing.txt');		
		}
		
		
		$zip->finalize();
		exit;
		/*$file = tempnam("tmp", "zip");
		$zip = new ZipArchive();
		$zip->open($file, ZipArchive::OVERWRITE);
		$missingFiles = "Images manquantes de ".$name." : \n";
			
		$zip->close();
		header('Content-Type: application/zip');
		header('Content-Length: ' . filesize($file));
		header('Content-Disposition: attachment; filename="download.zip"');
		readfile($file);
		unlink($file);
		exit;*/
		
	}

	public function download($uuid){
		$decodedProjectUuid = $this->decodeUuid($uuid);
		$rate = isset($this->params['named']['rate']) ? $this->params['named']['rate'] : 0;
		
		$project = $this->Folder->findByUuid($decodedProjectUuid);
		if($project){
		
			$options = array(
					'conditions' => array(
						'Version.showInLibrary' => 1,
						'Version.isHidden' => 0,
						'Version.isInTrash' => 0,
						'Version.mainRating >=' => $rate,
						'Version.Projectuuid' => $decodedProjectUuid)
			);
			$versions = $this->Version->find('all', $options);
				
			$this->generateZip($versions, $project['Folder']['name']);
		}
	}
	
	public function downloadAlbum($uuid){
		$decodedAlbumUuid = $this->decodeUuid($uuid);
		$rate = isset($this->params['named']['rate']) ? $this->params['named']['rate'] : 0;
		
		$albumConditions = array(
				'conditions' => array('Album.uuid' => $decodedAlbumUuid), //tableau de conditions
				'fields' => array('modelId', 'uuid', 'name', 'sortKeyPath', 'sortAscending', 'versionCount') //tableau de champs nommés
		);
		
		if($decodedAlbumUuid && $album = $this->Album->find('first', $albumConditions)){
		
			$options = array(
					'joins' => array(
			             array('table' => 'RKAlbumVersion',
			                'alias' => 'AlbumVersion',
			                'type' => 'INNER',
			                'conditions' => array(
			                    'AlbumVersion.albumid' => $album['Album']['modelId'],
			                    'AlbumVersion.versionid = Version.modelid'
		                	)
		            	)
					),
					'conditions' => array(
						'Version.showInLibrary' => 1,
						'Version.isHidden' => 0,
						'Version.isInTrash' => 0,
						'Version.mainRating >=' => $rate)
			);
			$versions = $this->Version->find('all', $options);
			
			$this->generateZip($versions, $album['Album']['name']);
		}

	}
	
	
	
	public function viewAlbum($uuid) {
		$decodedAlbumUuid = $this->decodeUuid($uuid);
		$rate = isset($this->params['named']['rate']) ? $this->params['named']['rate'] : 0;
		
		$albumConditions = array(
		    'conditions' => array('Album.uuid' => $decodedAlbumUuid), //tableau de conditions
		    'fields' => array('modelId', 'uuid', 'name', 'sortKeyPath', 'sortAscending', 'versionCount') //tableau de champs nommés
		);
						
		if($decodedAlbumUuid && $album = $this->Album->find('first', $albumConditions)){
			
			$this->paginate['joins'] = array(
	             array('table' => 'RKAlbumVersion',
	                'alias' => 'AlbumVersion',
	                'type' => 'INNER',
	                'conditions' => array(
	                    'AlbumVersion.albumid' => $album['Album']['modelId'],
	                    'AlbumVersion.versionid = Version.modelid'
	                )
	            )
	        );
			$this->paginate['conditions'] = array(
						'Version.showInLibrary' => 1,
						'Version.isHidden' => 0,
						'Version.isInTrash' => 0,
						'Version.mainRating >=' => $rate);
			$this->paginate['order'] = array('Version.imageDate' => 'asc');
				
			$versions = $this->paginate('Version');
			$album = $album;
			$this->set(compact('album', 'versions'));
			$this->set('_serialize', array('album', 'versions'));
		}
		else{
			throw new NotFoundException();
		}
		$this->render('thumbnails');
	}
	
}
?>