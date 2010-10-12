<?php
/*
 * Created on 19-Feb-08
 *
 *
 * implements methods from content model ingest form xml
 * builds a dc metadata form
 */
class FormBuilder {
 	function FormBuilder(){
 	  module_load_include('nc', 'FormBuilder', '');
 	  drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

 	}

 	function createQDCStream($form_values,&$dom,&$rootElement){
 	  $datastream = $dom->createElement("foxml:datastream");
 	  $datastream->setAttribute("ID","DC");
 	  $datastream->setAttribute("STATE","A");
 	  $datastream->setAttribute("CONTROL_GROUP","X");
 	  $version = $dom->createElement("foxml:datastreamVersion");
 	  $version->setAttribute("ID","DC.0");
 	  $version->setAttribute("MIMETYPE","text/xml");
 	  $version->setAttribute("LABEL","Dublin Core Record");
 	  $datastream->appendChild($version);
 	  $content = $dom->createElement("foxml:xmlContent");
 	  $version->appendChild($content);
 	  ///begin writing qdc
 	  $oai = $dom->createElement("oai_dc:dc");
 	  $oai->setAttribute('xmlns:oai_dc',"http://www.openarchives.org/OAI/2.0/oai_dc/");
 	  $oai->setAttribute('xmlns:dc',"http://purl.org/dc/elements/1.1/");
 	  $oai->setAttribute('xmlns:dcterms',"http://purl.org/dc/terms/");
 	  $oai->setAttribute('xmlns:xsi',"http://www.w3.org/2001/XMLSchema-instance");
 	  $content->appendChild($oai);
 	  //dc elements
 	  $previousElement=null;//used in case we have to nest elements for qualified dublin core
 	  foreach ($form_values as $key => $value){
 	    $index = strrpos($key,'-');
 	    if($index>01){
 	      $key = substr($key,0,$index);
 	    }

 	    $test = substr($key,0,2);

 	    if($test=='dc'||$test=='ap'){//don't try to process other form values
 	      try{
 	        if(!strcmp(substr($key,0,4),'app_')){
 	          $key = substr($key,4);
 	          $previousElement->appendChild($dom->createElement($key,$value));

 	        }else{
 	          $previousElement = $dom->createElement($key,$value);
 	          $oai->appendChild($previousElement);
 	        }

 	      }catch(exception $e){
 	        drupal_set_message(t($e->getMessage()),'error');
 	        continue;
 	      }
 	    }
 	    $rootElement->appendChild($datastream);

 	  }

 	}

 	//create the security Policy
 	function createPolicy($collectionPid,&$dom,&$rootElement){
 	  module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
 	  $objectHelper = new ObjectHelper();

 	  $dsid = 'CHILD_SECURITY';
 	  $policyStreamDoc = $objectHelper->getStream($collectionPid,$dsid,false);
 	  if(!isset($policyStreamDoc)){
 	    return null;//there is no policy stream so object will not have a policy stream
 	  }
 	  try {
 	    $xml = new SimpleXMLElement($policyStreamDoc);
 	  } catch (Exception $e) {
 	    watchdog(t("Fedora_Repository"),t("Problem getting Security Policy!"),NULL, WATCHDOG_ERROR);
 	    drupal_set_message(t('Problem getting Security Policy! ') . $e->getMessage(), 'error');
 	    return false;
 	  }
 	  $policyElement = $dom->createDocumentFragment();
 	  if (!$policyElement) {
 	    drupal_set_message(t('error parsing security policy stream!'));
 	    watchdog(t("Fedora_Repository"), t("Error parsing security policy stream, could not parse policy stream!"), NULL,WATCHDOG_NOTICE);
 	    return false;
 	  }
 	  $dom->importNode($policyElement,true);
 	  $value=$policyElement->appendXML($policyStreamDoc);
 	  if(!$value){
 	    drupal_set_message(t('error creating security policy stream!'));
 	    watchdog(t("Fedora_Repository"), t("Error creating security policy stream, could not parse collection policy template file!"),NULL, WATCHDOG_NOTICE);
 	    return false;
 	  }

 	  $ds1 = $dom->createElement("foxml:datastream");
 	  $rootElement->appendChild($ds1);
 	  $ds1->setAttribute("ID", "POLICY");
 	  $ds1->setAttribute("STATE", "A");
 	  $ds1->setAttribute("CONTROL_GROUP", "X");
 	  $ds1v = $dom->createElement("foxml:datastreamVersion");
 	  $ds1->appendChild($ds1v);
 	  $ds1v->setAttribute("ID", "POLICY.0");
 	  $ds1v->setAttribute("MIMETYPE", "text/xml");
 	  $ds1v->setAttribute("LABEL", "POLICY");
 	  //$ds1v->setAttribute("FORMAT_URI","info:fedora/fedora-system:format/xml.fedora.audit");
 	  $content = $dom->createElement("foxml:xmlContent");
 	  $ds1v->appendChild($content);
 	  $content->appendChild($policyElement);
 	  return true;
 	}


