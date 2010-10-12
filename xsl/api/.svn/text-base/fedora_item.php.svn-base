<?php

class Fedora_Item {
  public $pid = null; // The $pid of the fedora object represented by an instance of this class.
  public $object_profile = null;
  private $datastreams_list = null; // A SimpleXML object to store a list of this item's datastreams
  private static $connection_helper = null;

  /**
   * Create an object to represent an item in the Fedora repository.
   * Throws a SOAPException if the PID is not in the repository.
   *
   * @param string $pid
   * @return Fedora_Item
   */
  function Fedora_Item( $pid ) {
    drupal_bootstrap( DRUPAL_BOOTSTRAP_FULL );
    module_load_include( 'php', 'Fedora_Repository', 'ConnectionHelper' );
    module_load_include( 'inc', 'Fedora_Repository', 'api/fedora_utils');

    if ( empty( self::$connection_helper ) ) {
      self::$connection_helper = new ConnectionHelper();
    }
    $this->pid = $pid;
    $raw_objprofile = $this->soap_call( 'getObjectProfile', array ( 'pid' => $this->pid, 'asOfDateTime' => "" ) );

    $this->object_profile = (!empty($raw_objprofile) ? $raw_objprofile->objectProfile : '');
    
  }

  function add_datastream_from_file( $datastream_file, $datastream_id, $datastream_label = null, $datastream_mimetype = '', $controlGroup = 'M' ) {
    module_load_include('php', 'fedora_repository', 'mimetype');
    
    if ( empty( $datastream_mimetype ) ) {
      // Get mime type from the file extension.

      $mimetype_helper = new mimetype();
      $datastream_mimetype = $mimetype_helper->getType( $datastream_file );
    }
    $original_path = $datastream_file;
    // Temporarily move file to a web-accessible location.
    file_copy( $datastream_file, file_directory_path() );
    $datastream_url = drupal_urlencode( $datastream_file );
    $url = file_create_url( $datastream_url );

    $return_value = $this->add_datastream_from_url( $url, $datastream_id, $datastream_label, $datastream_mimetype, $controlGroup );

    if ( $original_path != $datastream_file ) {
      file_delete( $datastream_file );
    }

    return $return_value;
  }

  function add_datastream_from_url( $datastream_url, $datastream_id, $datastream_label = null, $datastream_mimetype = '', $controlGroup = 'M' ) {
    if ( empty( $datastream_label ) ) {
      $datastream_label = $datastream_id;
    }

    $params = array( 'pid' => $this->pid,
                     'dsID' => $datastream_id,
                     'altIDs' => null,
                     'dsLabel' => $datastream_label,
                     'versionable' => TRUE,
                     'MIMEType' => $datastream_mimetype,
                     'formatURI' => NULL,
                     'dsLocation' => $datastream_url,
                     'controlGroup' => $controlGroup,
                     'dsState' => 'A',
                     'checksumType' => 'DISABLED',
                     'checksum' => 'none',
                     'logMessage' => 'Ingested object '.$datastream_id );

    return $this->soap_call( 'addDataStream', $params )->datastreamID;
  }

  function add_datastream_from_string($str, $datastream_id, $datastream_label = null, $datastream_mimetype = 'text/xml', $controlGroup = 'M' ) {
    $dir = sys_get_temp_dir();
    $tmpfilename = tempnam($dir, 'fedoratmp');
    $tmpfile = fopen($tmpfilename, 'w');
    fwrite($tmpfile, $str, strlen($str));
    fclose($tmpfile);
    $returnvalue = $this->add_datastream_from_file( $tmpfilename, $datastream_id, $datastream_label, $datastream_mimetype, $controlGroup );
    unlink($tmpfilename);
    return $returnvalue;
  }

