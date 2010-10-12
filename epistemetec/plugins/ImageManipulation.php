<?php
module_load_include('php', 'Fedora_Repository', 'plugins/ImageManipulation');

class EpistemetecImageManipulation extends ImageManipulation {
	function EpistemetecImageManipulation() {
		module_load_include('php', 'Fedora_Repository', 'plugins/ImageManipulation');
		drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

	}

	function getMetadata(& $form_values) {
		module_load_include('php', 'Fedora_Repository', 'mimetype');
		global $base_url;
		//$mimetype = new mimetype();
		
		if (!empty ($_SESSION['fedora_ingest_files'])) {
			foreach ($_SESSION['fedora_ingest_files'] as $dsid => $createdFile) {
				$file = $form_values['ingest-file-location'];
				$fileUrl = $base_url . '/fedora/repository/'.$form_values['pid'].'/FULL_SIZE'; 
				$file_size = filesize($createdFile);
				$file_md5 = md5_file($createdFile);
				$file_path = $fileUrl;
				$imagesize = getimagesize($createdFile);
				//list($file_width, $file_height, $file_format, $attr,$file_mime) = getimagesize($createdFile);
				$file_width = $imagesize['0'];
				$file_height = $imagesize['1'];
				$file_format = $imagesize['2'];
				$attr = $imagesize['3'];
				$file_mime = $imagesize['mime'];
				continue;
			}
		}

		$form_values['img_size'] = $file_size;
		$form_values['img_md5'] = $file_md5;
		$form_values['img_file'] = $file_path;
		$form_values['img_imagelength'] = $file_height;
		$form_values['img_imagewidth'] = $file_width;
		$form_values['img_format'] = str_replace(array (
			'1',
			'2',
			'3',
			'4'
		), array (
			'GIF',
			'JPG',
			'PNG',
			'SWF'
		), $file_format);
		 
		$form_values['img_mime'] = $file_mime;
		$form_values['img_samplingfrequencyunit'] = '1';		
		$form_values['img_samplingfrequencyplane'] = '1';
	}
	
	 function manipulateImage($parameterArray=null,$dsid,$file,$file_ext,$folder) {  	
    $height=$parameterArray['height'];
    $width=$parameterArray['width'];

    $file_suffix='_'.$dsid.'.'.$file_ext;
    $returnValue=TRUE;

    $image = imageapi_image_open( $file );

    if(!$image) {
      drupal_set_message(t("Error opening image"));
      return false;
    }

    if ( !empty ( $height ) || !empty($width ) ) {
      $returnValue= imageapi_image_scale( $image, $height, $width );
    }

    if(!$returnValue) {
      drupal_set_message(t("Error scaling image"));
      return $returnValue;
    }
    $filename=substr(strrchr($file,'/'),1);
    
    $output_path= $_SERVER['DOCUMENT_ROOT'].base_path().file_directory_path().'/'.$folder.'/'.$filename.$file_suffix;
    
    $returnValue = imageapi_image_close( $image,$output_path );
    if($returnValue) {
      $_SESSION['fedora_ingest_files']["$dsid"]=$file.$file_suffix;
       
      return TRUE;
    }else {
      return $returnValue;
    }

  
	 }
}
?>
