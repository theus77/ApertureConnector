<?php
use Aws\S3\S3Client;
use Aws\Credentials\CredentialProvider;
use Psr\Log\Test\DummyTest;
App::uses('ApertureConnectorAppController', 'ApertureConnector.Controller', 'ApertureConnector.Model');
/**
 * Imgs Controller
 *
 */
class ImgController extends ApertureConnectorAppController {
		
	var $uses = array('ApertureConnector.ImageProxyState');
	public $components = array('RequestHandler');
	
	// constantes
	var $cache_dir = 'img/cache';
	var $error_404_img = 'img/404.jpg';
	var $error_missing_img = 'img/missing.%s.jpg';
	var $types = array(IMAGETYPE_GIF => "gif", IMAGETYPE_JPEG => "jpeg", IMAGETYPE_PNG => "png", IMAGETYPE_SWF => "swf", IMAGETYPE_PSD => "psd", IMAGETYPE_BMP => "wbmp");
	
	
	private function getParam($name, $default){
		$out = $default;
		if(isset($this->params['named'][$name]))
			$out = $this->params['named'][$name];
		if(isset($this->params[$name]))
			$out = $this->params[$name];
		if ( strrpos($name, 'mobile_', -strlen($name) ) === FALSE && $this->RequestHandler->isMobile() ) {
			$out = $this->getParam('mobile_'.$name, $out);
		}
		return $out;
	}
	
	
	public function s3($key){
		

// 		echo $key;
// 		exit;
		
		$quality		= $this->getParam('quality', false);
		
		$s3 = new S3Client([
				'version' => 'latest',
				'region'  => 'eu-central-1',
				'credentials' => CredentialProvider::ini('default', '/home/vagrant/.aws/credentials')
		]);		
		
		$result = $s3->getObject([
				'Bucket' => 'global-previews',
				'Key'    => $key
		]);
// 		echo $result['Body'];


		$data = $this->convertImage($result['Body'], $key);

		if($quality){
			$mime = 'image/png';
		}
		else{
			$mime = 'image/jpeg';
		}
		
		header("Content-type:$mime");
		header('Content-Length: ' . strlen($data['data']));
		echo $data['data'];
		exit();
		
		
		
	}
	
	

	
	public function viewFile($full_path, $fileId) {
		$data = file_get_contents($full_path);
		return $this->convertImage($data, $fileId);
	}
	
