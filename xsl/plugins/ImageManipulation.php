<?php
/*
 *
 *
 * This Class implements the methods defined in the STANDARD_IMAGE content model
 */

class ImageManipulation {
  function ImageManipulation() {


  }
  function createPreview($parameterArray,$dsid,$file,$file_ext) {
    $system = getenv('System');
    //if()
    $file_suffix='_'.$dsid.'.'.$file_ext;
    $height=$parameterArray['height'];
    $width=$parameterArray['width'];
    $returnValue=TRUE;
    system("convert -resize $width -quality 85  \"$file\" -strip \"$file$file_suffix\" 2>&1 &",$returnValue);
    //system("convert -resize $width -quality 85  \"$file\" -strip \"$file$file_suffix\"", $returnValue);
    if($returnValue=='0') {
      $_SESSION['fedora_ingest_files']["$dsid"]=$file.$file_suffix;
      return TRUE;
    }else {
      return $returnValue;
    }

  }

  function createPNG($parameterArray=null,$dsid,$file,$file_ext) {
    $file_suffix='_'.$dsid.'.'.$file_ext;
    $returnValue=TRUE;
    system("convert  \"$file\"  \"$file$file_suffix\" 2>&1 &",$returnValue);
    //system("convert  \"$file\"  \"$file$file_suffix\"",$returnValue);

    if($returnValue=='0') {
      $_SESSION['fedora_ingest_files']["$dsid"]=$file.$file_suffix;
      return TRUE;
    }else {

      return $returnValue;
    }

  }
  function createJP2($parameterArray=null, $dsid, $file, $file_ext ) {
    $file_suffix = "_$dsid.$file_ext";
    $return_value = TRUE;
    system("convert  \"$file\"  \"$file$file_suffix\" 2>&1 &",$returnValue);
    system("convert  -resize 800 \"$file\"  \"$file-med.jpg\" 2>&1 &",$returnValue);
    system("convert   \"$file\"  \"$file-full.jpg\" 2>&1 &",$returnValue);
    system("convert  -resize 120 \"$file\"  \"$file-tn.jpg\" 2>&1 &",$returnValue);
    //system("convert  \"$file\"  \"$file$file_suffix\"",$returnValue);

    if($returnValue=='0') {
      $_SESSION['fedora_ingest_files']["$dsid"]=$file.$file_suffix;
      $_SESSION['fedora_ingest_files']["JPG"]=$file.'-med.jpg';
      $_SESSION['fedora_ingest_files']["FULL_JPG"]=$file.'-full.jpg';
      $_SESSION['fedora_ingest_files']["TN"]=$file.'-tn.jpg';

      return TRUE;
    }
    else {
      return $returnValue;
    }
  }

  //use imagemapi to manipulate images instead of going directly to imagemagick or whatever
  function manipulateImage($parameterArray=null,$dsid,$file,$file_ext) {
  	
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
    $output_path= $_SERVER['DOCUMENT_ROOT'].base_path().file_directory_path().'/'.$filename.$file_suffix;
    
    $returnValue = imageapi_image_close( $image,$output_path );
    if($returnValue) {
      $_SESSION['fedora_ingest_files']["$dsid"]=$file.$file_suffix;
       
      return TRUE;
    }else {
      return $returnValue;
    }

  }

  function createThumbnailFromPDF($parameterArray,$dsid,$file,$file_ext) {
    $height=$parameterArray['height'];
    $width=$parameterArray['width'];
    $file_suffix='_'.$dsid.'.'.$file_ext;
    $returnValue=TRUE;
    //system("convert $file\[0\] -thumbnail 128x128 $uploaddir$thumb");
    // Use this for Linux.
    if ( stristr( $_SERVER[ 'SERVER_SOFTWARE'], 'microsoft' ) ) {
    } else if ( stristr( $_SERVER['SERVER_SOFTWARE'], 'linux' ) ) {
        $cmdline = "/usr/local/bin/convert \"$file\"\[0\] -thumbnail $width"."x$height \"$file$file_suffix\"";
      } else if ( stristr( $_SERVER['SERVER_SOFTWARE'], 'unix' ) ) {
        // Use this for Mac OS X (MAMP)
          $cmdline = "sips -s format jpeg \"$file\" -z $height $height --out \"$file$file_suffix\" >/dev/null";
        } else {
          $cmdline = "convert \"$file\"\[0\] -thumbnail ".$width."x".$height." \"$file$file_suffix\"";
        }

    system($cmdline, $returnValue);
    //system("convert $file\[0\] -thumbnail 128x128 $uploaddir$thumb");
    $var = $file.$file_suffix.' returnvalue= '.$returnValue;

    if($returnValue=='0') {
      $_SESSION['fedora_ingest_files']["$dsid"]=$file.$file_suffix;
      return TRUE;
    }else {
      return $returnValue;
    }

  }

  function createThumbnail($parameterArray,$dsid,$file,$file_ext) {
  // var_dump($parameterArray);exit(0);
    $file_suffix='_'.$dsid.'.'.$file_ext;
    $height=$parameterArray['height'];
    $width=$parameterArray['width'];
    $returnValue=TRUE;
    system("convert -resize $width -quality 85  \"$file\" -strip \"$file$file_suffix\" 2>&1 &",$returnValue);
    //system("convert -resize $width -quality 85  \"$file\" -strip \"$file$file_suffix\"",$returnValue);

    if($returnValue=='0') {
      $_SESSION['fedora_ingest_files']["$dsid"]=$file.$file_suffix;
      return TRUE;
    }else {
      return $returnValue;
    }

  }
}
?>