 	function handleQDCForm($form_values){
 	  module_load_include('php', 'Fedora_Repository', 'api/fedora_item');
 	  $dom = new DomDocument("1.0","UTF-8");
 	  $dom->formatOutput = true;
 	  $pid=$form_values['pid'];
 	  $rootElement = $dom->createElement("foxml:digitalObject");
 	  $rootElement->setAttribute('VERSION','1.1');
 	  $rootElement->setAttribute('PID',"$pid");
 	  $rootElement->setAttribute('xmlns:foxml',"info:fedora/fedora-system:def/foxml#");
 	  $rootElement->setAttribute('xmlns:xsi',"http://www.w3.org/2001/XMLSchema-instance");
 	  $rootElement->setAttribute('xsi:schemaLocation',"info:fedora/fedora-system:def/foxml# http://www.fedora.info/definitions/1/0/foxml1-1.xsd");
 	  $dom->appendChild($rootElement);
 	  //create standard fedora stuff
 	  $this->createStandardFedoraStuff($form_values,$dom, $rootElement);
 	  //create relationships
 	  $this->createRelationShips($form_values,$dom, $rootElement);
 	  //create dublin core
 	  $this->createQDCStream($form_values,$dom, $rootElement);
 	  if (!empty($form_values['ingest-file-location'])) {
 	    $this->createFedoraDataStreams($form_values,$dom, $rootElement);
 	  }
 	  $collectionPid = $form_values['collection_pid'];
 	  $this->createPolicy($collectionPid, &$dom, &$rootElement);
         
 	  try{
 	    $object = Fedora_Item::ingest_from_FOXML($dom);
            if (!empty($object->pid)) {
              drupal_set_message("Item ".l($object->pid, 'fedora/repository/'.$object->pid)." created successfully.", "status");
            }
            if ( !empty( $_SESSION['fedora_ingest_files'] ) ) {
              foreach($_SESSION['fedora_ingest_files'] as $dsid => $createdFile){
                file_delete($createdFile);
              }
            }
            file_delete($form_values['ingest-file-location']);
 	  }
          catch (exception $e) {
 	    drupal_set_message(t('Error Ingesting Object! ').$e->getMessage(),'error');
 	    watchdog(t("Fedora_Repository"), t("Error Ingesting Object!").$e->getMessage(),NULL, WATCHDOG_ERROR);
 	    return;
 	  }


 	}