	private function convertImage($data, $fileId) {		
		$out = array();
		
		
		$download		= $this->getParam('download', false);
		$name			= $this->getParam('name', "image");
		$resize			= $this->getParam('resize', false);
		$width			= $this->getParam('width', '*'); 
		$quality		= $this->getParam('quality', false);
		$height			= $this->getParam('height', '*'); //isset($this->params['named']['height']) ?	$this->params['named']['height'] : '*';
		$gravity		= $this->getParam('gravity', 'center'); //isset($this->params['named']['gravity']) ?	$this->params['named']['gravity'] : 'center';
		$radius			= $this->getParam('radius', false); //isset($this->params['named']['radius']) ?	$this->params['named']['radius'] : false;
		$background	    = $this->getParam('background', 'FFFFFF'); //isset($this->params['named']['background'])?$this->params['named']['background'] : 'FFFFFF';
		$radius_geometry = $this->getParam('radius_geometry', 'topleft-topright-bottomright-bottomleft'); //isset($this->params['named']['radius_geometry'])?$this->params['named']['radius_geometry'] : 'topleft-topright-bottomright-bottomleft';
		$watermark		 = $this->getParam('watermark', false);
		
		// get size of image
		$size	= getimagesizefromstring($data);
		// get mimetype
		$mime	= $size['mime'];
		
		//echo $height;exit;
		
		//adjuste width or height in case of ratio resize
		if($resize && $resize == 'ratio'){
			// if either width or height is an asterix
			if($width == '*' || $height == '*') {
				if($height == '*') {
					// recalculate height
					$height = ceil($width / ($size[0]/$size[1]));
				} else {
					// recalculate width
					$width = ceil(($size[0]/$size[1]) * $height);
				}
			} else {
				if (($size[1]/$height) > ($size[0]/$width)) {
					$width = ceil(($size[0]/$size[1]) * $height);
				} else {
					$height = ceil($width / ($size[0]/$size[1]));
				}
			}
		}
		
		
		// create new file names
		$file_relative = $this->cache_dir.'/'.($resize?$resize.'_'.(($resize == 'fillArea')?$gravity.'_':'').$width.'x'.$height.'_':'').($radius?'r'.$radius.'_'.$background.'_':'').$fileId.($quality?'-'.$quality.'.jpeg':'.png');
		$file_cached = WWW_ROOT.$this->cache_dir.DS.($resize?$resize.'_'.(($resize == 'fillArea')?$gravity.'_':'').$width.'x'.$height.'_':'').($radius?'r'.$radius.'_'.$background.'_':'').$fileId.($quality?'-'.$quality.'.jpeg':'.png');
		
		// if cached file already exists
		$cached = false;
		$out['lastUpdateDate'] = false;
		
		if(Configure::read('debug') <= 2 && file_exists($file_cached)) {
			$lastUpdateDate = @filemtime($file_cached);
			$out['lastUpdateDate'] = $lastUpdateDate;
			$cached = ($out['lastUpdateDate'] >= @filemtime($url));
		}
		
		
		if( !$cached ){
			
			//echo 'not cached';exit;
			
			//load the source file
			//$image = call_user_func('imagecreatefrom'.$this->types[$size[2]], $full_path);
			$image = imagecreatefromstring($data);
			
// 			header("Content-type:image/jpeg");
// 			imagejpeg($image);
			
// 			exit();
			
			
			
// 			header("Content-type:$mime");
// 			header('Content-Length: ' . strlen($data['data']));
// 			echo $data['data'];
			
		
			// if image modification is necessary
			if($resize || $radius) {
				// image
				if($resize){
					if (function_exists("imagecreatetruecolor") && ($temp = imagecreatetruecolor ($width, $height))) {
						$resizeFunction = 'imagecopyresampled';
					} else {
						$temp = imagecreate($width, $height);
						$resizeFunction = 'imagecopyresized';
					}
		
		
					$solid_colour = imagecolorallocate(
							$temp,
							hexdec(substr($background, 0, 2)),
							hexdec(substr($background, 2, 2)),
							hexdec(substr($background, 4, 2))
					);
		
					imagefill(
					$temp,
					0,
					0,
					$solid_colour
					);
		
						
					if($resize == 'fillArea'){
						if(($size[1]/$height) < ($size[0]/$width)){
							$cal_width = $size[1] * $width / $height;
							if(stripos($gravity, 'west') !== false)
							{
								call_user_func($resizeFunction, $temp, $image, 0, 0, $size[0]-$cal_width, 0, $width, $height, $cal_width, $size[1]);
							}
							else if(stripos($gravity, 'est') !== false)
							{
								call_user_func($resizeFunction, $temp, $image, 0, 0, 0, 0, $width, $height, $cal_width, $size[1]);
							}
							else{
								call_user_func($resizeFunction, $temp, $image, 0, 0, ($size[0]-$cal_width)/2, 0, $width, $height, $cal_width, $size[1]);
							}
						}
						else{
							$cal_height = $size[0] / $width * $height;
							if(stripos($gravity, 'north') !== false)
							{
								call_user_func($resizeFunction, $temp, $image, 0, 0, 0, 0, $width, $height, $size[0], $cal_height);
							}
							else if(stripos($gravity, 'south') !== false)
							{
								call_user_func($resizeFunction, $temp, $image, 0, 0, 0, $size[1]-$cal_height, $width, $height, $size[0], $cal_height);
							}
							else{
								call_user_func($resizeFunction, $temp, $image, 0, 0, 0, ($size[1]-$cal_height)/2, $width, $height, $size[0], $cal_height);
							}
						}
					}
					else if($resize == 'fill'){
						if(($size[1]/$height) < ($size[0]/$width)){
		
							$thumb_height = $width*$size[1]/$size[0];
							call_user_func($resizeFunction, $temp, $image, 0, ($height-$thumb_height)/2, 0, 0, $width, $thumb_height, $size[0], $size[1]);
						}
						else {
							$thumb_width = ($size[0]*$height)/$size[1];
							call_user_func($resizeFunction, $temp, $image, ($width-$thumb_width)/2, 0, 0, 0, $thumb_width, $height, $size[0], $size[1]);
						}
							
					}
					else{
						call_user_func($resizeFunction, $temp, $image, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
					}
					imagedestroy($image);
					$image = $temp;
				}
				if($radius){
					$image = $this->applyCorner($image, $width, $height, $radius, $radius_geometry, $background);
				}
				if($watermark){
// 					echo $watermark; exit;
					$image = $this->applyWatermark($image, $width, $height, $watermark);
				}
				if($quality){
					imagejpeg($image, $file_cached, $quality);
				}
				else {
					imagepng($image, $file_cached);
				}
				imagedestroy($image);
			}
			else {
				//convert into png
				if($quality){
					imagejpeg($image, $file_cached, $quality);
				}
				else {
					imagepng($image, $file_cached);
				}
				imagedestroy($image);
				// copy original file
				//copy($full_path, $file_cached);
			}
			$lastUpdateDate = time();
		}
		else{//file cahced
			//echo 'file cached';
			//echo $file_cached; exit;
			//test if the current version is the same as the one already knowed by the browser
			if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
					strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastUpdateDate)
			{
				//http://stackoverflow.com/questions/10847157/handling-if-modified-since-header-in-a-php-script
				header('HTTP/1.0 304 Not Modified');
				exit;
			}
		}
		//should always by image/png
		//$size	= getimagesize($file_cached);
		//$mime	= $size['mime'];

		// get file contents
		$out['data'] =  file_get_contents($file_cached);
		return $out;	
	
	}
	
