<?php
App::uses('ApertureConnector.IptcProperty', 'ApertureConnector.OtherProperty', 'ApertureConnector.ExifStringProperty', 'ApertureConnector.ExifNumberProperty');

class PropertiesComponent extends Component
{
	public function convertAppleDate($appleDate){
		return $appleDate+mktime(0, 0, 0, 1, 1, 2001);
	}
	
	/**
	 * Action called to get all album's versions
	 * @throws NotFoundException
	 */
	public function getProperties($version){
		$out = array();
		$versionId = $version['Version']['modelId'];
	
		$iptcProperty = ClassRegistry::init('ApertureConnector.IptcProperty');
		$otherProperty = ClassRegistry::init('ApertureConnector.OtherProperty');
		$exifStringProperty = ClassRegistry::init('ApertureConnector.ExifStringProperty');
		$exifNumberProperty = ClassRegistry::init('ApertureConnector.ExifNumberProperty');

		$iptcProperty->contain('UniqueString');
		$otherProperty->contain('UniqueString');
		$exifStringProperty->contain('UniqueString');
		
		foreach ($iptcProperty->findAllByVersionid($versionId) as $key => $property){
			$out[$property['IptcProperty']['propertyKey']] = $property['UniqueString']['stringProperty'];
		}
	
		foreach ($otherProperty->findAllByVersionid($versionId) as $key => $property){
			$out[$property['OtherProperty']['propertyKey']] = $property['UniqueString']['stringProperty'];
		}
	
		foreach ($exifStringProperty->findAllByVersionid($versionId) as $key => $property){
			$out[$property['ExifStringProperty']['propertyKey']] = $property['UniqueString']['stringProperty'];
		}
	
		foreach ($exifNumberProperty->findAllByVersionid($versionId) as $key => $property){
			$out[$property['ExifNumberProperty']['propertyKey']] = $property['ExifNumberProperty']['numberProperty'];
		}
	
		$date = $this->convertAppleDate($version['Version']['imageDate']);
		$out['ImageDate'] = $date;
			
		$reltavivePath = date('Y', $date).'/'.date('Y-m-d ', $date).$out['ProjectName'].'/'.$version['Version']['name'].'.jpg';
		if(file_exists(Configure::read('exportedPath').$reltavivePath)){
			$out['ExportedJpg'] = $reltavivePath;
		}
	
		return $out;
	}
}
?>