 	function createFedoraDataStreams($form_values,&$dom, &$rootElement) {
 	  module_load_include('php', 'Fedora_Repository', 'mimetype');
 	  global $base_url;
 	  $mimetype = new mimetype();
 	  $server=null;
 	  $file=$form_values['ingest-file-location'];

 	  if ( !empty( $file ) ) {
 	    $dformat = $mimetype->getType($file);
 	    $fileUrl = $base_url.'/'.drupal_urlencode($file);
 	    $beginIndex = strrpos($fileUrl,'/');
 	    $dtitle = substr($fileUrl,$beginIndex+1);
 	    $dtitle = urldecode($dtitle);
 	    //		$dtitle =  substr($dtitle, 0, strpos($dtitle, "."));
 	    $ds1 = $dom->createElement("foxml:datastream");
 	    $ds1->setAttribute("ID","OBJ");
 	    $ds1->setAttribute("STATE","A");
 	    $ds1->setAttribute("CONTROL_GROUP","M");
 	    $ds1v= $dom->createElement("foxml:datastreamVersion");
 	    $rootElement->appendChild($ds1);
 	    	
 	    $ds1v->setAttribute("ID","OBJ.0");
 	    $ds1v->setAttribute("MIMETYPE","$dformat");
 	    $ds1v->setAttribute("LABEL","$dtitle");
 	    $ds1content = $dom->createElement('foxml:contentLocation');
 	    $ds1content->setAttribute("REF","$fileUrl");
 	    $ds1content->setAttribute("TYPE","URL");
 	    $ds1->appendChild($ds1v);
 	    $ds1v->appendChild($ds1content);
 	  }
 	  if( !empty	($_SESSION['fedora_ingest_files'])){
 	    foreach($_SESSION['fedora_ingest_files'] as $dsid => $createdFile){
 	      $createdFile = strstr ($createdFile, $file);
 	      $dformat = $mimetype->getType($createdFile);
 	      $fileUrl = $base_url.'/'.drupal_urlencode( $createdFile );
 	      $beginIndex = strrpos($fileUrl,'/');
 	      $dtitle = substr($fileUrl,$beginIndex+1);
 	      $dtitle = urldecode($dtitle);
 	      //				$dtitle =  substr($dtitle, 0, strpos($dtitle, "."));
 	      $dtitle = $dtitle;
 	      $ds1 = $dom->createElement("foxml:datastream");
 	      $ds1->setAttribute("ID","$dsid");
 	      $ds1->setAttribute("STATE","A");
 	      $ds1->setAttribute("CONTROL_GROUP","M");
 	      $ds1v= $dom->createElement("foxml:datastreamVersion");
 	      $ds1v->setAttribute("ID","$dsid.0");
 	      $ds1v->setAttribute("MIMETYPE","$dformat");
 	      $ds1v->setAttribute("LABEL","$dtitle");
 	      $ds1content = $dom->createElement('foxml:contentLocation');
 	      $ds1content->setAttribute("REF","$fileUrl");
 	      $ds1content->setAttribute("TYPE","URL");
 	      $ds1->appendChild($ds1v);
 	      $ds1v->appendChild($ds1content);
 	      $rootElement->appendChild($ds1);
 	    }
 	  }



 	}
 	/**
 	 * creates the RELS-EXT for the foxml
 	 */
 	function createRelationShips($form_values,&$dom,&$rootElement){
 	  $drdf = $dom->createElement("foxml:datastream");
 	  $drdf->setAttribute("ID","RELS-EXT");
 	  $drdf->setAttribute("CONTROL_GROUP","X");
 	  $dvrdf = $dom->createElement("foxml:datastreamVersion");
 	  $dvrdf->setAttribute("FORMAT_URI","info:fedora/fedora-system:FedoraRELSExt-1.0");
 	  $dvrdf->setAttribute("ID","RELS-EXT.0");
 	  $dvrdf->setAttribute("MIMETYPE","application/rdf+xml");
 	  $dvrdf->setAttribute("LABEL","RDF Statements about this Object");
 	  $dvcontent = $dom->createElement("foxml:xmlContent");
 	  $rdf = $dom->createElement("rdf:RDF");
 	  $rdf->setAttribute("xmlns:rdf","http://www.w3.org/1999/02/22-rdf-syntax-ns#");
 	  $rdf->setAttribute("xmlns:rdfs","http://www.w3.org/2000/01/rdf-schema#");
 	  $rdf->setAttribute("xmlns:fedora","info:fedora/fedora-system:def/relations-external#");
 	  $rdf->setAttribute("xmlns:dc","http://purl.org/dc/elements/1.1/");
 	  $rdf->setAttribute("xmlns:oai_dc","http://www.openarchives.org/OAI/2.0/oai_dc/");
 	  $rdf->setAttribute("xmlns:fedora-model","info:fedora/fedora-system:def/model#");
 	  $rdfdesc = $dom->createElement("rdf:description");
 	  $pid = $form_values['pid'];
 	  $rdfdesc->setAttribute("rdf:about","info:fedora/$pid");
 	  $relationship = $form_values['relationship'];
 	  if(!isset($relationship)){
 	    $relationship='fedora:isMemberOfCollection';
 	  }
 	  //$member = $dom->createElement("fedora:isMemberOfCollection");
 	  $member = $dom->createElement("fedora:".$relationship);
 	  $membr = $form_values['collection_pid'];
 	  $member->setAttribute("rdf:resource","info:fedora/$membr");
 	  $rdfHasModel = $dom->createElement("fedora-model:hasModel");
 	  $contentModelPid=$form_values['content_model_pid'];
 	  $rdfHasModel->setAttribute("rdf:resource","info:fedora/$contentModelPid");
 	  $drdf->appendChild($dvrdf);
 	  $dvrdf->appendChild($dvcontent);
 	  $dvcontent->appendChild($rdf);
 	  $rdf->appendChild($rdfdesc);
 	  $rdfdesc->appendChild($member);
 	  $rdfdesc->appendChild($rdfHasModel);
 	  $rootElement->appendChild($drdf);

 	}
 	/**
 	 * creates the standard foxml properties
 	 */
 	function createStandardFedoraStuff($form_values, &$dom,&$rootElement){


 	  /*foxml object properties section */
 	  $objproperties = $dom->createElement("foxml:objectProperties");
 	  $prop2 = $dom->createElement("foxml:property");
 	  $prop2->setAttribute("NAME","info:fedora/fedora-system:def/model#state");
 	  $prop2->setAttribute("VALUE","A");
 	  $prop3 = $dom->createElement("foxml:property");
 	  $prop3->setAttribute("NAME","info:fedora/fedora-system:def/model#label");
 	  $prop3->setAttribute("VALUE",$form_values['dc:title']);
 	  $prop5 = $dom->createElement("foxml:property");
 	  $prop5->setAttribute("NAME","info:fedora/fedora-system:def/model#ownerId");
 	  $prop5->setAttribute("VALUE",$form_values['user_id']);
 	  //$objproperties->appendChild($prop1);
 	  $objproperties->appendChild($prop2);
 	  $objproperties->appendChild($prop3);
 	  $objproperties->appendChild($prop5);
 	  $rootElement->appendChild($objproperties);


 	}


