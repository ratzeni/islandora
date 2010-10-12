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

  function createThumbnailFromPDF($parameterArray,$dsid,$file,$file_ext) {
    $height=$parameterArray['height'];
    $width=$parameterArray['width'];
    $file_suffix='_'.$dsid.'.'.$file_ext;
    $returnValue=TRUE;
    //system("convert $file\[0\] -thumbnail 128x128 $uploaddir$thumb");
    system("convert $file\[0\] -thumbnail $heightx$width $file$file_suffix",$returnValue);
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