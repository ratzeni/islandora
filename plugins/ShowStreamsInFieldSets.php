<?php
/*
 * Created on 17-Apr-08
 *
 *
 */
 class ShowStreamsInFieldSets{
 	private $pid =null;
	function ShowStreamsInFieldSets($pid){
        //drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
		$this->pid=$pid;
	}
	
	function showFlv(){
		//$file = basename($form_values["ingest-file-location"]);
		//FLV is the datastream id
		$path = drupal_get_path('module', 'Fedora_Repository');
		$fullPath=base_path().$path;
		$content="";
		$pathTojs = drupal_get_path('module', 'Fedora_Repository').'/js/swfobject.js';
		drupal_add_js("$pathTojs");
		$content.='<div id="player'.$this->pid.'FLV"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this player.</div>';
		drupal_add_js('var s1 = new SWFObject("'.$fullPath.'/flash/flvplayer.swf","single","320","240","7");
		s1.addParam("allowfullscreen","true");
		s1.addVariable("file","'.base_path().'fedora/repository/'.$this->pid.'/FULL_SIZE/FLV.flv");
		s1.write("player'.$this->pid.'FLV");','inline','footer');
		$collection_fieldset = array(
     	 '#title' => t('Flash Video'),
     	 '#collapsible' => TRUE,
     	 '#collapsed' => FALSE,
      	'#value' => $content);
     	return theme('fieldset',$collection_fieldset);
	}

	function showTN() {
	  global $base_url;
	  $collection_fieldset = array(
            '#title' => '',
            '#attributes' => array(),
	    '#collapsible' => FALSE,
		'#value' => '<a href="'.$base_url.'/fedora/repository/'.$this->pid.'/OBJ/"><img src="'.$base_url.'/fedora/repository/'.$this->pid.'/TN/TN'.'" /></a>', 	 
	  );
	  return theme('fieldset', $collection_fieldset);
		
	}
        //same as showTN but artinventory stores the image in a dsid of IMAGE instead of OBJ
        function showArtInventoryTN(){
             global $base_url;
	  $collection_fieldset = array(
	    '#collapsible' => FALSE,
		'#value' => '<a href="'.$base_url.'/fedora/repository/'.$this->pid.'/IMAGE/image.jpg"><img src="'.$base_url.'/fedora/repository/'.$this->pid.'/TN/TN'.'" /></a>',
	  );
	  return theme('fieldset', $collection_fieldset);
        }
	
	function showQdc(){
		module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
		$objectHelper=new ObjectHelper();
		//$returnValue['title']="Description";
		$content=$objectHelper->getQDC($this->pid);
		$collection_fieldset = array(
     	 '#title' => t('Description'),
     	 '#collapsible' => TRUE,
     	 '#collapsed' => FALSE,
      	'#value' => $content);
     	return theme('fieldset',$collection_fieldset);
	}

	function showCritter(){
		$path=drupal_get_path('module', 'Fedora_Repository');
		module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
		module_load_include('php', 'Fedora_Repository', 'CollectionClass');

		$collectionHelper= new CollectionClass();
		$xmlstr=$collectionHelper->getStream($this->pid,"CRITTER");
		html_entity_decode($xmlstr);

		if($xmlstr==null||strlen($xmlstr)<5){
			return " ";
		}
		try {
			$proc = new XsltProcessor();
		} catch (Exception $e) {
			drupal_set_message(t($e->getMessage()),'error');
			return " ";
		}
		$xsl = new DomDocument();
		$xsl->load($path.'/xsl/critter.xsl');
		$input = new DomDocument();
		$input->loadXML(trim($xmlstr));
		$xsl = $proc->importStylesheet($xsl);
		$newdom = $proc->transformToDoc($input);
		$content=$newdom->saveXML();

		$collection_fieldset = array(
     	 '#title' => t('MNPL Critter Record'),
     	 '#collapsible' => TRUE,
     	 '#collapsed' => FALSE,
      	'#value' => $content);
		$pid=$this->pid;
        $itqlQuery = 'select $object $title $content from <#ri>
														where $object <rdf:type> <fedora-model:FedoraObject>
														and $object <fedora-model:label> $title
														and $object <fedora-model:contentModel> $content
			 											and $object <fedora-rels-ext:isMemberOf> <info:fedora/' . $pid . '>
			 											and $object <fedora-model:state> <info:fedora/fedora-system:def/model#Active> order by $title';

      	$relatedItems=$collectionHelper->getRelatedItems($this->pid,$itqlQuery);


     	return theme('fieldset',$collection_fieldset);


	}
	function showRefworks(){
		$path=drupal_get_path('module', 'Fedora_Repository');
		module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
		$collectionHelper= new CollectionClass();
		$xmlstr=$collectionHelper->getStream($this->pid,"refworks");
		html_entity_decode($xmlstr);
		//var_dump($xmlstr);
		if($xmlstr==null||strlen($xmlstr)<5){
			return " ";
		}
		try {
			$proc = new XsltProcessor();
		} catch (Exception $e) {
			drupal_set_message(t($e->getMessage()),'error');
			return " ";
		}
		$xsl = new DomDocument();
		$xsl->load($path.'/xsl/refworks.xsl');
		$input = new DomDocument();
		$input->loadXML(trim($xmlstr));
		$xsl = $proc->importStylesheet($xsl);
		$newdom = $proc->transformToDoc($input);
		$content=$newdom->saveXML();

		$collection_fieldset = array(
     	 '#title' => t('Refworks'),
     	 '#collapsible' => TRUE,
     	 '#collapsed' => FALSE,
      	'#value' => $content);
     	return theme('fieldset',$collection_fieldset);


	}




  function showJP2($collapsed=false) {
    $viewer_url = variable_get('fedora_base_url', '') . '/get/' . $this->pid . '/ilives:viewerSdef/getViewer';
    $html = '<iframe src="' . $viewer_url .'" frameborder="0" style="width: 100%; height: 400px;">Errors: unable to load viewer</iframe>';
    $fieldset = array (
      '#title' => t('Viewer'),
      '#collapsible' => TRUE,
      '#collapsed' => $collapsed,
      '#value' => $html);

    return theme('fieldset', $fieldset);
  }

    

	function showRomeo($collapsed=false){

		$path=drupal_get_path('module', 'Fedora_Repository');
		module_load_include('php', 'Fedora_Repository', 'CollectionClass');
		$collectionHelper = new CollectionClass();
		//$returnValue['title']="Description";
		$xmlstr=$collectionHelper->getStream($this->pid,"ROMEO",0);

		if($xmlstr==null||strlen($xmlstr)<5){
			return " ";
		}

		try {
			$proc = new XsltProcessor();
		} catch (Exception $e) {
			drupal_set_message(t($e->getMessage()),'error');
			return;
		}
		$xsl = new DomDocument();
		$xsl->load($path.'/xsl/romeo.xsl');
		$input = new DomDocument();
		$input->loadXML(trim($xmlstr));
		$xsl = $proc->importStylesheet($xsl);
		$newdom = $proc->transformToDoc($input);
		$content=$newdom->saveXML();

		$collection_fieldset = array(
     	 '#title' => t('Romeo'),
     	 '#collapsible' => TRUE,
     	 '#collapsed' => $collapsed,
      	'#value' => $content);
     	return theme('fieldset',$collection_fieldset);
	}

 }

?>
