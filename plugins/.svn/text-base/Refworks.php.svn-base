<?php

/*
 * Created on 26-Feb-08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
module_load_include('php', 'fedora_repository', 'SecurityClass');

class Refworks {

  private $romeoUrlString = "";
  private $referencelist;
  private $security_helper;
  private $collection_policy_stream;
  private $issn = '';

  function Refworks() {
    $this->romeoUrlString = "http://www.sherpa.ac.uk/romeo/api24.php?issn=";
  }

  function buildForm( &$form, $ingest_form_definition, &$form_values) {
    $form['indicator2'] = array (
        '#type' => 'fieldset',
        '#title' => t('Ingest Digital Object Step #2'
    ));
    foreach ($ingest_form_definition->form_elements->element as $element) {
      $name = strip_tags($element->name->asXML());
      $title = strip_tags($element->label->asXML());
      $required = strip_tags($element->required->asXML());
      $prefix = strip_tags($element->prefix);
      $required = strtolower($required);
      if ($required != 'true') {
        $required = '0';

      }

      $description = strip_tags($element->description->asXML());
      $type = strip_tags($element->type->asXML());

      $form['indicator2']["$name"] = array (
          '#title' => $title,
          '#required' => $required,
          '#description' => $description,
          '#prefix' => $prefix,
          '#type' => $type
      );
    }

    return $form;
  }

  /**
   * Read the input file and generate a list of Reference items.
   *
   * @param array $form_values
   * @param SimpleXMLElement $dom
   * @return SimpleXMLElement
   */
  function parse_refworks_item( &$form_values ) {

    $file = $form_values['ingest-file-location'];
    try {
      $dom = new DOMDocument('1.0', 'UTF-8');
      $dom->substituteEntities = FALSE;
      $dom->loadXML(trim(file_get_contents($file)));
      $xml = simplexml_import_dom($dom);
      //$xml=simplexml_load_string(trim(file_get_contents($file),Null,true));
      //$dom = dom_import_simplexml($xml);//test to see if it behaves better
      //$xml = new SimpleXMLElement(trim(file_get_contents($file)));
    } catch (Exception $e) {
      drupal_set_message(t('Error Processing Refworks file: ').$e->getMessage());
      return FALSE;
    }
    $this->referencelist = array();
    foreach( $xml->reference as $reference ) {
      array_push( $this->referencelist, $reference );
    }

    return $this->referencelist;
  }
