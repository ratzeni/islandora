<?php

/*
 * Created on Feb 1, 2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class ObjectHelper {

//allowed operations
  public static $VIEW_FEDORA = 'view fedora collection';
  public static $EDIT_FEDORA_METADATA = 'edit fedora meta data';
  public static $PURGE_FEDORA_OBJECTSANDSTREAMS = 'purge objects and datastreams';
  public static $ADD_FEDORA_STREAMS = 'add fedora datastreams';
  public static $INGEST_FEDORA_OBJECTS = 'ingest new fedora objects';
  public static $EDIT_TAGS_DATASTREAM = 'edit tags datastream';


  //TODO: Make this into a static member constant
  public $availableDataStreamsText = 'Detailed List of Content';

  function ObjectHelper( ) {
    drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
    module_load_include('php', 'Fedora_Repository', 'ConnectionHelper');
    $connectionHelper = new ConnectionHelper();
  //$this->fedoraUser = $connectionHelper->getUser();
  //$this->fedoraPass = $connectionHelper->getPassword();
  }

  /**
   *Grabs a stream from fedora sets the mimetype and returns it. $dsID is the
   *datastream id.
   *@param $pid String
   *@param $dsID String
   */
  function makeObject($pid, $dsID, $asAttachment = false, $label = null) {
    global $user;
    if ($pid == null || $dsID == null) {
      drupal_set_message(t("no pid or dsid given to create an object with!"));
      return ' ';
    }

    if (!fedora_repository_access(OBJECTHELPER :: $VIEW_FEDORA, $pid,$user)) {
      drupal_set_message(t("You do not have access to Fedora Objects within the attempted namespace."), 'error');
      return ' ';
    }

    if ((!isset ($user)) || $user->uid == 0) {
      $fedoraUser = 'anonymous';
      $fedoraPass = 'anonymous';
    } else {
      $fedoraUser = $user->name;
      $fedoraPass = $user->pass;
    }
    $mimeType = $this->getMimeType($pid, $dsID);
    if (function_exists("curl_init")) {
      if (!isset ($mimeType)) {
        $pid = variable_get('fedora_default_display_pid', 'demo:10');
        $dsID = variable_get('fedora_default_display_dsid', 'TN');
        $mimeType = 'image/jpeg';
      }

      $url = variable_get('fedora_base_url', 'http://localhost:8080/fedora') . '/get/' . $pid . '/' . $dsID;
      $ch = curl_init();
      $user_agent = "Mozilla/4.0 pp(compatible; MSIE 5.01; Windows NT 5.0)";
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_FAILONERROR, 1); // Fail on errors
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // allow redirects
      curl_setopt($ch, CURLOPT_TIMEOUT, 15); // times out after 15s
      curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_USERPWD, "$fedoraUser:$fedoraPass");
      // There seems to be a bug in Fedora 3.1's REST authentication, removing this line fixes the authorization denied error.
      //            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0); // return into a variable

      curl_setopt($ch, CURLOPT_URL, $url);
      header("Content-type: $mimeType");
      if ($asAttachment) {
        $suggestedFileName = "$label";
        $pos = strpos($suggestedFileName, '.');
        if ($pos === false) {
          $ext = strstr($mimeType, '/');
          $ext = substr($ext, 1);
          $suggestedFileName = "$label.$ext";

        }

        header('Content-Disposition: attachment; filename="' . $suggestedFileName . '"');

      }
      curl_exec($ch);
      curl_close($ch);
    } else {
      drupal_set_message(t('No curl support.'), 'error');
    }

  }

  //Gets collection objects t
  function getCollectionInfo($pid,$query=null) {
    module_load_include('php', 'Fedora_Repository', 'CollectionClass');
    $collectionClass= new CollectionClass();
    $results = $collectionClass->getRelatedItems($pid, $query);
    return $results;
  }

  /**
   * Gets a SimpleXMLElement object containing a list of datastreams contained within $pid
   *
   * @param unknown_type $pid
   * @return SimpleXMLElement
   */
  function get_datastreams_list_asSimpleXML( $pid ) {
    module_load_include('php', 'Fedora_Repository', 'ConnectionHelper');
    $soapHelper = new ConnectionHelper();
    $client = $soapHelper->getSoapClient(variable_get('fedora_soap_url', 'http://localhost:8080/fedora/services/access?wsdl'));
    //$object = null;
    $params = array (
        'pid' => $pid,
        'asOfDateTime' => ""
    );
    if (!isset ($client)) {
      drupal_set_message(t('Error connection to Fedora using Soap Client!'));
      return null;
    }
    try {
      $object = $client->__soapCall('listDataStreams', array (
          'parameters' => $params
      ));
    } catch (exception $e) {
      drupal_set_message($e->getMessage());
      return null;
    }
    return $object;
  }

  /**
   * returns the mime type
   */
  function getMimeType($pid, $dsID) {
    global $user;

    if ( empty( $pid ) || empty( $dsID ) ) {
      drupal_set_message(t('You must specify an Object pid and datastream ID.'), 'error');
      return '';
    }
    if (!fedora_repository_access(ObjectHelper :: $VIEW_FEDORA, $pid, $user)) {
      drupal_set_message(t('You do not have the appropriate permissions'), 'error');
      return;
    }

    $datastream_list = $this->get_datastreams_list_asSimpleXML($pid);
    if ( !isset ( $datastream_list ) ) {
      drupal_set_message( t("No datastreams available") );
      return ' ';
    }

    foreach ($datastream_list as $datastream) {
      foreach ($datastream as $datastreamValue) {
        if ($datastreamValue->ID == $dsID ) {
          return $datastreamValue->MIMEType;
        }
      }
    }

    return '';
  }

  /**
   * internal function
   * @param $pid String
   * @param $dataStreamValue Object
   */
  function create_link_for_ds($pid, $dataStreamValue) {
    global $base_url;
    $path = drupal_get_path('module', 'Fedora_Repository');
    if (user_access(ObjectHelper :: $PURGE_FEDORA_OBJECTSANDSTREAMS)) {
      $purgeImage = '<a title="purge datastream ' . $dataStreamValue->label . '" href="' . $base_url . '/fedora/repository/purgeStream/' . $pid . '/' . $dataStreamValue->ID . '/' . $dataStreamValue->label . '"><img src="' . $base_url . '/' . $path . '/images/purge.gif" alt="purge datastream" /></a>';
    } else {
      $purgeImage = '&nbsp;';
    }
    $fullPath = base_path() . $path;
    $content = '';
    $id = $dataStreamValue->ID;
    $label = $dataStreamValue->label;
    $label = str_replace("_", " ", $label);
    $mimeType = $dataStreamValue->MIMEType;

    $view = '<a href="' . $base_url . '/fedora/repository/' . drupal_urlencode( $pid ) . '/' . $id . '/' . drupal_urlencode( $label ) . '" target="_blank" >' . t('View') . '</a>';
    $download = '<a href="' . $base_url . '/fedora/repository/object_download/' . drupal_urlencode( $pid ) . '/' . $id . '/' . drupal_urlencode( $label ) . '" target="_blank" >' . t('Download') . '</a>';
    $content .= "<tr><td>$label</td><td>&nbsp;$view</td><td>&nbsp;$download</td><td>&nbsp;$mimeType</td><td>&nbsp;$purgeImage</td></tr>\n";
    //$content .= "<tr><td><b>Mime Type :</b></td><td>$mimeType</td></tr>\n";

    return $content;

  }

  /**
   * Queries fedora for what we call the qualified dublin core.  Currently only dc.coverage has
   * any qualified fields
   * Transforms the returned xml to html
   * This is the default metadata view.  With icons for searching a dublin core field
   * @param $pid String
   * @return String
   */
  function getQDC($pid) {

    global $base_url;
    $path = drupal_get_path('module', 'Fedora_Repository');
    module_load_include('php', 'Fedora_Repository', 'ConnectionHelper');

    $soapHelper = new ConnectionHelper();
    $client = $soapHelper->getSoapClient(variable_get('fedora_soap_url', 'http://localhost:8080/fedora/services/access?wsdl'));

    $dsId = 'QDC';
    $params = array (
        'pid' => "$pid",
        'dsID' => "$dsId",
        'asOfDateTime' => ""
    );
    try {
      $object = $client->__soapCAll('getDatastreamDissemination', array (
          'parameters' => $params
      ));
    } catch (Exception $e) {
      try { //probably no QDC so we will try for the DC stream.
        $dsId = 'DC';
        $params = array (
            'pid' => "$pid",
            'dsID' => "$dsId",
            'asOfDateTime' => ""
        );
        $object = $client->__soapCAll('getDatastreamDissemination', array (
            'parameters' => $params
        ));
      } catch (exception $e2) {
        drupal_set_message($e2->getMessage(), 'error');
        return;
      }
    }
    $xmlstr = $object->dissemination->stream;
    try {
      $proc = new XsltProcessor();
    } catch (Exception $e) {
      drupal_set_message($e->getMessage(), 'error');
      return;
    }

    $proc->setParameter('', 'baseUrl', $base_url);
    $proc->setParameter('', 'path', $base_url . '/' . $path);
    $input = null;
    $xsl = new DomDocument();
    try {
      $xsl->load($path . '/xsl/convertQDC.xsl');
      $input = new DomDocument();
      $input->loadXML(trim($xmlstr));
    } catch (exception $e) {
      watchdog(t("Fedora_Repository"), t("Problem loading xsl file!").$e,NULL,WATCHDOG_ERROR);

    }
    $xsl = $proc->importStylesheet($xsl);
    $newdom = $proc->transformToDoc($input);
    $output = $newdom->saveXML();
    $baseUrl = base_path();
    //$baseUrl=substr($baseUrl, 0, (strpos($baseUrl, "/")-1));
    if (user_access(ObjectHelper :: $EDIT_FEDORA_METADATA)) {
      $output .= '<br /><a title = "' . t('Edit Meta Data') . '" href="' . $base_url . '/fedora/repository/' . 'editmetadata/' . $pid . '/' . $dsId . '"><img src="' . $base_url . '/' . $path . '/images/edit.gif" alt="' . t('Edit Meta Data') . '" /></a>';
    }
    return $output;
  }

  /**
   * Gets a list of datastreams from an object using its pid
   *
   * We make some assumptions here.  We have implemented a policy that
   * we ingest in our repository will have TN (thumbnail) datastream.  Even audio
   * will have a picture of a speaker or something.  This is not critical
   * but makes searches etc. look better if there is a TN stream.
   * This diplays all the streams in a collapsed fieldset at the bottom of the object page.
   * you can implement a content model if you would like certain streams displayed in certain ways.
   * @param $object_pid String
   * @return String
   *
   */
  function get_formatted_datastream_list($object_pid, $contentModels) {
    global $fedoraUser, $fedoraPass, $base_url;
    module_load_include('php', 'Fedora_Repository', 'ConnectionHelper');
    module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
    $availableDataStreamsText = 'Detailed List of Content';
    //$metaDataText='Description';
    $path = drupal_get_path('module', 'Fedora_Repository');
    $mainStreamLabel = null;
    $object = $this->get_datastreams_list_asSimpleXML($object_pid);
    if (!isset ($object)) {
      drupal_set_message(t("No datastreams available"));
      return ' ';
    }
    $hasOBJStream = null;
    $hasTNStream = FALSE;
    $dataStreamBody = "<br /><table>\n";


    $dataStreamBody .= $this->get_parent_objects_asHTML($object_pid);
    $dataStreamBody .= '<tr><th colspan="4"><h3>' . t("$availableDataStreamsText") . '</h3></th></tr>';
    foreach ($object as $datastream) {
      foreach ($datastream as $datastreamValue) {
        if ($datastreamValue->ID == 'OBJ') {
          $hasOBJStream = '1';
          $mainStreamLabel = $datastreamValue->label;
          $mainStreamLabel = str_replace("_", " ", $mainStreamLabel);

        }
        if ($datastreamValue->ID == 'TN') {
          $hasTNStream = TRUE;
        }
        //create the links to each datastream
        $dataStreamBody .= $this->create_link_for_ds($object_pid, $datastreamValue); //"<tr><td><b>$key :</b></td><td>$value</td></tr>\n";

      }
    }
    $dataStreamBody .= "</table>\n";
    //if they have access let them add a datastream
    if (user_access(ObjectHelper :: $ADD_FEDORA_STREAMS)) {
      $dataStreamBody .= drupal_get_form('add_stream_form', $object_pid);
    }
    $content='';
    //var_dump($contentModels);
    if ( empty( $contentModels )) {
    //only show this stuff if there are no contentmodels
      if (isset ($hasOBJStream)) {
        $content .= '<a href="' . base_path() . 'fedora/repository/' . $object_pid . '/OBJ/' . $mainStreamLabel . '" target="_blank" >';
        $content .= '<img src="' . base_path() . 'fedora/repository/' . $object_pid . '/TN" alt="Thumbnail"/>';
        $content .= '</a>';
      } else {
      //don't use thumbnail as link, we don't know which datastream to link to
        $content .= '<img src="' . base_path() . 'fedora/repository/' . $object_pid . '/TN" alt="' . $object_pid . '"/>';
      }
    }

    foreach($contentModels as $contentModel) {
      $content .= $this->createExtraFieldsets($object_pid, $contentModel);
    }
    $fieldset = array (
        '#title' => t("$availableDataStreamsText"
        ), '#collapsible' => TRUE, '#collapsed' => TRUE, '#value' => $dataStreamBody);
    $content .= theme('fieldset', $fieldset);

    if (user_access(ObjectHelper :: $PURGE_FEDORA_OBJECTSANDSTREAMS)) {
      $purgeObject = '<a title="' . t('purge object ') . $object_pid . '" href="' . base_path() . 'fedora/repository/purgeObject/' . $object_pid . '"><img hspace = "8" src="' . $base_url . '/' . $path . '/images/purge_big.png" alt="' . t('purge Object') . '"></a>';
    } else {
      $purgeObject = '&nbsp;';
    }

    $content .= $purgeObject;
    return $content;

  }
  /**
   * Queries a collection object for an xslt to format how the
   * collection of objects is displayed.
   */
  function getXslContent($pid, $path, $canUseDefault = true) {
    module_load_include('php', 'Fedora_Repository', 'CollectionClass');
    $collectionClass = new CollectionClass();
    $xslContent = $collectionClass->getCollectionViewStream($pid);
    if (!$xslContent && $canUseDefault) { //no xslt so we will use the default sent with the module
      $xslContent = file_get_contents($path . '/xsl/sparql_to_html.xsl');
    }
    return $xslContent;

  }

  /**
   * returns a stream from a fedora object given a pid and dsid
   *
   */
  function getStream($pid, $dsid, $showError = 1) {
    module_load_include('php', 'Fedora_Repository', 'ConnectionHelper');
    $soapHelper = new ConnectionHelper();
    try {
      $client = $soapHelper->getSoapClient(variable_get('fedora_soap_url', 'http://localhost:8080/fedora/services/access?wsdl'));
      $params = array (
          'pid' => "$pid",
          'dsID' => "$dsid",
          'asOfDateTime' => ""
      );

      if (!isset ($client)) {
        drupal_set_message(t('Error connection to Fedora using Soap Client!'));
        return null;
      }
      $object = $client->__soapCall( 'getDatastreamDissemination', array ('parameters' => $params) );
    } catch (Exception $e) {
      if ($showError) {
        drupal_set_message(t('Error getting Datastream for %pid and %datastream<br />', array ('%pid' => $pid, '%datastream' => $dsid ) ), 'error');
      }
      return null;
    }
    $content = $object->dissemination->stream;
    $content = trim($content);
    return $content;
  }
  /*
   * gets the name of the content models for the specified object
   * this now returns an array of pids as in Fedora 3 we can have more then one Cmodel for an object
   */
  function get_content_models_list( $pid, $include_fedora_system_content_models = FALSE ) {

    module_load_include('php', 'Fedora_Repository', 'CollectionClass');
    $collectionHelper = new CollectionClass();

    $pids = array();

    $query='select $object from <#ri>
            where <info:fedora/'.$pid.'> <fedora-model:hasModel> $object
            and $object <fedora-model:state> <info:fedora/fedora-system:def/model#Active>';
    $content_models = $collectionHelper->getRelatedItems($pid,$query);

    if ( empty( $content_models ) ) {
      return $pids;
    }

    try {
      $sxml = new SimpleXMLElement( $content_models );
    } catch ( exception $e ) {
      watchdog( t( "Fedora_Repository" ), t("Could not find a parent object for %s", $pid),NULL, WATCHDOG_ERROR );
      return $pids;
    }

    if( !isset( $sxml ) ) {
      return $pids;
    }

    foreach( $sxml->xpath( '//@uri' ) as $uri ) {
      if ( strpos( $uri, 'fedora-system' ) != FALSE && $include_fedora_system_content_models == FALSE ) {
        continue;
      }
      $pids[] = substr( strstr( $uri, '/' ), 1 );
    }

    return $pids;
  }

  //$fileObject is a drupal $fileObject
  //if xacmlOnly = true we will bypass the drupal permissions FOR IR purposes
  function addStream($pid,$dsid, $fileObject,$xacmlOnly=false) {
    global $user;

    if(!fedora_repository_access(OBJECTHELPER :: $ADD_FEDORA_STREAMS, $pid,$user)&&!$xacmlOnly) {
      drupal_set_message('You do not have permission to add datastreams to this object!');
      return false;
    }
    global $base_url;
    module_load_include('php', 'Fedora_Repository', 'mimetype');
    module_load_include('php', 'Fedora_Repository', 'ConnectionHelper');

    $dsLabel = $fileObject->filename;

    $pathToModule = drupal_get_path('module', 'Fedora_Repository');

    $file = $fileObject->filepath;
    //$streamUrl = 'http://' . $_SERVER['HTTP_HOST'] .$base_url. $file;

    $streamUrl = str_replace("https", "http", $base_url).'/'.drupal_urlencode( $file );

    $mimetype = $fileObject->filemime;

    $controlGroup = "M";
    if ($mimetype == 'text/xml') {
      $controlGroup = 'X';
    }
    $params = array (
        'pid' => $pid,
        'dsID' => $dsid,
        'altIDs' => "",
        'dsLabel' => $dsLabel,
        'versionable' => "true",
        'MIMEType' => $mimetype,
        'formatURI' => "URL",
        'dsLocation' => $streamUrl,
        'controlGroup' => "$controlGroup",
        'dsState' => "A",
        'checksumType' => "DISABLED",
        'checksum' => "none",
        'logMessage' => "datastream added"
    );
    try {
      $soapHelper = new ConnectionHelper();
      $client = $soapHelper->getSoapClient(variable_get('fedora_soap_manage_url', 'http://localhost:8080/fedora/services/management?wsdl'));

      if ($client == null) {
        drupal_set_message(t('Error Getting Soap Client.'), 'error');
        return false;
      }
      $object = $client->__soapCall('addDatastream', array (
          'parameters' => $params
      ));
    } catch (exception $e) {
      drupal_set_message(t($e->getMessage()), 'error');
      return false;
    }
    unlink($fileObject->filepath);
    return true;




  }




  /*
   * determines whether we can see the object or not
   */
  function fedora_repository_access($op, $pid) {

    global $user;
    $returnValue = false;
    if ($pid == NULL) {
      $pid = variable_get('fedora_repository_pid', 'islandora:top');
    }
    $nameSpaceAllowed = explode(" ", variable_get('fedora_pids_allowed', 'demo: changeme: islandora:'));
    $pos = NULL;
    foreach ($nameSpaceAllowed as $nameSpace) {

      $pos = stripos($pid, $nameSpace);

      if ($pos === 0) {
        $returnValue = true;
      }
    }
    if ($returnValue) {
      $user_access = user_access($op);

      if ($user_access == NULL) {
        return false;
      }
      return $user_access;
    } else {
      return false;
    }

  }


  /**
   * internal function
   * uses an xsl to parse the sparql xml returned from the ITQL query
   *
   *
   * @param $content String
   */
  function parseContent($content, $pid, $dsId, $collection, $pageNumber = null) {

    $path = drupal_get_path('module', 'Fedora_Repository');
    global $base_url;
    $collection_pid = $pid; //we will be changing the pid later maybe
    //module_load_include('php', ''Fedora_Repository'', 'ObjectHelper');
    $objectHelper = $this;
    $parsedContent = NULL;
    $contentModels = $objectHelper->get_content_models_list($pid);
    $isCollection = false;
    //if this is a collection object store the $pid in the session as it will come in handy
    //after a purge or ingest to return to the correct collection.

    if (!empty ( $contentModels ) ) {
      foreach($contentModels as $contentModel) {
        if ($contentModel == 'epistemetec:albumCModel' || $contentModel == 'epistemetec:compilationCModel'
        || $contentModel == 'epistemetec:videotecaCModel') {
          $_SESSION['fedora_collection'] = $pid;
          $isCollection=true;
        }
      }
    }

    //get a list of datastream for this object

    $datastreams = $this->get_formatted_datastream_list($pid, $contentModels);

    //$label=$content;
    $collectionName = $collection;
    if (!$pageNumber) {
      $pageNumber = 1;
    }

    if (!isset ($collectionName)) {
      $collectionName = variable_get('fedora_repository_name', 'Collection');
    }
    $xslContent = $this->getXslContent($pid, $path);

    //get collection list and display using xslt-------------------------------------------

    if (isset ($content) && $content != false) {

        $input = new DomDocument();
        $input->loadXML(trim($content));
        $results = $input->getElementsByTagName('result');
        if($results->length > 0){
         try {
          $proc = new XsltProcessor();

          $proc->setParameter('', 'collectionPid', $collection_pid);
          $proc->setParameter('', 'collectionTitle', $collectionName);
          $proc->setParameter('', 'baseUrl', $base_url);
          $proc->setParameter('', 'path', $base_url . '/' . $path);
          $proc->setParameter('', 'hitPage', $pageNumber);
          $proc->registerPHPFunctions();
          $xsl = new DomDocument();
          $xsl->loadXML($xslContent);

          //php xsl does not seem to work with namespaces so removing it below
          //I may have just been being stupid here
          //         $content = str_ireplace('xmlns="http://www.w3.org/2001/sw/DataAccess/rf1/result"', '', $content);
        
          $xsl = $proc->importStylesheet($xsl);
          $newdom = $proc->transformToDoc($input);
          $objectList = $newdom->saveXML(); //is the xml transformed to html as defined in the xslt associated with the collection object

          if (!$objectList) {
            throw new Exception("Invalid XML.");
         }
        } catch (Exception $e) {
         drupal_set_message(t($e->getMessage()), 'error');
          return '';
        }
      }
    } else {
      drupal_set_message(t("No Objects in this Collection or bad query."));
    }

    //--------------------------------------------------------------------------------
    //show the collections datastreams
    if ($results->length >0 || $isCollection==true) {
    //	if(strlen($objectList)>22||$contentModel=='Collection'||$contentModel=='Community'){//length of empty dom still equals 22 because of <table/> etc
      $collectionPolicyExists = $objectHelper->getMimeType($pid,'COLLECTION_POLICY');
     //drupal_set_message($collectionPolicyExists, 'error');
      if (user_access(ObjectHelper :: $INGEST_FEDORA_OBJECTS)&& $collectionPolicyExists) {
        if (!empty($collectionPolicyExists) &&  strcasecmp($collectionPolicyExists,'application/zip')) 
      
          $ingestObject = '<a title="' . t('Ingest a New object into ') . $collectionName . ' ' . $collection_pid . '" href="' . base_path() . 'fedora/ingestObject/' . $collection_pid . '/' . $collectionName . '"><img hspace = "8" src="' . $base_url . '/' . $path . '/images/ingest.png" alt="' . t('Ingest Object') . '"></a>';

       }else {
        $ingestObject = '&nbsp;';
      }

      $datastreams .= $ingestObject;
      $collection_fieldset = array (
        '#title' => t('Collection Description'),
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
        '#value' => $datastreams
      );
      $collectionListOut = theme('fieldset', $collection_fieldset);

      $object_list_fieldset = array (
        '#title' => t('Items in this Collection'),
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,
        '#value' => (isset($objectList) ? $objectList : ''), //collection list
      );

      $objectListOut = theme('fieldset', $object_list_fieldset);

    } else {

    //$collectionName='';
      $collection_fieldset = array (
        '#title' => "",
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,
        '#value' => $datastreams,
      );
      $collectionListOut = theme('fieldset', $collection_fieldset);
      $objectListOut = ''; //no collection objects to show so don't show field set
    }

    return "<STRONG>" . $collectionName . "</STRONG>" . ' <br />' . $collectionListOut . '<br />' . $objectListOut;

  }

  /**
   * Gets the parent objects that this object is related to
   *
   * @param unknown_type $pid
   * @return unknown
   */
  function get_parent_objects($pid) {
    $query_string = 'select $object $title from <#ri>
                            where ($object <dc:title> $title
                              and <info:fedora/'.$pid.'> <fedora-rels-ext:isMemberOfCollection> $object
                              and $object <fedora-model:state> <info:fedora/fedora-system:def/model#Active>)
                            order by $title';

    $objects = $this->getCollectionInfo($pid, $query_string);
    return $objects;
  }

  function get_parent_objects_asHTML( $pid ) {
    global $base_url;
    $parent_collections = $this->get_parent_objects( $pid );
    try {
      $parent_collections = new SimpleXMLElement( $parent_collections );
    }catch(exception $e ){
      drupal_set_message(t('error getting parent objects ').$e->getMessage());
      return;
    }

    $parent_collections_HTML = '';
    foreach ( $parent_collections->results->result as $result ) {

      $collection_label = $result->title;

      foreach($result->object->attributes() as $a => $b) {

        if ( $a == 'uri' ) {

          $uri = (string)$b;
          $uri = $base_url.'/fedora/repository'.substr( $uri, strpos($uri, '/') ).'/-/'.$collection_label;
        }
      }
      $parent_collections_HTML .= '<a href="'.$uri.'">'.$collection_label.'</a><br />';
    }
    if (!empty( $parent_collections_HTML ) ) {
      $parent_collections_HTML = '<tr><td><h3>' . t("Belongs to These Collections: ").'</h3></td><td colspan="4">'.$parent_collections_HTML.'</td></tr>';
    }

    return $parent_collections_HTML;
  }

  /**
   * gets a list of datastreams and related function that we should use to show datastreams in their own fieldsets
   * from the content model associated with the object
   */
  function createExtraFieldsets($pid, $contentModel) {
  //$models = $collectionHelper->getContentModels($collectionPid, false);
  // possible problem in below if the collection policy has multiple content models
  //with different pids but same dsid we could get wrong one,  low probability and functionality
  // will probably change for fedora version 3.
    if (empty($contentModel)) {
      return null;
    }
    try {
      $contentModelXMLStream = $this->getStream($contentModel, 'ISLANDORACM', false);
    }catch (Exception $e ){

    }
    if(empty($contentModelXMLStream)) {
      return null;
    }
    $count = 0;
    $output = '';
    try {
      $xml = new SimpleXMLElement($contentModelXMLStream);
    } catch (Exception $e) {
    //just show default dc or qdc as we could not find a content model
      module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
      $objectHelper = new ObjectHelper();
      $metaDataText = 'Description';
      $body = $objectHelper->getQDC($pid);
      $fieldset = array (
          '#title' => t($metaDataText
          ), '#collapsible' => TRUE, '#collapsed' => TRUE, '#value' => $body);

     $output .= theme('fieldset', $fieldset);

      return $output;

    }
    if ($xml->display_in_fieldset->datastream != null) {
      foreach ($xml->display_in_fieldset->datastream as $datastream) {
        $count++;
        $dsId = $datastream['id'];
        $expanded = $datastream['expanded'];
        $phpFile = '';
        if ( !empty( $datastream->method->file ) ) {
          $phpFile = strip_tags($datastream->method->file->asXML());
        }
        if ( !empty($phpFile) ) {
          $phpClass = strip_tags($datastream->method->class_name->asXML());
          $phpMethod = strip_tags($datastream->method->method_name->asXML());
          require_once (drupal_get_path('module', 'Fedora_Repository') . '/' . $phpFile);

          $thisClass = new $phpClass ($pid);
          $output .= $thisClass->$phpMethod ();
        }
      }
    }
    if ($count < 1) {
    //just show default dc or qdc as we could not find a content model

      $metaDataText = 'Description';
      $body = $this->getQDC($pid);
      $fieldset = array (
          '#title' => t($metaDataText
          ), '#collapsible' => TRUE, '#collapsed' => TRUE, '#value' => $body);

      $output .= theme('fieldset', $fieldset);
    }


    if ('ilives:pageCModel' == $contentModel || 'ilives:bookCModel' == $contentModel) {
      global $user;
      $qs = '';
      if ($user->uid != 0) {
        $qs = '?uid=' . base64_encode($user->name . ':' . $user->sid);
      }

      $viewer_url = variable_get('fedora_base_url', '') . '/get/' . $pid . '/ilives:viewerSdef/getViewer' . $qs;

      $html = '<iframe src="' . $viewer_url .'" frameborder="0" style="width: 100%; height: 400px;">Errors: unable to load viewer</iframe>';

      $fieldset = array (

        '#title' => t('Viewer'),

        '#collapsible' => TRUE,

        '#collapsed' => $collapsed,

        '#value' => $html);



      return theme('fieldset', $fieldset);

    }
    return $output;
  }

  function modifyDatastreamByValue($params) {
    module_load_include('php', 'Fedora_Repository', 'ConnectionHelper');
    $connection= new ConnectionHelper();
    $client = $connection->getSoapClient(variable_get('fedora_soap_manage_url', 'http://localhost:8080/fedora/services/management?wsdl'));
    try {
      $object = $client->__soapCall('ModifyDatastreamByValue', array (
          $params
      ));
    } catch (exception $e) {
      drupal_set_message(t("Error modifying datastream data ") . $e->getMessage(), 'error');
      return false;
    }
    return true;
  }

  /**
   * Get a list of datastreams as defined in an object's associated content model.
   *
   * @return Array of datastream IDs.
   */
  function get_datastreams_from_content_model( $pid, $content_model_dsid ) {
    $datastreamlist = array();

    //$xml = $this->get_content_model_xml( $pid, $contentModel );
    $xml = $this->getStream($pid, $content_model_dsid,false);
    try {
      $sxml = new SimpleXMLElement($xml);
    } catch (Exception $e) {
      watchdog(t("Fedora_Repository"), t("Error Getting Content Model xml stream!").$e->getMessage(),NULL, WATCHDOG_ERROR);
      return false;
    }
    if ($sxml->display_in_fieldset->datastream != null) {
      $count = 0;
      foreach ($sxml->display_in_fieldset->datastream as $datastream) {
        $count++;
        foreach($datastream->attributes() as $a => $b) {
          if ( $a == 'id' && (string)$b != 'QDC' ) {
            $newattr = array( (string)$b => (string)$b );
            $datastreamlist = array_merge($datastreamlist, $newattr);
          //
          }
        }

      }
    }

    return $datastreamlist;
  }
  /**
   * Look in the content model for rules to run on the specified datastream.
   *
   * @param string $pid
   * @param string $dsid
   * @return boolean
   */
  function get_and_do_datastream_rules( $pid, $dsid, $file = '' ) {
    if (!user_access('ingest new fedora objects')) {
      drupal_set_message(t('You do not have permission to add datastreams.'));
      return FALSE;
    }
    $collection_object = new CollectionClass();
    $content_model_list = $this->get_content_models_list( $pid );
    foreach ( $content_model_list as $content_model ) {
      $content_model_xml = $this->getStream( $content_model, variable_get('Islandora_Content_Model_DSID', 'ISLANDORACM'), 1);
      IF ( !empty( $content_model_xml->display_in_fieldset->datastream) ) {
        foreach ($content_model_xml->display_in_fieldset->datastream as $datastream) {
          if ( $datastream['id'] == $dsid ) {


            $collection_object->callMethods($file, $datastream->add_datastream_method);
            break;
          }
        }
      }

      if(!empty($_SESSION['fedora_ingest_files'])) {
        foreach($_SESSION['fedora_ingest_files'] as $dsid => $createdFile) {
          $file_obj = new stdClass();
          //  				$file->uid      = $user->uid;
          $file_obj->status   = 0;
          $file_obj->filename = substr($createdFile, strrpos($createdFile, '/') );
          $file_obj->filepath = $createdFile;
          $file_obj->filemime = file_get_mimetype( $createdFile);
          $this->addStream( $pid, $dsid, $file_obj);
          file_delete( $createdFile );
        }
        $_SESSION['fedora_ingest_files'] = '';
      }
    }
  }
  /**
   * Read the object's content model to find the function to call to build a form
   * to edit the datastream on the webpage.
   *this has been updated for Fedroa 3 but not tested yet.  Now loops through all the contentmodels to find one
   * that handles the dsid specified.  Could be problems if an object has two content models that both want to defnine a form to edit one datastream
   * @param string $pid
   * @param string $dsid
   * @return
   */
  function get_edit_datastream_function( $pid, $dsid ) {
    if($dsid==null || $pid==null) {
      return false;
    }
    $content_models = $this->get_content_models_list( $pid );
    $output=null;
    foreach($content_models as $content_model) {
      if($content_model!='fedora-system:FedoraObject-3.0') {
        $xml = $this->getStream($content_model, 'ISLANDORACM',false);
        $content_model_xml=null;
        try {
          $content_model_xml = new SimpleXMLElement($xml);
        } catch (Exception $e) {
          watchdog(t("Fedora_Repository"), t("Error Getting Content Model xml stream!").$e->getMessage(),NULL, WATCHDOG_ERROR);
          return false;
        }
        $build_form_method = $content_model_xml->edit_metadata->build_form_method ;
        //$content_model_xml = $this->get_content_model_xml( $pid, $content_model );
        if ( !empty( $content_model_xml->edit_metadata->build_form_method ) && $build_form_method->attributes()->dsid==$dsid ) {
          $phpFile = '';
          if ( !empty( $content_model_xml->edit_metadata->build_form_method->file ) ) {
            $phpFile = strip_tags( $content_model_xml->edit_metadata->build_form_method->file->asXML());
          }

          if ( !empty($phpFile) ) {
            $phpClass = strip_tags( $content_model_xml->edit_metadata->build_form_method->class_name->asXML() );
            $phpMethod = strip_tags( $content_model_xml->edit_metadata->build_form_method->method_name->asXML() );
            require_once (drupal_get_path('module', 'Fedora_Repository') . '/' . $phpFile);
            $thisClass = new $phpClass ($pid);
            $output = $thisClass->$phpMethod ();
            return $output;
          }
        } else {
          return FALSE;
        }
      }
    }

    return $output;
  }

  function edit_datastream_form_submit_function( $form_id, $form_values, $soap_client ) {
    $content_model_list = $this->get_content_models_list( $form_values['pid'] );
    if ( !empty ($content_model_list ) ) {
      foreach( $content_model_list as $content_model ) {

        $content_model_xml = $this->getStream( $content_model, 'ISLANDORACM' );
        if ( empty( $content_model_xml ) ) {
          continue;
        }
        $content_model_xml = new SimpleXMLElement( $content_model_xml );
        if ( !empty( $content_model_xml->edit_metadata->submit_form_method ) ) {
          $phpFile = '';
          if ( !empty( $content_model_xml->edit_metadata->submit_form_method->file ) ) {
            $phpFile = strip_tags( $content_model_xml->edit_metadata->submit_form_method->file->asXML());
          }
          if ( !empty($phpFile) ) {
            $phpClass = strip_tags( $content_model_xml->edit_metadata->submit_form_method->class_name->asXML() );
            $phpMethod = strip_tags( $content_model_xml->edit_metadata->submit_form_method->method_name->asXML() );
            require_once (drupal_get_path('module', 'Fedora_Repository') . '/' . $phpFile);

            $thisClass = new $phpClass ($pid);
            return $thisClass->$phpMethod( $form_id, $form_values, $soap_client );
          }
        }
      }
    }

    return FALSE;
  }
}
?>