 	function buildQDCForm(&$form,$ingest_form_definition,&$form_values){
 	  $form['indicator2']=array(
				'#type' => 'fieldset',
				'#title' => t('Ingest Digital Object Step #2')
 	  );
 	  foreach($ingest_form_definition->form_elements->element as $element){
 	    $name=strip_tags($element->name->asXML());
 	    $title=strip_tags($element->label->asXML());
 	    $required = strip_tags($element->required->asXML());
 	    $required=strtolower($required);
 	    if($required!='true'){
 	      $required='0';

 	    }

 	    $description = strip_tags($element->description->asXML());
 	    $type = strip_tags($element->type->asXML());
 	    $options=array();
 	    if($element->type=='select'){
 	      foreach($element->authoritative_list->item as $item){
 	        $field = strip_tags($item->field->asXML());
 	        $value = strip_tags($item->value->asXML());
 	        $options["$field"] = $value;
 	      }
 	      $form['indicator2']["$name"]=array(
				'#title' => $title,
				'#required' => $required,
				'#description' => $description,
				'#type' => $type,
				'#options'=>$options
 	      );

 	    }else{
 	      $form['indicator2']["$name"]=array(
				'#title' => $title,
				'#required' => $required,
				'#description' => $description,
				'#type' => $type
 	      );
 	    }
 	  }

 	  return $form;
 	}

}
?>
