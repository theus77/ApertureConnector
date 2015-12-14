<?php
//echo Configure::read('libsPath')."PHPZip-master/ZipStream.php";
//exit;
include_once(dirname(__FILE__)  . '/../lib/PHPZip-master/ZipStream.php');

class KeywordsController extends ApertureConnectorAppController {
	
	public $uses = array('ApertureConnector.Keyword');
	
	//public $components = array('ApertureConnector.Properties');
	
	/*public $paginate = array(
			'limit' => 16,
			'fields' => array('modelId', 'uuid', 'name'), //tableau de champs nommés
			'recursive' => -1
	);*/
	
	public function index() {
		$findOptions = array(
				//'conditions' => array(
				//		'Keyword.parentId' => null),
				//'contain' => 'Children'
				'order' => array('Keyword.name'),
		);
		
		$keywords = $this->Keyword->find('all',$findOptions);
		//$keywords = $this->Keyword->children(null, false, null, 'Keyword.name');
		
		if(!$keywords){
			throw new NotFoundException();
		}
		
		$this->set('keywords', $keywords);
		
		$this->set('_serialize', array('keywords'));
		
	}
	
	
	public function view($modelId) {
		$findOptions = array(
			//'conditions' => array(
			//		'Keyword.parentId' => null),
			'contain' => 'KeywordForVersion'
			//'order' => array('Keyword.name'),
		);
	
		$keywords = $this->Keyword->findByModelid($modelId,$findOptions);
		//$keywords = $this->Keyword->children(null, false, null, 'Keyword.name');
	
		if(!$keywords){
			throw new NotFoundException();
		}
	
		$this->set('keywords', $keywords);
	
		$this->set('_serialize', array('keywords'));
	
	}
	
	
	
	
}

?>