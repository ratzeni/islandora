<?php
module_load_include('php', 'Fedora_Repository', 'plugins/ShowStreamsInFieldSets');
/*
 * Created on 17-Apr-08
 *
 *
 */
class ShowEpistemetecStreamsInFieldSets extends ShowStreamsInFieldSets {
	private $pid = null;
	function ShowEpistemetecStreamsInFieldSets($pid) {
		//drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
		$this->pid = $pid;
	}

	function showMediumSize() {
		global $base_url;
		$collection_fieldset = array (
			'#collapsible' => FALSE,
			'#value' => '<a href="' . $base_url . '/fedora/repository/' . $this->pid . '/MEDIUM_SIZE/"><img src="' . $base_url . '/fedora/repository/' . $this->pid . '/MEDIUM_SIZE/MEDIUM_SIZE' . '" /></a>',
			
		);
		return theme('fieldset', $collection_fieldset);

	}

//	function showImageData() {
//		module_load_include('php', 'Fedora_Repository', 'epistemetec/plugins/ObjectHelper');
//		$objectHelper = new ObjectEpistemetecHelper();
//		//$returnValue['title']="Description";
//		$content = $objectHelper->getImageData($this->pid);
//		$collection_fieldset = array (
//			'#title' => t('MAG DATA'),
//			'#collapsible' => TRUE,
//			'#collapsed' => TRUE,
//			'#value' => $content
//		);
//		return theme('fieldset', $collection_fieldset);
//	}

	function showMagData() {
		module_load_include('php', 'Fedora_Repository', 'epistemetec/plugins/ObjectHelper');
		$objectHelper = new ObjectEpistemetecHelper();
		//$returnValue['title']="Description";
		$content = $objectHelper->getMagData($this->pid);
		$collection_fieldset = array (
			'#title' => t('MAG DATA'),
			'#collapsible' => TRUE,
			'#collapsed' => TRUE,
			'#value' => $content
		);
		return theme('fieldset', $collection_fieldset);
	}

	function showEpistemetecData() {
		module_load_include('php', 'Fedora_Repository', 'epistemetec/plugins/ObjectHelper');
		$objectHelper = new ObjectEpistemetecHelper();
		//$returnValue['title']="Description";
		$content = $objectHelper->getEpistemetecData($this->pid);
		$collection_fieldset = array (
			'#title' => t('EPISTEMETEC DATA'),
			'#collapsible' => TRUE,
			'#collapsed' => TRUE,
			'#value' => $content
		);
		return theme('fieldset', $collection_fieldset);
	}

	function showMP3() {
	//$file = basename($form_values["ingest-file-location"]);
		//FLV is the datastream id
		$path = drupal_get_path('module', 'Fedora_Repository');
		$fullPath=base_path().$path;
		$content="";
		$pathTojs = drupal_get_path('module', 'Fedora_Repository').'/js/swfobject.js';
		drupal_add_js("$pathTojs");
		$content.='<div id="player'.$this->pid.'FLV"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this player.</div>';
		drupal_add_js('var s1 = new SWFObject("'.$fullPath.'/flash/flvplayer.swf","single","320","21","7");
		s1.addParam("allowfullscreen","true");
		s1.addVariable("file","'.base_path().'fedora/repository/'.$this->pid.'/FULL_SIZE/MP3.mp3");
		s1.write("player'.$this->pid.'FLV");','inline','footer');
		$collection_fieldset = array(
     	 '#title' => t('Player MP3'),
     	 '#collapsible' => TRUE,
     	 '#collapsed' => FALSE,
      	'#value' => $content);
     	return theme('fieldset',$collection_fieldset);
	}

}
?>