	/**
	 * manipulates and displays an image
	 * @uuid 		= version uuid (mandatory) -- default value is false
	 * @width 		= width to resize image to (optional) -- default value is *
	 * @height 		= height to resize image to (optional) -- default value is *
	 * @resize 		= ratio/strech/fillArea/fill (optional) -- default value is false
	 * @gravity		= center/north/south/southest/...  -- default value is center
	 * @radius  	= corner radius in pixels -- default value is false
	 * @background  = corner colour in rgb hex format -- default value is FFFFFF
	 */
	public function viewVersion($encodedUuid) {
		// get params
		//echo $uuid.'<br>';
		
		
		$uuid = $this->decodeUuid($encodedUuid);
		
		
		//echo $uuid." ".$encodedUuid;
		//exit;
		
		$download		= $this->getParam('download', false);
		$name			= $this->getParam('name', "image");
		$resize			= $this->getParam('resize', false);
		$width			= $this->getParam('width', '*'); 
		$quality		= $this->getParam('quality', false);
		$height			= $this->getParam('height', '*'); //isset($this->params['named']['height']) ?	$this->params['named']['height'] : '*';
		$gravity		= $this->getParam('gravity', 'center'); //isset($this->params['named']['gravity']) ?	$this->params['named']['gravity'] : 'center';
		$radius			= $this->getParam('radius', false); //isset($this->params['named']['radius']) ?	$this->params['named']['radius'] : false;
		$background	    = $this->getParam('background', 'FFFFFF'); //isset($this->params['named']['background'])?$this->params['named']['background'] : 'FFFFFF';
		$radius_geometry = $this->getParam('radius_geometry', 'topleft-topright-bottomright-bottomleft'); //isset($this->params['named']['radius_geometry'])?$this->params['named']['radius_geometry'] : 'topleft-topright-bottomright-bottomleft';
		$language		= $this->getParam('language', 'en');
		
	
		if($uuid){
			
			$imageProxy = $this->ImageProxyState->findByVersionuuid($uuid);

//  			print_r($imageProxy);
//  			exit;
			// get full image path
			if($imageProxy){				
				$full_path = Configure::read('aperturePath').'Thumbnails/'.$imageProxy['ImageProxyState']['thumbnailPath'];
			}
			else{
				$uuid = false;
			}
// 			echo $full_path;
// 			exit;
		}
	
		
		
		//echo file_exists($full_path);
		//exit;
		
		// check file exists

		if($uuid){
			if(isset($full_path) && file_exists($full_path)/**/) {
				$data = $this->viewFile($full_path, $this->encodeUuid($uuid));
			}
			else {
				
				//echo Configure::read('Config.language');
				//exit;
				$data = $this->viewFile(__($this->error_missing_img, $language), 'missing-'.$language);
			}
		} else {

			//echo '404';exit;
			$data = $this->viewFile(__($this->error_404_img, $language), '404-'.$language);
			
			//	echo Configure::read('Config.language');
			//exit;
			//throw new NotFoundException(__('Image not found'));
			//echo __($this->error_img, Configure::read('Config.language'));
			//exit;
			//$size	= getimagesize(__($this->error_img, Configure::read('Config.language')));
			//$mime	= $size['mime'];
			//$data = file_get_contents(__($this->error_img, Configure::read('Config.language')));
		}
	
	
		// set headers and output image
		if($data['lastUpdateDate'])	{
			header("Last-Modified:".date('D, d M Y H:i:s ', $data['lastUpdateDate'])."GMT");
		}
		if($download){
			header("Content-Transfer-Encoding: binary");
			header("Content-Disposition: attachment; filename=".$name.($quality?".jpg":".png"));
		}

		if($quality){
			$mime = 'image/png';
		}
		else{
			$mime = 'image/jpeg';
		}
		
		header("Content-type:$mime");
		header('Content-Length: ' . strlen($data['data']));
		echo $data['data'];
		exit();
	}
	
