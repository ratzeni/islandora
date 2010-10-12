<?php

/*
 * Created on Jan 22, 2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

class formClass {

  function formClass() {
    module_load_include('nc', 'formClass', '');
    drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

  }
  /*
   * create the paths for urls and map them to php functions
   */
  function createMenu(){
    $items = array ();

    $items['admin/settings/fedora_repository'] = array (
          'title' => t('Fedora Collection List'),
          'description' => t('Enter the Fedora Collection information here'),
          'page callback' => 'drupal_get_form',
          'page arguments' => array('fedora_repository_admin'),
    //'access' => user_access('access administration pages'),
      'access arguments' => array('administer site configuration'),
          'type' => MENU_NORMAL_ITEM,
          // 'type' => MENU_DEFAULT_LOCAL_TASK,
    );
    $items['admin/settings/fedora_repository/collection'] = array (
      'title' => t('Collection List'),
      'description' => t('Enter the fedora colleciton information here.'),
      'access arguments' => array('administer site configuration'),
      'type' => MENU_DEFAULT_LOCAL_TASK,
      'weight' => 0,
    );
    
    $items['admin/settings/fedora_repository/demoobjects'] = array (
      'title' => t('Install Demos'),
      'description' => t('Install example content models and collections to get started using Islandora and Fedora.'),
      'page callback' => 'fedora_repository_install_demos_page',
      'access arguments' => array('add fedora datastreams'),
      'type' => MENU_LOCAL_TASK,
    );
    
    $items['fedora'] = array (
          'title' => t('Digital Repository'),
          'page callback' => 'repository_page',
          'type' => MENU_CALLBACK,
    //  'page arguments'=>array(1),
          'access arguments' => array( 'view fedora collection' ),
    //		  'access' => TRUE
    );
    $items['fedora/repository'] = array (
          'title' => t('Digital Repository'),
          'page callback' => 'repository_page',
          'type' => MENU_NORMAL_ITEM,
    //  'page arguments'=>array(1),
          'access arguments' => array( 'view fedora collection' ),
    //		  'access' => TRUE
    );
    $items['fedora/repository/object_download'] = array (
      'title' => t('Download Object'),
          'page callback' => 'fedora_object_as_attachment',
          'type' => MENU_CALLBACK,
          'access arguments' => array('view fedora collection')
    );
    $items['fedora/repository/editmetadata'] = array (
            'title' => t('Edit MetaData'),
          'page callback' => 'fedora_repository_edit_qdc_page',
    // 'page arguments' => array(1),
   //'type' =>  MENU_LOCAL_TASK,
          'type'=> MENU_CALLBACK,
          'access arguments' => array('edit fedora meta data')
    );
    $items['fedora/repository/editmagmetadata'] = array (
            'title' => t('Edit MAG MetaData'),
          'page callback' => 'fedora_repository_edit_mag_page',
    // 'page arguments' => array(1),
   //'type' =>  MENU_LOCAL_TASK,
          'type'=> MENU_CALLBACK,
          'access arguments' => array('edit fedora meta data')
    );
    $items['fedora/repository/purgeStream'] = array (
            'title' => t('Purge DataStream'),
          'page callback' => 'fedora_repository_purge_stream',
          'type' =>  MENU_CALLBACK,
          'access arguments' => array('purge objects and datastreams')
    );
    
    $items['fedora/repository/purgeObject'] = array (
          'title' => t('Purge Object'),
          'page callback' => 'fedora_repository_purge_object',
    //'type' =>  MENU_LOCAL_TASK,
          'type'=> MENU_CALLBACK,
          'access arguments' => array('purge objects and datastreams')
    );
    $items['fedora/repository/addStream'] = array (

        'title' => t('Add Stream'),
        'page callback' => 'add_stream',
    //'type' => MENU_LOCAL_TASK,
        'type'=> MENU_CALLBACK,
        'access arguments' => array('add fedora datastreams')
    );

    $items['fedora/repository/collection'] = array (
          'title' => t('Collection View'),
            'page callback' => 'fedora_collection_view',
            'type'=> MENU_CALLBACK,
            'access argruments' => array('view fedora collection')
    );
    //new for mnpl******************************************
    $items['fedora/repository/mnpl_advanced_search'] = array (
          'title' => t('Repository Advanced Search'),
           'page callback' => 'fedora_repository_mnpl_advanced_search',
            'type'=> MENU_CALLBACK,
            'access arguments' => array('view fedora collection')
    );

	 $items['fedora/repository/epistemetec_search'] = array (
          'title' => t('Repository Advanced Search'),
           'page callback' => 'fedora_repository_mnpl_advanced_search',
            'type'=> MENU_CALLBACK,
            'access arguments' => array('view fedora collection')
    ); 
    $items['fedora/ingestObject'] = array (
        'title' => t('Ingest Object'),
      'page callback' => 'fedora_repository_ingest_object',
        'type' => MENU_CALLBACK,
      'access arguments' => array('add fedora datastreams')
    );

     $items['fedora/repository/list_terms'] = array (
      'title' => t('List Terms'),
      'page callback' => 'fedora_repository_list_terms',
      'type' => MENU_CALLBACK,
      'access arguments' => array('view fedora collection')
    );

    $items['fedora/tagging/add_tag/js'] = array (
      'page callback' => 'fedora_tagging_add_tag_js',
      'access arguments' => array('edit tags datastream'),
      'type' => MENU_CALLBACK,
    );


   /* $items['fedora/item'] = array (
      'title' => t('Repository Item'),
      'access arguments' => array('view fedora collection'),
      'page callback' => 'fedora_repository_item',
      'type' => MENU_NORMAL_ITEM,
    );

    $item['feodra/item/view'] = array (
      'title' => t('View Repository Item'),
      'access arguments' => array('view fedora collection'),
      'type' => MENU_DEFAULT_LOCAL_TASK,
      'weight' => 0,
    );*/

   // $items = array_merge($items,$irItems);
    return $items;
  }


  function createAdminForm() {
    if(!user_access('administer site configuration')){
      drupal_set_message(t('You must specify an Object pid and Dublin Core Datastream ID to edit MetaData'),'error');
      return;
    }
    module_load_include('inc', 'fedora_repository', 'api/fedora_utils');

    $form = array ();
    $form['fedora_repository_name'] = array (
        '#type' => 'textfield',
        '#title' => t('Default Collection Name ' ),
      '#default_value' => variable_get('fedora_repository_name', 'Islandora Demos Collection'),
      '#description' => t('The Name of the Collection to grab the list of items from'),
      '#required' => true,
      '#weight' => -2
    );
    $form['fedora_repository_pid'] = array (
        '#type' => 'textfield',
        '#title' => t('Default Collection PID'),
        '#default_value' => variable_get('fedora_repository_pid', 'islandora:top'),
        '#description' => t('The PID of the Collection Object to grab the list of items from'),
        '#required' => true,
        '#weight' => -2
    );
    $form['fedora_repository_url'] = array (
        '#type' => 'textfield',
        '#title' => t('Fedora RISearch URL'),
        '#default_value' => variable_get('fedora_repository_url',
        'http://localhost:8080/fedora/risearch'),
        '#description' => t('The url of the fedora server'), '#required' => true,
        '#weight' => 0
    );
    $form['fedora_fgsearch_url'] = array (
        '#type' => 'textfield',
        '#title' => t('Fedora Lucene Search URL'),
        '#default_value' => variable_get('fedora_fgsearch_url', 'http://localhost:8080/fedoragsearch/rest'),
        '#description' => t('The url of the Lucene fedora server'),
        '#required' => true,
        '#weight' => 0
    );
    $form['fedora_index_name'] = array (
        '#type' => 'textfield',
        '#title' => t('Fedora Lucene Index Name'),
        '#default_value' => variable_get('fedora_index_name', 'BasicIndex'),
        '#description' => t('The name of the lucene index to search'),
        '#required' => true,
        '#weight' => 0
    );
    $form['fedora_soap_url'] = array (
        '#type' => 'textfield',
        '#title' => t('Fedora Soap Url' ),
        '#default_value' => variable_get('fedora_soap_url', 'http://localhost:8080/fedora/services/access?wsdl'),
        '#description' => t('The Url to use for soap connections'),
        '#required' => true,
        '#weight' => 0,
        '#suffix' => '<p>'.(fedora_available() ? '<img src="'.url('misc/watchdog-ok.png') .'"/>'.t('Successfully connected to fedora server at ') : '<img src="'.url('misc/watchdog-error.png') .'"/> '.t('Unable to connect to fedora server at ')).variable_get('fedora_soap_url', '').'</p>',

    );
    $form['fedora_base_url'] = array (
        '#type' => 'textfield',
        '#title' => t('Fedora base url'),
        '#default_value' => variable_get('fedora_base_url', 'http://localhost:8080/fedora'),
        '#description' => t('The Url to use for Rest type connections'),
        '#required' => true,
        '#weight' => 0,
    );

    $form['fedora_soap_manage_url'] = array (
        '#type' => 'textfield',
        '#title' => t('Fedora Soap Management Url'),
        '#default_value' => variable_get('fedora_soap_manage_url', 'http://localhost:8080/fedora/services/management?wsdl'),
        '#description' => t('The Url to use for soap api-m connections'),
        '#required' => true,
        '#weight' => 0
    );

   /* $form['fedora_default_display_pid'] = array (
        '#type' => 'textfield',
        '#title' => t('Fedora Default Display Object Pid'	),
        '#default_value' => variable_get('fedora_default_display_pid', 'demo:10'),
        '#description' => t('Object Pid of an Image to show if the requested pid/datastream cannot be found'),
        '#required' => true,
        '#weight' => 0
    );
    $form['fedora_default_display_dsid'] = array (
        '#type' => 'textfield',
        '#title' => t('Fedora Default Display Datastream ID'	),
      '#default_value' => variable_get('fedora_default_display_dsid', 'TN'),
      '#description' => t('Object Datastream id of an Image to show if the requested pid/datastream cannot be found'),
      '#required' => true,
      '#weight' => 0
    );*/

    $form['fedora_pids_allowed'] = array (
      '#type' => 'textfield',
      '#title' => t('Pid namespaces allowed in this Drupal install'	),
      '#default_value' => variable_get('fedora_pids_allowed', 'default: demo: changeme: Islandora: ilives: '),
      '#description' => t('The pid namespaces that you are allowed to see from this drupal install.  In reality this can be more than a namespace as it could include demo:mydemos etc.  Can be a space seperated list to include more than one pid namespace.  '),
      '#required' => true,
      '#weight' => 0
    );
/*
    $form['fedora_admin_user'] = array (
      '#type' => 'textfield',
      '#title' => t('A user with the Drupal role administrator'),
      '#default_value' => variable_get('fedora_admin_user', 'admin'),
      '#description' => t('A user with the administrator role.  This is the user the Islandora module will use when admin access is needed for a task, such as creating a Collection object for a new user.'),
      '#required' => true, '#weight' => 0
    );

    $form['fedora_searchterms_location'] = array (
      '#type' => 'file',
      '#title' => 'SearchTerms.xml file location',
    );
 * 
 */

    $form['#attributes'] = array('enctype' => "multipart/form-data");

    // Additional form handling for file uploads.
    $form['#submit'][] = 'fedora_repository_admin_settings_submit';
    return system_settings_form($form);
  }


  function updateMetaData($form_id, $form_values, $client) {
    // ======================================
    // = begin creation of foxml dom object =
    // ======================================
    $dom = new DomDocument("1.0", "UTF-8");
    $dom->formatOutput = true;

    ///begin writing qdc

    $oai = $dom->createElement("oai_dc:dc");
    $oai->setAttribute('xmlns:oai_dc', "http://www.openarchives.org/OAI/2.0/oai_dc/");
    $oai->setAttribute('xmlns:dc', "http://purl.org/dc/elements/1.1/");
    $oai->setAttribute('xmlns:dcterms', "http://purl.org/dc/terms/");
    $oai->setAttribute('xmlns:xsi', "http://www.w3.org/2001/XMLSchema-instance");

    //dc elements
    $previousElement = null; //used in case we have to nest elements for qualified dublin core
    foreach ($form_values as $key => $value) {

      $index = strrpos($key, '-');
      $key = substr($key, 0, $index);
      $test = substr($key, 0, 2);
      if ($test == 'dc' || $test == 'ap') { //don't try to process other form values
        try {
          if (!strcmp(substr($key, 0, 4), 'app_')) {
            $key = substr($key, 4);
            $previousElement->appendChild($dom->createElement($key, $value));

          } else {
            $previousElement = $dom->createElement($key, $value);
            $oai->appendChild($previousElement);
          }

        } catch (exception $e) {
          drupal_set_message(t($e->getMessage()), 'error');
          continue;
        }
      }

    }

    $dom->appendChild($oai);

    if (!$client) {
      return;
    }

    $pid = $form_values['pid'];
    $dsId = $form_values['dsid'];
    $params = array (
            "pid" => $pid,
            "dsID" => $dsId,
            "altIDs" => "",
            "dsLabel" => "Qualified Dublin Core",
            "MIMEType" => "text/xml",
            "formatURI" => "URL",
            "dsContent" => $dom->saveXML(), "checksumType" => "DISABLED", "checksum" => "none", "logMessage" => "datastream_modified", "force" => "true");
    try {
      $object = $client->__soapCall('ModifyDatastreamByValue', array (
      $params
      ));
    } catch (exception $e) {
      drupal_set_message(t("Error updating metadata ") . $e->getMessage(), 'error');

    }

  }
  //queries the collection object for a childsecurity datastream and if found parses it
  //to determine if this user is allowed to ingest in this collection
  //we assume if they are able to modify objects in the collection they can ingest as well.
  function can_ingest_here($collection_pid) {
    module_load_include('php', 'Fedora_Repository', 'SecurityClass');
    $securityClass = new SecurityClass();
    return $securityClass->canIngestHere($collection_pid);

  }


  /**
   * Create a multi step form (wizard) for ingesting objects into Fedora
   */
  function createIngestForm( $collection_pid, $collection_label, &$form_state ) {
    global $user;
    module_load_include('php', 'Fedora_Repository', 'CollectionClass');
    //        drupal_add_js("function _imce_ingest_ImceFinish(path, w, h, s, imceWin) {imceWin.close(); document.getElementById('edit-ingest-file-location').value = path;}",'inline','header');

    if(!user_access('ingest new fedora objects')){
      drupal_set_message(t('You do not have permission to ingest..'),'error');
      return '';
    }
    if (empty($form_state['storage']['step'])) {
      // we are coming in without a step, so default to step 1
      $form_state['storage']['step'] = 1;
    }
    //this seems brittle should work for simple policies that deny and then overide with allow
    if(!$this->can_ingest_here($collection_pid)){
        drupal_set_message(t('You do not have premission to ingest here'));
        return '';
    }
    
    if($collection_pid==null){
      drupal_set_message(t('You must specify an Collection Object pid to ingest an object.'),'error');
      return FALSE;
    }
    $collectionHelper = new CollectionClass();
    //$collectionPolicyStream = $collectionHelper->getCollectionPolicyStream($collection_pid);
    $contentModels = $collectionHelper->getContentModels($collection_pid);
    if(!$contentModels){
      drupal_set_message(t('No Content Models associated with this collection. ').$collection_label.t('Please contact your administrator.'),'error');
      return FALSE;
    }
    $modelsForForm=array();
    foreach ($contentModels as $contentModel){
      $identifier = $contentModel->getIdentifier();
      $name = $contentModel->content_model_name;
      $modelsForForm["$identifier"] = "$name";
    }

    switch( $form_state['storage']['step'] ) {
      case 1:

        $form['indicator']=array(
            '#type' => 'fieldset',
            '#title' => t('Ingest Digital Object into '.$collection_pid.' '.$collection_label.' Step #1')
        );

        $form['indicator']['models']=array(//content models available
            '#type' => 'select',
            '#title' => t('Content Models Available'),
            '#options' => $modelsForForm,
            '#description' => t('Content Models available in this collection.  A Content Model defines what is allowed in a collection and what to do with a file when it is uploaded (An example may creating a thumbnail from an image.).')
        );
        

        break;

      case 2:
        module_load_include('php', 'Fedora_Repository', 'mimetype');
        $collectionHelper = new CollectionClass();
        $content_model_pid = ContentModel::getPidFromIdentifier($form_state['values']['models']);
        $content_model_dsid = ContentModel::getDSIDFromIdentifier($form_state['values']['models']);
    
        $form = $collectionHelper->build_ingest_form($form, $form_state, $content_model_pid,$content_model_dsid);
    
        break;
    }

    $form['collection_pid'] = array(
        '#type' => 'hidden',
        '#value' => $collection_pid
    );

    if ( $form_state['storage']['step'] < 2 ) {
      $button_name = t('Next');
    } else {
      $prefix =  'Please be patient.  Once you click next there may be a number of files created.  Depending on your Content Model this could take a few minutes to process.<br />';
      $button_name = t('Ingest');
    }

    $form['submit'] = array(
        '#type' => 'submit',
        '#value' => $button_name
    );

    return $form;
  }

  //this function may not be being used
  function createAddDataStreamForm($pid, &$form_state) {
    //dump_vars($form_state);
    // Populate the list of datastream IDs.

    module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
    $obj_helper = new ObjectHelper();
    $content_models = $obj_helper->get_content_models_list( $pid );
    $available_dsids=array();
    if ( !empty ($content_models) ) {
      foreach($content_models as $content_model){
        $newElements=$obj_helper->get_datastreams_from_content_model( $content_model, 'ISLANDORACM' ) ;

        if(!empty($newElements)){
          $available_dsids = array_merge($available_dsids,$newElements);
        }
      }
    }
     
    $used_datastreams = $obj_helper->get_datastreams_list_asSimpleXML( $pid );
    $used_datastream_ids = array();
    foreach ( $used_datastreams->datastreamDef as $used_datastream ) {
      array_push( $used_datastream_ids,  $used_datastream->ID );
    }
    $unused_dsids = array();

    if ($form_state['submitted'] && $form_state['clicked_button']['#value'] != 'OK' ) {
      $form['add_datastream_label'] = array(
            '#value' => t('<br /><h3>The datastream has been uploaded.</h3>'),
            '#weight' => -10,
      );
      $form['#redirect'] = "fedora/repository/$pid/";
      $form['submit']=array(
            '#type' => 'submit',
            '#value'=>t('OK')
      );
      return $form;
    }
    if ( !empty($available_dsids)) {
      $unused_dsids = array_diff($available_dsids, $used_datastream_ids );
      if ( empty( $unused_dsids) ) {
        return;
      }
    }

    $form['add_datastream_label'] = array(
          '#value' => t('<br /><h3>Add Datastream:</h3>'),
          '#weight' => -10,
    );

    $form['pid'] = array (
            '#type' => 'hidden',
            '#value' => "$pid"
    );
    /*
     $form['stream_location'] = array (
     '#title' => 'File Location',
     '#required' => 'true',
     '#description' => 'The path to the file for the new datastream',
     //'#prefix' => '<a onclick="window.open(\'?q=imce/browse\', \'_imce_fedora_\', \'width=640, height=600, resizable=1\')" href="#">Upload File</a>',
     '#type' => 'hidden'
     );*/


    $form['stream_label'] = array (
            '#title' => 'Datastream Label',
            '#required' => 'true',
            '#description' => t('A Human readable label'),
            '#type' => 'textfield'
            );
            //		$form['delete_file'] = array (
            //			'#title' => 'Remove File After Ingest',
            //			'#description' => 'Remove the file from the drupal file system after ingest into the Digital Repository.',
            //			'#type' => 'hidden',
            //			'#default_value' => 'remove_file',
            //				//'#options' => $options
            //
            //		);
            $form['#attributes']['enctype'] = 'multipart/form-data';
            $form['add-stream-file-location'] = array (
            '#type' => 'file',
            '#title' => t('Upload Document'),
            '#size' => 48,
            //			'#required'=>'true',
            '#description' => t('The file to upload.')
            );
            $form['#redirect'] = "fedora/repository/$pid/";
            $form['submit']=array(
        '#type' => 'submit',
        '#value'=>t('Add Datastream')
            );

            if ( !empty($unused_dsids) ) {
              $form['stream_id'] = array(
            '#type' => 'select',
            '#title' => t('Datastream ID'),
            '#default_value' => variable_get('feed_item_length','teaser'),
            '#weight' => '-1',
            '#description' => t('Datastream IDs defined by the content model.'),
              );
              $form['stream_id']['#options'] = $unused_dsids;

            } else {
              $form['stream_id'] = array (
                '#title' => 'Datastream ID',
                '#required' => 'true',
                '#description' => t('An ID for this stream that is unique to this Object. Must start with a letter and contain only alphanumeric characters and dashes and underscores.'),
                '#type' => 'textfield',
                '#weight' => -1,
              );
            }
            return $form;
  }

  /**
   * Creates a drupal form to edit either the QDC or DC datastream
   */
  function createMetaDataForm($pid, $dsId=null,$client){

    if(!isset($dsId)){
      $dsId='QDC';
    }

    //$client = getSoapClient(variable_get('fedora_soap_url', 'http://localhost:8080/fedora/services/access?wsdl'));
    $params = array('pid' => "$pid", 'dsID' => "$dsId", 'asOfDateTime' =>"");
    try{
      $object = $client->__soapCAll('getDatastreamDissemination', array ('parameters' => $params));
    }catch(Exception $e){
      return 'Error Getting DataStream '.$dsId;
    }

    $content=$object->dissemination->stream;
    $content=trim($content);
    $doc = new DOMDocument();
    if(!$doc->loadXML($content)){
      echo "error loading xml";
    }

    $oai_dc=$doc->getElementsByTagName('dc');

    $dcItems=$oai_dc->item(0)->getElementsByTagName('*');
    $form=array();
    for ($i = 0; $i < $dcItems->length; $i++) {
      $name=$dcItems->item($i)->nodeName;
      if($name=='dc:description'){
        $form["$name".'-'."$i"]=array(
            '#title'=>$name,
            '#type' => 'textarea',
            '#default_value' => $dcItems->item($i)->nodeValue,
            '#description' => 'Dublin Core '.substr($dcItems->item($i)->nodeName,3)
        );
      }else if($name=='dc:title'){
        $form["$name".'-'."$i"]=array(
            '#title'=>$name,
            '#type' => 'textfield',
            '#required' => 'true',
            '#default_value' => $dcItems->item($i)->nodeValue,
            '#description' => 'Dublin Core '.substr($dcItems->item($i)->nodeName,3)
        );
      }else{

        if($oai_dc->item(0)->nodeName!=$dcItems->item($i)->parentNode->nodeName){
          $description=strstr  ( $name  , ':'  );
          $form['app_'."$name".'-'."$i"]=array(
                    '#title'=>$name,
                    '#type' => 'textfield',
                    '#default_value' => $dcItems->item($i)->nodeValue,
                    '#description' => 'Dublin Core '.substr($description,1)
          );
        }else{
          $value=$dcItems->item($i)->nodeValue;
          if($name=='dc:coverage'){
            $value='';
          }
          $description=strstr  ( $name  , ':'  );
          $form["$name".'-'."$i"]=array(
                    '#title'=>$name,
                    '#type' => 'textfield',
                    '#default_value' =>$value,
                    '#description' => 'Dublin Core '.substr($description,1)

          );

        }
      }
    }
    //	$form['#redirect']=  "fedora/repository/$pid/";
    $form['pid']=array(
        '#type'=>'hidden',
        '#value'=>"$pid"
    );
    $form['dsid']=array(
    '#type'=>'hidden',
    '#value'=>"$dsId"
    );
    $form['submit']=array(
    '#type' => 'submit',
    '#value'=>t('Update Metadata'),
    );

    return $form;


  }
  

}
?>