  /**
   * Add a relationship string to this object's RELS-EXT.
   * does not support rels-int yet.
   * @param string $relationship
   * @param <type> $object
   */
  function add_relationship( $relationship, $object, $namespaceURI = RELS_EXT_URI ) {


    $ds_list = $this->get_datastreams_list_as_array();
    
    if (empty($ds_list['RELS-EXT'])) {
      $this->add_datastream_from_string('        <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
          <rdf:Description rdf:about="info:fedora/'.$this->pid.'">
          </rdf:Description>
        </rdf:RDF>', 'RELS-EXT', 'Fedora object-to-object relationship metadata', 'text/xml', 'X');
    }

    $relsext = $this->get_datastream_dissemination('RELS-EXT');

    if ( substr($object, 0, 12) != 'info:fedora/') {
      $object = "info:fedora/$object";
    }

    $relsextxml = new DomDocument();
    
    $relsextxml->loadXML($relsext);
    $description = $relsextxml->getElementsByTagNameNS('http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'Description')->item(0);
       // Create the new relationship node.
    $newrel = $relsextxml->createElementNS($namespaceURI,$relationship);
      
    $newrel->setAttribute('rdf:resource', $object);
    
    $description->appendChild($newrel);
    $this->modify_datastream_by_value( $relsextxml->saveXML(), 'RELS-EXT', "Fedora Object-to-Object Relationship Metadata", 'text/xml');
    //print ($description->dump_node());


    /*$params = array( 'pid' => $this->pid,
                     'relationship' => $relationship,
                     'object' => $object,
                     'isLiteral' => FALSE,
                     'datatype' => '',
                   );

    return $this->soap_call( 'addRelationship', $params );
    */
  }

  /**
   * Removes the given relationship from the item's RELS-EXT and re-saves it.
   * @param string $relationship
   * @param string $object
   */
  function purge_relationship( $relationship, $object ) {

    $relsext = $this->get_datastream_dissemination('RELS-EXT');
    $namespaceURI = 'info:fedora/fedora-system:def/relations-external#';
    // Pre-pend a namespace prefix to recognized relationships

    switch ($relationship) {
      case 'rel:isMemberOf':
      case 'fedora:isMemberOf':
        $relationship = "isMemberOf";
        $namespaceURI = 'info:fedora/fedora-system:def/relations-external#';
        break;
      case "rel:isMemberOfCollection":
      case "fedora:isMemberOfCollection":
        $relationship = "isMemberOfCollection";
        $namespaceURI = 'info:fedora/fedora-system:def/relations-external#';
        break;
      case "fedora:isPartOf":
        $relationship = "isPartOf";
        $namespaceURI = 'info:fedora/fedora-system:def/relations-external#';
        break;
    }

    if ( substr($object, 0, 12) != 'info:fedora/') {
      $object = "info:fedora/$object";
    }

    $relsextxml = new DomDocument();
    $relsextxml->loadXML($relsext);
    $modified = FALSE;
    $rels = $relsextxml->getElementsByTagNameNS($namespaceURI, $relationship);
    if (!empty($rels)) {
      foreach ($rels as $rel) {
        if ($rel->getAttributeNS('http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'resource') == $object) {
          $rel->parentNode->removeChild($rel);
          $modified = TRUE;
        }
      }
    }
    if ($modified) {
      $this->modify_datastream_by_value( $relsextxml->saveXML(), 'RELS-EXT', "Fedora Object-to-Object Relationship Metadata", 'text/xml');
    }
    return $modified;
    //print ($description->dump_node());
  }

  function export_as_foxml() {
    $params = array ('pid' => $this->pid,
                     'format' => 'info:fedora/fedora-system:FOXML-1.1',
                     'context' => 'migrate',);
    $result = self::soap_call('export', $params);
    return $result->objectXML;
  }

  /**
   * Does a search using the "query" format followed by the Fedora REST APi.
   *
   * @param string $pattern to search for, including wildcards.
   * @param string $field The field to search on, e.g. pid, title, cDate. See http://www.fedora-commons.org/confluence/display/FCR30/REST+API#RESTAPI-findObjects for details
   * @param int $max_results not used at this time
   * @return Array of pid => title pairs that match the results
   */
  static function find_objects_by_pattern($pattern = '*', $field = 'pid', $max_results = 100 ) {
    module_load_include('inc', 'fedora_repository', 'api/fedora_utils');
    $pattern = drupal_urlencode($pattern);
    $done = FALSE;
    $cursor = 0;
    $session_token = '';
    $i = 0;
    $results = array();
    while (!$done && $i < 5) {
      $i++;
      $url = variable_get('fedora_base_url', 'http://localhost:8080/fedora');
      if ($cursor == 0) {
        $url .= "/objects?query=$field~$pattern&pid=true&title=true&resultFormat=xml&maxResults=$max_results";
      }
      else {
        $url .= "/objects?pid=true&title=true&sessionToken=$session_token&resultFormat=xml&maxResults=$max_results";
      }
      
      $resultxml = do_curl($url);

      libxml_use_internal_errors(TRUE);
      $resultelements = simplexml_load_string($resultxml);
      if ($resultelements === FALSE) {
        libxml_clear_errors();
        break;
      }
      $cursor += count($resultelements->resultList->objectFields);
      if (count($resultelements->resultList->objectFields) < $max_results
          || count($resultelements->resultList->objectFields) == 0) {
        $done = TRUE;
      }
      foreach ($resultelements->resultList->objectFields as $obj ) {
        $results[(string)$obj->pid] = (string)$obj->title;
        $cursor++;
        if ($cursor >= $max_results) {
          $done = TRUE;
          break;
        }
      }
      $session_token = $resultelements->listSession->token;
      $done = !empty($session_token);
    }
    return $results;
  }

  function get_datastream_dissemination($dsid, $as_of_date_time = "") {
    $params = array (
                'pid' => $this->pid,
                'dsID' => $dsid,
                'asOfDateTime' => $as_of_date_time,
              );
    $object = self::soap_call('getDataStreamDissemination', $params);
    if (!empty($object)) {
      $content = $object->dissemination->stream;
      $content = trim($content);
    }
    else {
      $content = "";
    }
    return $content;
  }

  /**
   * Retrieves and returns a SimpleXML list of this item's datastreams, and stores them
   * as an instance variable for caching purposes.
   *
   * @return SimpleXMLElement
   */
  function get_datastreams_list_as_SimpleXML() {
    //if ( empty( $this->datastreams_list ) ) {
      $params = array (
        'pid' => $this->pid,
        'asOfDateTime' => ""
      );

      $this->datastreams_list = $this->soap_call('listDataStreams', $params);
    //}
    return $this->datastreams_list;
  }

  /**
   * Returns an associative array of this object's datastreams. Results look like this:
   *
   *  'DC' =>
   *    array
   *      'label' => string 'Dublin Core Record for this object' (length=34)
   *      'MIMEType' => string 'text/xml' (length=8)
   *  'RELS-EXT' =>
   *    array
   *      'label' => string 'RDF Statements about this object' (length=32)
   *      'MIMEType' => string 'application/rdf+xml' (length=19)
   *
   * @return array
   */
  function get_datastreams_list_as_array() {
    
      $this->get_datastreams_list_as_SimpleXML();
    

    $ds_list = array();
    foreach( $this->datastreams_list->datastreamDef as $ds ) {
      $ds_list[$ds->ID]['label'] = $ds->label;
      $ds_list[$ds->ID]['MIMEType'] = $ds->MIMEType;
      $ds_list[$ds->ID]['URL'] = $this->url().'/'.$ds->ID.'/'.drupal_urlencode( $ds->label );
    }

    return $ds_list;
  }

  /**
   * Returns a MIME type string for the given Datastream ID.
   *
   * @param string $dsid
   * @return string
   */
  function get_mimetype_of_datastream( $dsid ) {
    $this->get_datastreams_list_as_SimpleXML();

    $mimetype = '';
    foreach ($datastream_list as $datastream) {
      foreach ($datastream as $datastreamValue) {
        if ($datastreamValue->ID == $dsid ) {
           return $datastreamValue->MIMEType;
        }
      }
    }

    return '';
  }

  /**
   * Currently the Fedora API call getRelationships is reporting an uncaught
   * exception so we will parse the RELS-EXT ourselves and simulate the
   * documented behaviour.
   * @param String $relationship - filter the results to match this string.
   */
  function get_relationships( $relationship = NULL ) {
    $relationships = array();
    try {
      $relsext = $this->get_datastream_dissemination('RELS-EXT');
    } catch (exception $e) {
      drupal_set_message("Error retrieving RELS-EXT of object $pid", 'error');
      return $relationships;
    }

    // Parse the RELS-EXT into an associative array.
    $relsextxml = new DOMDocument();
    $relsextxml->loadXML($relsext);
    $relsextxml->normalizeDocument();
    $rels = $relsextxml->getElementsByTagNameNS('info:fedora/fedora-system:def/relations-external#', '*');

    foreach ($rels as $child) {
      if (empty($relationship) || preg_match("/$relationship/", $child->tagName)) {
        $relationships[] = array( 'subject' => $this->pid,
                                  'predicate' => $child->tagName,
                                  'object' => substr($child->getAttributeNS('http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'resource'), 12),
                                );
      }
    }
    return $relationships;
    //$children = $relsextxml->RDF->description;
    //$children = $relsextxml->RDF->description;


    //$params = array( 'pid' => $this->pid,
    //                 'relationship' => 'Null' );

    //return $this->soap_call( 'getRelationships', $params );
  }

  /**
   * Creates a RELS-EXT XML stream from the supplied array and saves it to
   * the item on the server.
   * @param <type> $relationships
   */
  function save_relationships( $relationships ) {
    // Verify the array format and that it isn't empty.
    if (!empty($relationships)) {
      $relsextxml = '<rdf:RDF xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:fedora="info:fedora/fedora-system:def/relations-external#" xmlns:fedora-model="info:fedora/fedora-system:def/model#" xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#">'
            . '<rdf:description rdf:about="'.$this->pid.'">';

      foreach ($relationships as $rel) {
        if (empty($rel['subject']) || empty($rel['predicate']) || empty($rel['object']) || $rel['subject'] != 'info:fedora/'.$this->pid ){
          drupal_set_message("Error with relationship format: ".$rel['subject']." - ".$rel['predicate'].' - '.$rel['object'], "error");
          return false;
        }
      }
    }

    // Do the messy work constructing the RELS-EXT XML. Because addRelationship is broken.

    return false;
  }

  /**
   * Removes this object form the repository.
   */
  function purge($log_message = 'Purged using Islandora API.', $force = FALSE) {
    $params = array( 'pid' => $this->pid,
                     'logMessage' => $log_message,
                     'force' => $force );

    return $this->soap_call('purgeObject', $params);
  }

  function purge_datastream( $dsID, $start_date = NULL, $end_date = NULL, $log_message = 'Purged datastream using Islandora API', $force = FALSE) {
    $params = array( 'pid' => $this->pid,
                     'dsID' => $dsID,
                     'startDT' => $start_date,
                     'endDT' => $end_date,
                     'logMessage' => $log_message,
                     'force' => $force, );

    return $this->soap_call('purgeDatastream', $params);
  }

  function url() {
    global $base_url;

    return $base_url.'/fedora/repository/'.$this->pid.(!empty($this->object_profile) ? '/-/'.drupal_urlencode($this->object_profile->objLabel) : '');
  }

  static function get_next_PID_in_namespace( $pid_namespace = '' ) {
    if (empty($pid_namespace)) {
      // Just get the first one in the config settings.
      $allowed_namespaces = explode(" ", variable_get('fedora_pids_allowed', 'demo: changeme: islandora:'));
      $pid_namespace = $allowed_namespaces[0];
      if (!empty($pid_namespace)) {
        $pid_namespace = substr($pid_namespace, 0, strpos($pid_namespace, ":"));
      } else {
        $pid_namespace = 'default';
      }
    }

    $params = array (
      'numPIDs' => '',
      'pidNamespace' => $pid_namespace,
    );

    $result = self::soap_call('getNextPID', $params);
    return $result->pid;
  }

  static function ingest_from_FOXML( $foxml ) {

    $params = array('objectXML' => $foxml->saveXML(), 'format' => "info:fedora/fedora-system:FOXML-1.1", 'logMessage'=>"Fedora Object Ingested");

    $object = self::soap_call( 'ingest', $params );

    return new Fedora_Item( $object->objectPID );
  }

  static function ingest_from_FOXML_file( $foxml_file ) {

    $foxml = new DOMDocument();

    $foxml->load( $foxml_file );

    return self::ingest_from_FOXML( $foxml );
  }

  static function ingest_from_FOXML_files_in_directory( $path ) {
    // Open the directory
    $dir_handle = @opendir($path);
    // Loop through the files
    while ($file = readdir($dir_handle)) {
      if( $file == "." || $file == ".." || strtolower( substr( $file, strlen($file) - 4) ) != '.xml' ) {
        continue;
      }

      try {
        self::ingest_from_FOXML_file( $path.'/'.$file );
      } catch (exception $e) {

      }
    }
    // Close
    closedir($dir_handle);


  }

  function modify_datastream_by_value( $content, $dsid, $label, $mime_type, $force = FALSE) {
    $params = array( 'pid' => $this->pid,
                     'dsID' => $dsid,
                     'altIDs' => NULL,
                     'dsLabel' => $label,
                     'MIMEType' => $mime_type,
                     'formatURI' => NULL,
                     'dsContent' => $content,
                     'checksumType' => 'DISABLED',
                     'checksum' => 'none',
                     'logMessage' => 'Modified by Islandora API.',
                     'force' => $force);
    self::soap_call('modifyDatastreamByValue', $params);
  }

  static function soap_call( $function_name, $params_array ) {
    if ( !self::$connection_helper ) {
      module_load_include('php', 'Fedora_Repository', 'ConnectionHelper');
      self::$connection_helper = new ConnectionHelper();
    }
    switch ( $function_name ) {
      case 'ingest':
      case 'addDataStream':
      case 'addRelationship':
      case 'export':
      case 'getDatastream':
      case 'getNextPID':
      case 'getRelationships':
      case 'modifyDatastreamByValue':
      case 'purgeDatastream':
      case 'purgeObject':
        $soap_client = self::$connection_helper->getSoapClient( variable_get( 'fedora_soap_manage_url', 'http://localhost:8080/fedora/services/management?wsdl' ) );
      try {
          $result = $soap_client->__soapCall( $function_name, array ( 'parameters' => $params_array ) );
      } catch (exception $e) {
         drupal_set_message(t("Error trying to call SOAP function $function_name. Check watchdog logs for more information."), 'error');
         watchdog(t("FEDORA_REPOSITORY"), t("Error Trying to call SOAP function $function_name") . $e, NULL, WATCHDOG_ERROR);
         return NULL;
      }
        break;

      default:
        
        try {
          $soap_client = self::$connection_helper->getSoapClient( variable_get( 'fedora_soap_url', 'http://localhost:8080/fedora/services/access?wsdl' ) );
          $result = $soap_client->__soapCall( $function_name, array ( 'parameters' => $params_array ) );
        } catch (exception $e) {
         watchdog(t("FEDORA_REPOSITORY"),t("Error Trying to call SOAP function $function_name").$e,null,WATCHDOG_ERROR);
         return null;
        }
    }
    return $result;
  }


  /**
   * Creates the minimal FOXML for a new Fedora object, which is then passed to
   * ingest_from_FOXML to be added to the repository.
   *
   * @param string $pid if none given, getnextpid will be called.
   * @param string $state The initial state, A - Active, I - Inactive, D - Deleted
   */
  static function create_object_FOXML( $pid = '', $state = 'A', $label = 'Untitled', $owner = '' ) {
    $foxml = new DOMDocument("1.0", "UTF-8");
    $foxml->formatOutput = TRUE;
    if (empty($pid)) {
      // Call getNextPid
      $pid = self::get_next_PID_in_namespace();
    }
    if (empty($owner)) {
      if (!empty($user->uid)) { // Default to current Drupal user.
        $owner = $user->uid;
      } else { // We are not inside Drupal
        $owner = 'fedoraAdmin';
      }
    }

    $root_element = $foxml->createElement("foxml:digitalObject");
    $root_element->setAttribute("VERSION", "1.1");
    $root_element->setAttribute("PID", $pid);
    $root_element->setAttribute("xmlns:foxml", "info:fedora/fedora-system:def/foxml#");
    $root_element->setAttribute("xmlns:xsl", "http://www.w3.org/2001/XMLSchema-instance");
    $root_element->setAttribute("xsl:schemaLocation", "info:fedora/fedora-system:def/foxml# http://www.fedora.info/definitions/1/0/foxml1-1.xsd");
    $foxml->appendChild($root_element);

    // FOXML object properties section
    $object_properties = $foxml->createElement("foxml:objectProperties");
    $state_property = $foxml->createElement("foxml:property");
    $state_property->setAttribute("NAME", "info:fedora/fedora-system:def/model#state");
    $state_property->setAttribute("VALUE", $state);

    $label_property = $foxml->createElement("foxml:property");
    $label_property->setAttribute("NAME", "info:fedora/fedora-system:def/model#label");
    $label_property->setAttribute("VALUE", $label);

    $owner_property = $foxml->createElement("foxml:property");
    $owner_property->setAttribute("NAME", "info:fedora/fedora-system:def/model#ownerId");
    $owner_property->setAttribute("VALUE", $owner );

    $object_properties->appendChild($state_property);
    $object_properties->appendChild($label_property);
    $object_properties->appendChild($owner_property);
    $root_element->appendChild($object_properties);
    $foxml->appendChild($root_element);

    return $foxml;
  }

  static function ingest_new_item( $pid = '', $state = 'A', $label = '', $owner = '' ) {
    return self::ingest_from_FOXML(self::create_object_FOXML( $pid, $state, $label, $owner));
  }

  static function fedora_item_exists( $pid ) {
    $item = new Fedora_Item($pid);
    return (!empty($item->object_profile));
  }

  /********************************************************
   * Relationship Functions
   ********************************************************/

  /**
   * Returns an associative array of relationships that this item has
   * in its RELS-EXT.
   */
}
?>