//create A DC stream with ID of DC
  function createQDCStream( &$dom, &$rootElement, $reference ) {

    $datastream = $dom->createElement("foxml:datastream");
    $datastream->setAttribute("ID", "DC");
    $datastream->setAttribute("STATE", "A");
    $datastream->setAttribute("CONTROL_GROUP", "X");
    $version = $dom->createElement("foxml:datastreamVersion");
    $version->setAttribute("ID", "DC.0");
    $version->setAttribute("MIMETYPE", "text/xml");
    $version->setAttribute("LABEL", "DC Dublin Core Record");
    $datastream->appendChild($version);
    $content = $dom->createElement("foxml:xmlContent");
    $version->appendChild($content);
    ///begin writing qdc
    $oai = $dom->createElement("oai_dc:dc");
    $oai->setAttribute('xmlns:oai_dc', "http://www.openarchives.org/OAI/2.0/oai_dc/");
    $oai->setAttribute('xmlns:dc', "http://purl.org/dc/elements/1.1/");
    $oai->setAttribute('xmlns:dcterms', "http://purl.org/dc/terms/");
    $oai->setAttribute('xmlns:xsi', "http://www.w3.org/2001/XMLSchema-instance");
    $content->appendChild($oai);

    foreach ($reference->sr as $value) {
      $element = $dom->createElement('dc:type', $value);
      $oai->appendChild($element);
    }
    foreach ($reference->id as $value) {
      $identifier = $value;
    }
    foreach ($reference->a1 as $value){
      $element = $dom->createElement('dc:creator', $value);
      $oai->appendChild($element);
    }
    foreach ($reference->t1 as $value) {
    //			$form_values['dc:title'] = $value;
      $element = $dom->createElement('dc:title', $value);
      $oai->appendChild($element);
    }
    foreach ($reference->jf as $value) {
      $source = $value;
    }
    foreach ($reference->yr as $value) {
      $element = $dom->createElement('dc:date', $value);
      $oai->appendChild($element);
    }
    foreach ($reference->vo as $value) {
      $source .= ' Volume: ' . $value;
    }
    foreach ($reference->is as $value) {
      $source .= ' Issue: ' . $value;
    }
    foreach ($reference->sp as $value) {
      $source .= ' Start Page: ' . $value;
    }
    foreach ($reference->op as $value) {
      $source .= ' Other Pages: ' . $value;
    }
    foreach ($reference->ul as $value) {
      $source .= ' URL: ' . $value;
    }
    foreach ($reference->k1 as $value) {
      $element = $dom->createElement('dc:subject', $value);
      $oai->appendChild($element);
    }
    foreach ($reference->a2 as $value) {
      $element = $dom->createElement('dc:contributor', $value);
      $oai->appendChild($element);
    }
    foreach ($reference->a3 as $value) {
      $element = $dom->createElement('dc:contributor', $value);
      $oai->appendChild($element);
    }
    foreach ($reference->a4 as $value) {
      $element = $dom->createElement('dc:contributor', $value);
      $oai->appendChild($element);
    }
    foreach ($reference->a5 as $value) {
      $element = $dom->createElement('dc:contributor', $value);
      $oai->appendChild($element);
    }
    foreach ($reference->la as $value) {
      $element = $dom->createElement('dc:language', $value);
      $oai->appendChild($element);
    }
    foreach ($reference->pb as $value) {
      $source = 'Publisher: ' . $value;
    }
    foreach ($reference->pp as $value) {
      $source .= ' Place of Publication: ' . $value;
    }
    foreach ($reference->sn as $value) {
      $identifier .= ' ISSN/ISBN: ' . $value;
      //$this->romeoUrlString = $this->romeoUrlString . $value;
      if(!$this->issn==''){
        $this->issn=$value;
      }else{
        $this->issn .= ','.$value;
      }
    }
    foreach ($reference->ab as $value) {
      $description = ' abstract: ' . $value;
    }
    foreach ($reference->cr as $value) {
      $description .= ' Cited Reference: ' . $value;
    }
    $element = $dom->createElement('dc:description', $description);
    $oai->appendChild($element);
    $element = $dom->createElement('dc:source', $source);
    $oai->appendChild($element);
    $element = $dom->createElement('dc:identifier', $identifier);
    $oai->appendChild($element);
    //$rootElement->appendChild($datastream);
    return $datastream;
  }

  function handleForm( &$form_values ) {
    $errorMessage = NULL;
    module_load_include( 'php', 'Fedora_Repository', 'CollectionClass' );
    module_load_include( 'php', 'Fedora_Repository', 'ContentModel' );
    module_load_include('php', 'fedora_repository', 'api/fedora_item');
    $content_model_pid = ContentModel::getPidFromIdentifier( $form_values['models'] );
    $content_model_dsid = ContentModel::getDSIDFromIdentifier( $form_values['models'] );
    $collectionHelper = new CollectionClass();
      $startTime=time();
    $collection_pid = $form_values['collection_pid'];

    $this->parse_refworks_item( $form_values );

    $this->security_helper = new SecurityClass();

    $collection_item = new Fedora_Item($collection_pid);
    $this->collection_policy_stream = $collection_item->get_datastream_dissemination('CHILD_SECURITY');
    if (empty($this->collection_policy_stream)) {
      $this->collection_policy_stream = file_get_contents(drupal_get_path('module', 'fedora_repository').'/policies/noObjectEditPolicy.xml');
    }
    $success = 0;
    $errors = 0;
    foreach( $this->referencelist as $reference ) {
      $dom = new DomDocument("1.0", "UTF-8");
      $dom->substituteEntities = FALSE;
      $dom->formatOutput = true;
      $pid = $collectionHelper->getNextPid( $collection_pid, $content_model_dsid );

 	  $rootElement = $dom->createElement("foxml:digitalObject");
 	  $rootElement->setAttribute('VERSION','1.1');
 	  $rootElement->setAttribute('PID',"$pid");
 	  $rootElement->setAttribute('xmlns:foxml',"info:fedora/fedora-system:def/foxml#");
 	  $rootElement->setAttribute('xmlns:xsi',"http://www.w3.org/2001/XMLSchema-instance");
 	  $rootElement->setAttribute('xsi:schemaLocation',"info:fedora/fedora-system:def/foxml# http://www.fedora.info/definitions/1/0/foxml1-1.xsd");
 	  $dom->appendChild($rootElement);
      //create standard fedora stuff

      $qdc_element = $this->createQDCStream( $dom, $rootElement, $reference );
      if (!$qdc_element) {
        drupal_set_message(t('Error Create DC for Refworks'), 'error');
        continue;
      }
      $item_title='';
      foreach ($reference->t1 as $value) {
        $item_title .= ' --- '.$value;
      }
      $this->createStandardFedoraStuff($form_values, $dom, $rootElement, $reference );
      $rootElement->appendChild($qdc_element);
      //create relationships
      $this->createRelationShips($form_values, $dom, $rootElement, $pid);
      //create dublin core

      $this->createFedoraDataStreams($form_values, $dom, $rootElement, $reference);

      if (!empty ( $this->collection_policy_stream)) {
        $this->create_security_policies($dom, $rootElement, $reference);
      }

      $params = array (
          'objectXML' => $dom->saveXML(), 'format' => 'info:fedora/fedora-system:FOXML-1.1', 'logMessage' => "Fedora Object Ingested");

      try {
        $soapHelper = new ConnectionHelper();
        $client = $soapHelper->getSoapClient(variable_get('fedora_soap_manage_url', 'http://localhost:8080/fedora/services/management?wsdl'));

        if ($client == null) {
          drupal_set_message(t('Error Getting Soap Client.'), 'error');
          watchdog(t("FEDORA_REPOSITORY"),t("Error Getting SOAP client").$e,null,WATCHDOG_ERROR);
          return;
        }
        $object = $client->__soapCall('ingest', array (
            $params
        ));
        watchdog(t("FEDORA_REPOSITORY"),t("Successfully added repository item ").$pid.' - '.$item_title,null,WATCHDOG_INFO);
        $deleteFiles = $form_values['delete_file']; //remove files from drupal file system

        if ($deleteFiles > 0) {
          unlink($form_values['fullpath']);
        }
      } catch (exception $e) {
        $errors++;
        $errorMessage = 'yes';
         //drupal_set_message(t('Error Ingesting Object! Check Drupal watchdog logs for more info' . $e->getMessage()), 'error');
        watchdog(t("FEDORA_REPOSITORY"),t("Error during ingest").$item_title.' '.$e,null,WATCHDOG_ERROR);
        //return ' ';
      }     
      $success++;
    }
     if(isset($errorMessage)){
        drupal_set_message(t('Error Ingesting one or more records! Check Drupal watchdog logs for more info') , 'error');
      }
       $endTime=time();
         drupal_set_message(t('Successfull ingest of %success records.  %errors records failed.  Ingest took %seconds seconds',array('%success'=>$success-$errors,'%errors'=>$errors,'%seconds'=>$endTime-$startTime)) , 'info');
        
         //drupal_set_message(t('ingest took %seconds ',array('%seconds'=>$endTime-$startTime)),'info');
  }

  /**
   * creates the RELS-EXT for the foxml
   */
  function createRelationShips( $form_values, &$dom, &$rootElement, $pid = NULL ) {
    $drdf = $dom->createElement("foxml:datastream");
    $drdf->setAttribute("ID", "RELS-EXT");
    $drdf->setAttribute("CONTROL_GROUP", "X");
    $dvrdf = $dom->createElement("foxml:datastreamVersion");
    $dvrdf->setAttribute("ID", "RELS-EXT.0");
    $dvrdf->setAttribute("MIMETYPE", "text/xml");
    $dvrdf->setAttribute("LABEL", "Fedora Object-to-Object Relationship Metadata");
    $dvcontent = $dom->createElement("foxml:xmlContent");
    $rdf = $dom->createElement("rdf:RDF");
    $rdf->setAttribute("xmlns:rdf", "http://www.w3.org/1999/02/22-rdf-syntax-ns#");
    $rdf->setAttribute("xmlns:rdfs", "http://www.w3.org/2000/01/rdf-schema#");
    $rdf->setAttribute("xmlns:fedora", "info:fedora/fedora-system:def/relations-external#");
    $rdf->setAttribute("xmlns:fedora-model", "info:fedora/fedora-system:def/model#");
    //$rdf->setAttribute("xmlns:dc", "http://purl.org/dc/elements/1.1/");
    //$rdf->setAttribute("xmlns:oai_dc", "http://www.openarchives.org/OAI/2.0/oai_dc/");
    $rdfdesc = $dom->createElement("rdf:description");
    $rdfdesc->setAttribute("rdf:about", "info:fedora/$pid");
    $member = $dom->createElement("fedora:isMemberOfCollection");
    $membr = $form_values['collection_pid'];
    $member->setAttribute("rdf:resource", "info:fedora/$membr");
    $model = $dom->createElement("fedora-model:hasModel");
    $model->setAttribute("rdf:resource", "info:fedora/islandora:refworksCModel");

    $drdf->appendChild($dvrdf);
    $dvrdf->appendChild($dvcontent);
    $dvcontent->appendChild($rdf);
    $rdf->appendChild($rdfdesc);
    $rdfdesc->appendChild($member);
    $rdfdesc->appendChild($model);
    $rootElement->appendChild($drdf);

  }

  function createRomeoDataStream(& $dom, &$rootElement) {
    $ds1 = $dom->createElement("foxml:datastream");
    $ds1->setAttribute("ID", "ROMEO");
    $ds1->setAttribute("STATE", "A");
    $ds1->setAttribute("CONTROL_GROUP", "E");
    $ds1v = $dom->createElement("foxml:datastreamVersion");
    $ds1v->setAttribute("ID", "ROMEO.0");
    $ds1v->setAttribute("MIMETYPE", "text/xml");
    $ds1v->setAttribute("LABEL", "ROMEO");
    $ds1content = $dom->createElement('foxml:contentLocation');
    $url = $this->romeoUrlString.$this->issn;
    $this->issn='';//clear the issn's for next ingest in case we are doing batch
    $ds1content->setAttribute("REF", "$url");
    $ds1content->setAttribute("TYPE", "URL");
    $ds1->appendChild($ds1v);
    $ds1v->appendChild($ds1content);
    $rootElement->appendChild($ds1);
  }

  function createFedoraDataStreams($form_values, &$dom, &$rootElement, $reference) {
    global $base_url;
    module_load_include('php', 'Fedora_Repository', 'mimetype');
    $mimetype = new mimetype();
    $server = null;
    $this->createRomeoDataStream($dom, $rootElement);
    $ds1 = $dom->createElement("foxml:datastream");
    $ds1->setAttribute("ID", "refworks");
    $ds1->setAttribute("STATE", "A");
    $ds1->setAttribute("CONTROL_GROUP", "X");
    $ds1v = $dom->createElement("foxml:datastreamVersion");
    $ds1v->setAttribute("ID", "refworks.0");
    $ds1v->setAttribute("MIMETYPE", "text/xml");
    $ds1v->setAttribute("LABEL", "Refworks datastream");
    $ds1content = $dom->createElement("foxml:xmlContent");

    $ds1content->appendChild( $dom->importNode( dom_import_simplexml( $reference ), TRUE ));
    $ds1->appendChild($ds1v);
    $ds1v->appendChild($ds1content);
    $rootElement->appendChild($ds1);

    $count = 0;


  }

  /**
   * creates the standard foxml properties
   */
  function createStandardFedoraStuff( $form_values, &$dom, &$rootElement, $reference) {
		/*foxml object properties section */
    $objproperties = $dom->createElement("foxml:objectProperties");
//    $prop1 = $dom->createElement("foxml:property");
//    $prop1->setAttribute("NAME", "http://www.w3.org/1999/02/22-rdf-syntax-ns#type");
//    $prop1->setAttribute("VALUE", "FedoraObject");
    $prop2 = $dom->createElement("foxml:property");
    $prop2->setAttribute("NAME", "info:fedora/fedora-system:def/model#state");
    $prop2->setAttribute("VALUE", "A");
    $prop3 = $dom->createElement("foxml:property");
    $prop3->setAttribute("NAME", "info:fedora/fedora-system:def/model#label");
    $label = $reference->t1;
    if (strlen($label) > 254) {
      $label = substr($label, 0, 245);
    //$label.=$label.'...';
    }
    $prop3->setAttribute("VALUE", $label);
//    $prop4 = $dom->createElement("foxml:property");
//    $prop4->setAttribute("NAME", "info:fedora/fedora-system:def/model#contentModel");
//    $prop4->setAttribute("VALUE", $form_values['content_model_name']);
    $prop5 = $dom->createElement("foxml:property");
    $prop5->setAttribute("NAME", "info:fedora/fedora-system:def/model#ownerId");
    $prop5->setAttribute("VALUE", $form_values['user_id']);
//    $objproperties->appendChild($prop1);
    $objproperties->appendChild($prop2);
    $objproperties->appendChild($prop3);
//    $objproperties->appendChild($prop4);
    $objproperties->appendChild($prop5);
    $rootElement->appendChild($objproperties);

  }
  /**
   * Read the list of Users from the U1 field and Roles from the U2 field and add elements
   * to the security policy record for this item, then add the record as the security policy datastream.
   *
   * @param array $form_values
   * @param DOMDocument $dom
   * @param  $rootElement
   * @param SimpleXMLElement $reference
   */
  function create_security_policies( $dom, &$rootElement, $reference ) {

  
    global $user;
    
    $ds1 = $dom->createElement("foxml:datastream");
    $ds1->setAttribute("ID", "POLICY");
    $ds1->setAttribute("STATE", "A");
    $ds1->setAttribute("CONTROL_GROUP", "X");
    $ds1v = $dom->createElement("foxml:datastreamVersion");
    $ds1v->setAttribute("ID", "POLICY.0");
    $ds1v->setAttribute("MIMETYPE", "text/xml");
    $ds1v->setAttribute("LABEL", "POLICY Record");
    $ds1content = $dom->createElement( "foxml:xmlContent" );
    
    $custom_policy = $this->collection_policy_stream;
    $allowed_users_and_roles = array();
    $allowed_users_and_roles['users'] = array();
    $allowed_users_and_roles['roles'] = array();
    foreach ($reference->u1 as $namelist) {
      foreach(explode(';',strip_tags($namelist->asXML())) as $name){
      //foreach (preg_split("/[\s,;]+/", strip_tags($namelist->asXML()), null, PREG_SPLIT_NO_EMPTY) as $name) {
        array_push( $allowed_users_and_roles['users'], $name );
      }
    }
    if ( empty( $reference->u1 ) ) {
      // If no "u1" value exists, add the currently logged-in user to the item's security policy.
      array_push( $allowed_users_and_roles['users'], $user->name );
    }
    
    foreach ( $reference->u2 as $rolelist ) {
      foreach(explode(';',strip_tags($rolelist->asXML())) as $role){
     // foreach (preg_split("/[\s,;]+/", strip_tags($rolelist->asXML()), null, PREG_SPLIT_NO_EMPTY) as $role) {
        array_push( $allowed_users_and_roles['roles'], $role );
      }
    }
    $custom_policy = $this->security_helper->set_allowed_users_and_roles( $custom_policy, $allowed_users_and_roles );
    $custom_policy_sxe = new SimpleXMLElement( $custom_policy );
    $ds1->appendChild($ds1v);
    $ds1v->appendChild($ds1content);

    $rootElement->appendChild($ds1);
    $ds1content->appendChild( $dom->importNode( dom_import_simplexml( $custom_policy_sxe ), TRUE ));    
  }
 
}
?>