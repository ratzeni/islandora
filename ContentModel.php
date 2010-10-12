<?php
/*
 * Created on 18-Feb-08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

class ContentModel {
  function ContentModel() {

  }
  public $pid_namespace;
  public $content_model_pid;
  public $content_model_dsid;
  public $content_model_name;

  public function getIdentifier() {
    return $this->content_model_pid.'/'.$this->content_model_dsid;
  }

  public static function getPidFromIdentifier($identifier) {
    return substr($identifier, 0, strpos($identifier, "/"));
  }

  public static function getDSIDFromIdentifier($identifier) {
    $temp = strstr ($identifier, "/");
    return substr($temp,1);
  }
}
?>