	private function applyWatermark($image, $width, $height, $watermark){
		$stamp = imagecreatefrompng($watermark);
		$sx = imagesx($stamp);
		$sy = imagesy($stamp);
		imagecopy($image, $stamp, ($width - $sx) /2, ($height - $sy)/2, 0, 0, $sx, $sy);
		return $image;
	}
	
	private function applyCorner($source_image, $source_width, $source_height, $radius, $radius_geometry, $colour){
		$corner_image = imagecreatetruecolor(
				$radius,
				$radius
		);
	
	
		$clear_colour = imagecolorallocate(
				$corner_image,
				0,
				0,
				0
		);
	
	
		$solid_colour = imagecolorallocate(
				$corner_image,
				hexdec(substr($colour, 0, 2)),
				hexdec(substr($colour, 2, 2)),
				hexdec(substr($colour, 4, 2))
		);
	
		imagecolortransparent(
		$corner_image,
		$clear_colour
		);
	
		imagefill(
		$corner_image,
		0,
		0,
		$solid_colour
		);
	
		imagefilledellipse(
		$corner_image,
		$radius,
		$radius,
		$radius * 2,
		$radius * 2,
		$clear_colour
		);
	
		/*
		 * render the top-left, bottom-left, bottom-right, top-right corners by rotating and copying the mask
		*/
	
		if(stripos($radius_geometry, "topleft") !== FALSE){
			imagecopymerge(
			$source_image,
			$corner_image,
			0,
			0,
			0,
			0,
			$radius,
			$radius,
			100
			);
		}
	
		$corner_image = imagerotate($corner_image, 90, 0);
	
		if(stripos($radius_geometry, "bottomleft") !== FALSE){
			imagecopymerge(
			$source_image,
			$corner_image,
			0,
			$source_height - $radius,
			0,
			0,
			$radius,
			$radius,
			100
			);
		}
	
		$corner_image = imagerotate($corner_image, 90, 0);
	
		if(stripos($radius_geometry, "bottomright") !== FALSE){
			imagecopymerge(
			$source_image,
			$corner_image,
			$source_width - $radius,
			$source_height - $radius,
			0,
			0,
			$radius,
			$radius,
			100
			);
		}
	
		$corner_image = imagerotate($corner_image, 90, 0);
	
		if(stripos($radius_geometry, "topright") !== FALSE){
			imagecopymerge(
			$source_image,
			$corner_image,
			$source_width - $radius,
			0,
			0,
			0,
			$radius,
			$radius,
			100
			);
		}
	
		$tansparent_colour = imagecolorallocate(
				$source_image,
				hexdec(substr($colour, 0, 2)),
				hexdec(substr($colour, 2, 2)),
				hexdec(substr($colour, 4, 2))
		);
			
		imagecolortransparent(
		$source_image,
		$tansparent_colour
		);
	
		// 		header("Content-type:image/png");
		// 		echo imagepng($source_image) ;
		// 		exit();
		return $source_image;
	
	}

}
