<?php
class EditStreams {
  function EditStreams($pid) {
    include_once 'includes/bootstrap.inc';
    drupal_bootstrap(DRUPAL_BOOTSRTAP_FULL);
    $this->pid = $pid;
  }
  function buildSpecimenEditForm() {
    module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
    $object = new ObjectHelper();
    $spec = $object->getStream($this->pid, 'CRITTER', 0);
    $doc = new DomDocument();
    if (!isset ($spec)) {
      drupal_set_message(t('Error getting Specimen metadata stream'), 'error');
      return null;
    }
    $xml = new SimpleXMLElement($spec);

    $form = array ();
    $lab_id = $xml->xpath('//critters:lab_id');
    $form['display'] = array (
        '#type' => 'item',
        '#title' => t('<strong>Lab Id</strong>'),
        '#value' => implode($lab_id)
    );
    $form['lab_id'] = array (
        '#type' => 'hidden',
        // '#title' => t('<strong>Lab Id</strong>'),
        '#value' => implode($lab_id)
    );



    $type = $xml->xpath('//critters:type');
    $form['type'] = array (
        '#type' => 'select',
        '#options'=>array("Algae"=>"Algae","Coral"=>"Coral","Cyanobacteria"=>"Cyanobacteria","Invertebrate"=>"Invertebrate","Sponge"=>"Sponge","Tunicate"=>"Tunicate","Unclassified"=>"Unclassified"),
        '#title' => t('<strong>Type</strong>'),
        '#default_value' => implode($type)
    );
    $phylum = $xml->xpath('//critters:phylum');
    $form['phylum'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($phylum),
        '#title' => t('<strong>Phylum</strong>'
    ));

    $subphylum = $xml->xpath('//critters:subPhylum');
    $form['subphylum'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($subphylum),
        '#title' => t('<strong>SubPhylum</strong>'
    ));

    $class = $xml->xpath('//critters:class');
    $form['class'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($class),
        '#title' => t('<strong>Class</strong>'
    ));

    $order = $xml->xpath('//critters:order');
    $form['order'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($order),
        '#title' => t('<strong>Order</strong>'
    ));

    $family = $xml->xpath('//critters:family');
    $form['family'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($family),
        '#title' => t('<strong>Family</strong>'
    ));

    $genus = $xml->xpath('//critters:genus');
    $form['genus'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($genus),
        '#title' => t('<strong>Genus</strong>'
    ));
    $species = $xml->xpath('//critters:species');
    $form['species'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($species),
        '#title' => t('<strong>Species</strong>'
    ));
    $date = $xml->xpath('//critters:date_collected');
    $form['date'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($date),
        '#title' => t('<strong>Date Collected</strong>'
    ));
    $sitename = $xml->xpath('//critters:sitename');
    $form['sitename'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($sitename),
        '#title' => t('<strong>Site Name</strong>'
    ));
    $country = $xml->xpath('//critters:country');
    $form['country'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($country),
        '#title' => t('<strong>Country</strong>'
    ));
    $region = $xml->xpath('//critters:region');
    $form['region'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($region),
        '#title' => t('<strong>Region</strong>'
    ));
    $latitude = $xml->xpath('//critters:latitude');
    $form['latitude'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($latitude),
        '#title' => t('<strong>Latitude</strong>'
    ));
    $longitude = $xml->xpath('//critters:longitude');
    $form['longitude'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($longitude),
        '#title' => t('<strong>Longitude</strong>'
    ));
    $depth = $xml->xpath('//critters:depth');
    $form['depth'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($depth),
        '#title' => t('<strong>Depth</strong>'
    ));

    $form['#redirect'] = 'fedora/repository/'.$this->pid;
    $form['pid'] = array (
        '#type' => 'hidden',
        '#value' => $this->pid
    );
    $form['submit'] = array (
        '#type' => 'submit',
        '#value' => 'Update'
    );
    return $form;
  }
  function handleSpecimenEditForm($form_id,$form_values,$soap_client) {

  // ======================================
  // = begin creation of foxml dom object for critter/specimen stream=
  // ======================================
    module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
    $objectHelper = new ObjectHelper();
    $critterStream = $objectHelper->getStream($form_values['pid'],'CRITTER',true);
    $sxec = new SimpleXMLElement($critterStream);
    $dom = new DomDocument("1.0", "UTF-8");
    $dom->formatOutput = true;
    ///begin writing critter datastream
    $spec = $dom->createElement("critters:sample");
    $spec->setAttribute('xmlns:critters', "http://vre.upei.ca/critters/");
    $spec->setAttribute('name', implode($sxec->xpath('//@name')));
    // critter sample (root) elements
    $date = $dom->createElement("critters:date_collected",$form_values['date']);
    $size = $dom->createElement("critters:samplesize", implode($sxec->xpath('//critters:samplesize')));
    $lab_id = $dom->createElement("critters:lab_id",implode($sxec->xpath('//critters:lab_id')));
    $type = $dom->createElement("critters:type",$form_values['type']);
    $desc = $dom->createElement("critters:description", implode($sxec->xpath('//critters:description')));
    $spec->appendChild($lab_id);
    $spec->appendChild($type);
    $spec->appendChild($date);
    $spec->appendChild($size);
    $spec->appendChild($desc);
    //critter taxonomy elements
    $taxon = $dom->createElement("critters:taxonomy");
    $phylum = $dom->createElement("critters:phylum",$form_values['phylum']);
    $subphylum = $dom->createElement("critters:subPhylum",$form_values['subphylum']);
    $class = $dom->createElement("critters:class",$form_values['class']);
    $order = $dom->createElement("critters:order",$form_values['order']);
    $family = $dom->createElement("critters:family",$form_values['family']);
    $genus = $dom->createElement("critters:genus",$form_values['genus']);
    $species = $dom->createElement("critters:species",$form_values['species']);
    $taxon->appendChild($phylum);
    $taxon->appendChild($subphylum);
    $taxon->appendChild($class);
    $taxon->appendChild($order);
    $taxon->appendChild($family);
    $taxon->appendChild($genus);
    $taxon->appendChild($species);
    // critter photos
    $photos = $dom->createElement("critters:photos");
    $photos->setAttribute('quantity',implode($sxec->xpath('//@quantity')));
    foreach($sxec->xpath('//@id') as $idkey=>$photo) {
      $image = $dom->createElement("critters:photo");
      $image->setAttribute('id',$photo);
      foreach($sxec->xpath('//critters:location') as $key=>$value) {
        if ($key == $idkey) {
          $location= $dom->createElement("critters:location",$value);
          $image->appendChild($location);
        }
      }
      $photos->appendChild($image);
    }
    // critter site elements
    $site = $dom->createElement("critters:site");
    $sitename = $dom->createElement("critters:sitename",$form_values['sitename']);
    $country = $dom->createElement("critters:country",$form_values['country']);
    $region = $dom->createElement("critters:region",$form_values['region']);
    $latitude = $dom->createElement("critters:latitude",$form_values['latitude']);
    $longitude = $dom->createElement("critters:longitude",$form_values['longitude']);
    $depth = $dom->createElement("critters:depth",$form_values['depth']);
    $site->appendChild($sitename);
    $site->appendChild($country);
    $site->appendChild($region);
    $site->appendChild($latitude);
    $site->appendChild($longitude);
    $site->appendChild($depth);
    // critter collectors
    $collectors = $dom->createElement("critters:collectors");
    foreach($sxec->xpath('//critters:collector') as $individual) {
      $collector = $dom->createElement("critters:collector",$individual);
      $collectors->appendChild($collector);
    }
    // append nested elements
    $spec->appendChild($taxon);
    $spec->appendChild($photos);
    $spec->appendChild($site);
    $spec->appendChild($collectors);
    $dom->appendChild($spec);
    $newxml = $dom->saveXML();

    // map critter form elements to DC stream
    // loop through taxonomy to create label pulled from dctitle
    if(($form_values['species'] != '')and($form_values['genus'] != '')) {$label = " <i>".$form_values['genus']." ".$form_values['species']."</i>";}
    elseif(($form_values['species'] != '')and($form_values['genus'] == '')) {$label = " <i>".$form_values['species']."</i>";}
    elseif(($form_values['species'] == '')and($form_values['genus'] != '')) {$label = " <i>".$form_values['genus']."</i>";}
    elseif(($form_values['species'] == '')and($form_values['genus'] == '')and($form_values['family'] != '')) {$label = " ".$form_values['family'];}
    elseif(($form_values['species'] == '')and($form_values['genus'] == '')and($form_values['family'] == '')and($form_values['order'] != '')) {$label = " ".$form_values['order'];}
    elseif(($form_values['species'] == '')and($form_values['genus'] == '')and($form_values['family'] == '')and($form_values['order'] == '')and($form_values['class'] != '')) {$label = " ".$form_values['class'];}
    elseif(($form_values['species'] == '')and($form_values['genus'] == '')and($form_values['family'] == '')and($form_values['order'] == '')and($form_values['class'] == '')and($form_values['subphylum'] != '')) {$label = " ".$form_values['subphylum'];}
    elseif(($form_values['species'] == '')and($form_values['genus'] == '')and($form_values['family'] == '')and($form_values['order'] == '')and($form_values['class'] == '')and($form_values['subphylum'] == '')and($form_values['phylum'] != '')) {$label = " ".$form_values['phylum'];}
    $dom2 = new DomDocument("1.0", "UTF-8");
    $dom2->formatOutput = true;
    ///begin writing dc datastream
    $oai = $dom2->createElement("oai_dc:dc");
    $oai->setAttribute('xmlns:oai_dc',"http://www.openarchives.org/OAI/2.0/oai_dc/");
    $oai->setAttribute('xmlns:dc',"http://purl.org/dc/elements/1.1/");
    $oai->setAttribute('xmlns:dcterms',"http://purl.org/dc/terms/");
    $oai->setAttribute('xmlns:xsi',"http://www.w3.org/2001/XMLSchema-instance");
    $dctitle = $dom2->createElement("dc:title",implode($sxec->xpath('//critters:lab_id')).$label);
    $dccreator = $dom2->createElement("dc:creator",'Marine Natural Products Lab, University of Prince Edward Island');
    // list specimen taxonomy as dc:subject
    $dcphylum = $dom2->createElement("dc:subject",$form_values['phylum']);
    $dcsubphylum = $dom2->createElement("dc:subject",$form_values['subphylum']);
    $dcclass = $dom2->createElement("dc:subject",$form_values['class']);
    $dcorder = $dom2->createElement("dc:subject",$form_values['order']);
    $dcfamily = $dom2->createElement("dc:subject",$form_values['family']);
    $dcgenus = $dom2->createElement("dc:subject",$form_values['genus']);
    $dcspecies = $dom2->createElement("dc:subject",$form_values['species']);
    // end taxonomy
    $dcdesc = $dom2->createElement("dc:description",implode($sxec->xpath('//critters:description')));
    $dcpublisher = $dom2->createElement("dc:publisher",'Marine Natural Products Lab, University of Prince Edward Island');
    $dcpid = $dom2->createElement("dc:identifier",$form_values['pid']);
    $dcident = $dom2->createElement("dc:identifier",implode($sxec->xpath('//critters:lab_id')));
    $dcid = $dom2->createElement("dc:identifier",implode($sxec->xpath('//@name')));

    $dcdate = $dom2->createElement("dc:date",implode($sxec->xpath('//critters:date_collected')));
    $dctype = $dom2->createElement("dc:type",implode($sxec->xpath('//critters:type')));
    $dcformat = $dom2->createElement("dc:format",'text/html');
    $dclang = $dom2->createElement("dc:language",'eng');
    $dcsource = $dom2->createElement("dc:source",'');
    $dcrelation = $dom2->createElement("dc:relation",'vre:mnpl-specimens');
    $dcrights = $dom2->createElement("dc:rights",'All Rights Reserved - Marine Natural Products Lab. For permission to use this material please contact MNPL at rkerr@upei.ca.');

    // append elements
    $oai->appendChild($dctitle);
    $oai->appendChild($dccreator);
    $oai->appendChild($dcphylum);
    $oai->appendChild($dcsubphylum);
    $oai->appendChild($dcclass);
    $oai->appendChild($dcorder);
    $oai->appendChild($dcfamily);
    $oai->appendChild($dcgenus);
    $oai->appendChild($dcspecies);
    $oai->appendChild($dcdesc);
    $oai->appendChild($dcpublisher);
    //loop for contributors
    foreach($sxec->xpath('//critters:collector') as $individual) {
      $dccontrib = $dom2->createElement("dc:contributor",$individual);
      $oai->appendChild($dccontrib);
    }
    $oai->appendChild($dcdate);
    $oai->appendChild($dctype);
    $oai->appendChild($dcformat);
    $oai->appendChild($dcpid);
    $oai->appendChild($dcident);
    $oai->appendChild($dcid);
    $oai->appendChild($dcsource);
    $oai->appendChild($dcrelation);
    $oai->appendChild($dcrights);
    $dom2->appendChild($oai);
    $dcxml = $dom2->saveXML();
    $params = array("pid"=>$form_values['pid'],"dsID"=>'DC',"altIDs"=>"","dsLabel"=> "Dublin Core Record","MIMEType"=> "text/xml","formatURI"=>"URL","dsContent" => $dcxml, "checksumType" => "DISABLED", "checksum" => "none", "logMessage" => "datastream_modified", "force" => "true");
    $soapHelper = new ConnectionHelper();
    $client=$soapHelper->getSoapClient(variable_get('fedora_soap_manage_url', 'http://localhost:8080/fedora/services/management?wsdl'));
    $object = $client->__soapCall('ModifyDatastreamByValue', array ($params));
    //  check collections against type - modify relsext to match critter type
    if($form_values['type']=='Algae') {$collection_id='vre:mnpl-6';}
    elseif($form_values['type']=='Coral') {$collection_id='vre:mnpl-2';}
    elseif($form_values['type']=='Cyanobacteria') {$collection_id='vre:mnpl-7';}
    elseif($form_values['type']=='Invertebrate') {$collection_id='vre:mnpl-4';}
    elseif($form_values['type']=='Sponge') {$collection_id='vre:mnpl-3';}
    elseif($form_values['type']=='Tunicate') {$collection_id='vre:mnpl-5';}
    elseif($form_values['type']=='Unclassified') {$collection_id='vre:mnpl-8';}

    $relsext = $objectHelper->getStream($form_values['pid'],'RELS-EXT',true);
    $dom3 = new DomDocument("1.0", "UTF-8");
    $dom3->formatOutput = true;
    $rdf = $dom3->createElement("rdf:RDF");
    $rdf->setAttribute("xmlns:rdf","http://www.w3.org/1999/02/22-rdf-syntax-ns#");
    $rdf->setAttribute("xmlns:rdfs","http://www.w3.org/2000/01/rdf-schema#");
    $rdf->setAttribute("xmlns:fedora","info:fedora/fedora-system:def/relations-external#");
    $rdf->setAttribute("xmlns:dc","http://purl.org/dc/elements/1.1/");
    $rdf->setAttribute("xmlns:oai_dc","http://www.openarchives.org/OAI/2.0/oai_dc/");
    $rdf->setAttribute("xmlns:fedora-model","info:fedora/fedora-system:def/model#");
    $rdfdesc = $dom3->createElement("rdf:description");
    $rdfdesc->setAttribute("rdf:about",'info:fedora/'.$form_values['pid']);
    $member = $dom3->createElement("fedora:isMemberOfCollection");
    $member->setAttribute("rdf:resource","info:fedora/$collection_id");
    $member2 = $dom3->createElement("fedora:isMemberOfCollection");
    $member2->setAttribute("rdf:resource","info:fedora/vre:mnpl-specimens");
    $member3 = $dom3->createElement("fedora:isMemberOfCollection");
    $diveid = implode($sxec->xpath('//critters:lab_id'));
    $diveid = strtolower(preg_replace('/[0-9]+/','',$diveid));
    $member3->setAttribute("rdf:resource","info:fedora/vre:mnpl-dive-".$diveid);
    $rdfHasModel = $dom3->createElement("fedora-model:hasModel");
    $contentModelPid=$form_values['content_model_pid'];
    $rdfHasModel->setAttribute("rdf:resource","info:fedora/islandora:mnpl-specimenCModel");
    $rdf->appendChild($rdfdesc);
    $rdfdesc->appendChild($member);
    $rdfdesc->appendChild($member2);
    $rdfdesc->appendChild($member3);
    $rdfdesc->appendChild($rdfHasModel);
    $dom3->appendChild($rdf);
    $relsxml = $dom3->saveXML();
    $params = array("pid"=>$form_values['pid'],"dsID"=>'RELS-EXT',"altIDs"=>"","dsLabel"=> "Fedora Object-to-Object Relationship Metadata","MIMEType"=> "text/xml","formatURI"=>"URL","dsContent" => $relsxml, "checksumType" => "DISABLED", "checksum" => "none", "logMessage" => "datastream_modified", "force" => "true");
    $soapHelper = new ConnectionHelper();
    $client=$soapHelper->getSoapClient(variable_get('fedora_soap_manage_url', 'http://localhost:8080/fedora/services/management?wsdl'));
    $object = $client->__soapCall('ModifyDatastreamByValue', array ($params));

    // form values
    $pid = $form_values['pid'];
    $dsId = 'CRITTER';
    // get fractions and compounds critter streams for modify
    $itqlQuery = 'select $object $title  from <#ri> where $object <fedora-model:label> $title  and $object <fedora-rels-ext:isPartOf> <info:fedora/'.$pid.'> and $object <fedora-model:state> <info:fedora/fedora-system:def/model#Active> order by $title';
    require_once (drupal_get_path('module', 'Fedora_Repository') . '/CollectionClass.php');
    $collectionObj = new CollectionClass();
    $relatedItems=$collectionObj->getRelatedItems($this->pid,$itqlQuery);
    $sxe=new SimpleXMLElement($relatedItems);
    $nmspace = $sxe->getNamespaces(true);
    $regspace = $sxe->registerXPathNamespace('ri',implode($nmspace));
    foreach ($sxe->xpath('//@uri') as $link) {
      $partpid = ltrim($link,'info:fedora/');
      $params = array (
          "pid" => $partpid,
          "dsID" => $dsId,
          "altIDs" => "",
          "dsLabel" => "Parent Critter Record",
          "MIMEType" => "text/xml",
          "formatURI" => "URL",
          "dsContent" => $newxml, "checksumType" => "DISABLED", "checksum" => "none", "logMessage" => "datastream_modified", "force" => "true");
      $soapHelper = new ConnectionHelper();
      $client=$soapHelper->getSoapClient(variable_get('fedora_soap_manage_url', 'http://localhost:8080/fedora/services/management?wsdl'));
      $object = $client->__soapCall('ModifyDatastreamByValue', array ($params));
    }
    // end of update other datastreams   - begin soap call for main critter record
    $params = array (
        "pid" => $pid,
        "dsID" => $dsId,
        "altIDs" => "",
        "dsLabel" => "CRITTER",
        "MIMEType" => "text/xml",
        "formatURI" => "URL",
        "dsContent" => $newxml, "checksumType" => "DISABLED", "checksum" => "none", "logMessage" => "datastream_modified", "force" => "true");
    try {
      $soapHelper = new ConnectionHelper();
      $client=$soapHelper->getSoapClient(variable_get('fedora_soap_manage_url', 'http://localhost:8080/fedora/services/management?wsdl'));

      if($client==null) {
        drupal_set_message(t('Error Getting Soap Client.'),'error');
        return;
      }
      $object = $client->__soapCall('ModifyDatastreamByValue', array ($params));
      return true;
    } 	catch(exception $e){
      drupal_set_message(t('Error Editing Specimen! ').$e->getMessage(),'error');
      watchdog(t("Fedora_Repository"), t("Error Editing Specimen!").$e->getMessage(), WATCHDOG_ERROR);

      return;
    }

  } //end handle spec
  function buildCompoundEditForm() {
    module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
    drupal_add_css( drupal_get_path( 'module', 'fedora_repository' ).'/mnpl/mnpl_forms.css', 'module' );

    $object = new ObjectHelper();
    $cmpd = $object->getStream($this->pid, 'COMPOUND', 0);
    $doc = new DomDocument();
    if (!isset ($cmpd)) {
      drupal_set_message(t('Error getting compound metadata stream'), 'error');
      return null;
    }
    $xml = new SimpleXMLElement($cmpd);
    $radioValues = array ();
    $radioValues['No_Assay'] = 'No_Assay';
    $radioValues['Hit'] = 'Hit';
	$radioValues['Strong'] = 'Strong';
    $radioValues['Medium'] = 'Medium';
    $radioValues['Low'] = 'Low';
    $radioValues['Inactive'] = 'Inactive';

    $form = array ();
    $identifier = $xml->xpath('//compounds:identifier');
    $form['identifier'] = array (
        '#type' => 'hidden',
        '#title' => t('<strong>Identifier</strong>'),
        '#value' => implode($identifier)
    );
    $form['ident'] = array (
        '#type' => 'item',
        '#title' => t('<strong>Identifier</strong>'),
        '#value' => implode($identifier)
    );
    $new = $xml->xpath('//compounds:new');
    $form['new'] = array (
        '#prefix' => '<div class="horiz_checkboxes">',
        '#suffix' => '</div><br /><br/>',
        '#type' => 'radios',
        '#default_value' => implode($new),
        '#options' => array('Yes'=>'Yes', 'No'=>'No'),
        '#title' => t('<strong>New</strong>'
    ));

    $ref = $xml->xpath('//compounds:references');
    $form['references'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($ref),
        '#title' => t('<strong>References</strong>'
    ));

    $weight = $xml->xpath('//compounds:weight');
    $form['weight'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($weight),
        '#title' => t('<strong>Weight</strong>'
    ));

    $pur = $xml->xpath('//compounds:purification_com');
    $form['pur'] = array (
        '#type' => 'textarea',
        '#default_value' => implode($pur),
        '#title' => t('<strong>Purification Comments</strong>'
    ));

    $inhib = $xml->xpath('//compounds:inhibitors');
    $form['inhib'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($inhib),
        '#title' => t('<strong>Inhibitors</strong>'
    ));

    $pt = $xml->xpath('//compounds:ptp1b');
    $form['pt'] = array (
        '#type' => 'radios',
        '#default_value' => implode($pt),
        '#options'=> $radioValues,
        '#title' => t('<strong>PTP1B</strong>'
    ));
    $pt_com = $xml->xpath('//compounds:ptp1b_com');
    $form['pt_com'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($pt_com),
        '#title' => t('<strong>PTP1B Comments</strong>'
    ));
    $hct = $xml->xpath('//compounds:hct116');
    $form['hct'] = array (
        '#type' => 'radios',
        '#default_value' => implode($hct),
        '#options'=> $radioValues,
        '#title' => t('<strong>HCT116</strong>'
    ));
    $hct_com = $xml->xpath('//compounds:hct116_com');
    $form['hct_com'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($hct_com),
        '#title' => t('<strong>HCT116 Comments</strong>'
    ));
    $hela = $xml->xpath('//compounds:hela');
    $form['hela'] = array (
        '#type' => 'radios',
        '#default_value' => implode($hela),
        '#options'=> $radioValues,
        '#title' => t('<strong>HELA</strong>'
    ));
    $hela_com = $xml->xpath('//compounds:hela_com');
    $form['hela_com'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($hela_com),
        '#title' => t('<strong>HELA Comments</strong>'
    ));
    $pc3 = $xml->xpath('//compounds:pc3');
    $form['pc3'] = array (
        '#type' => 'radios',
        '#default_value' => implode($pc3),
        '#options'=> $radioValues,
        '#title' => t('<strong>PC3</strong>'
    ));
    $pc3_com = $xml->xpath('//compounds:pc3_com');
    $form['pc3_com'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($pc3_com),
        '#title' => t('<strong>PC3 Comments</strong>'
    ));
    $are = $xml->xpath('//compounds:are');
    $form['are'] = array (
        '#type' => 'radios',
        '#default_value' => implode($are),
        '#options'=> $radioValues,
        '#title' => t('<strong>ARE</strong>'
    ));
    $are_com = $xml->xpath('//compounds:are_com');
    $form['are_com'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($are_com),
        '#title' => t('<strong>ARE Comments</strong>'
    ));
    $antiproliferative = $xml->xpath('//compounds:antiproliferative');
    $form['antiproliferative'] = array (
        '#type' => 'radios',
        '#default_value' => implode($antiproliferative),
        '#options'=> $radioValues,
        '#title' => t('<strong>Antiproliferative</strong>'
    ));
    $antiproliferative_com = $xml->xpath('//compounds:antiproliferative_com');
    $form['antiproliferative_com'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($antiproliferative_com),
        '#title' => t('<strong>Antiproliferative Comments</strong>'
    ));
    $location = $xml->xpath('//compounds:location');
    $form['location'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($location),
        '#title' => t('<strong>Location</strong>'
    ));
    $notes = $xml->xpath('//compounds:notes');
    $form['notes'] = array (
        '#type' => 'textarea',
        '#default_value' => implode($notes),
        '#title' => t('<strong>Notes</strong>'
    ));

    $form['#redirect'] = 'fedora/repository/'.$this->pid;
    $form['pid'] = array (
        '#type' => 'hidden',
        '#value' => $this->pid
    );
    $form['submit'] = array (
        '#type' => 'submit',
        '#value' => 'Update'
    );
    return $form;
  }
  function handleCompoundEditForm($form_id,$form_values,$soap_client) {
  // ======================================
  // = begin creation of foxml dom object =
  // ======================================
    $dom = new DomDocument("1.0", "UTF-8");
    $dom->formatOutput = true;
    ///begin writing fraction datastream
    $cmpd = $dom->createElement("compounds:sample");
    $cmpd->setAttribute('xmlns:compounds', "http://vre.upei.ca/compounds/");
    //fraction elements
    $ident = $dom->createElement("compounds:identifier",$form_values['identifier']);
    $new = $dom->createElement("compounds:new",$form_values['new']);
    $ref = $dom->createElement("compounds:references",$form_values['ref']);
    $weight = $dom->createElement("compounds:weight",$form_values['weight']);
    $pur = $dom->createElement("compounds:purification_com",$form_values['pur']);
    $inhib = $dom->createElement("compounds:inhibitors",$form_values['inhib']);
    $pt = $dom->createElement("compounds:ptp1b",$form_values['pt']);
    $pt_com = $dom->createElement("compounds:ptp1b_com",$form_values['pt_com']);
    $hct = $dom->createElement("compounds:hct116",$form_values['hct']);
    $hct_com = $dom->createElement("compounds:hct116_com",$form_values['hct_com']);
    $hela = $dom->createElement("compounds:hela",$form_values['hela']);
    $hela_com = $dom->createElement("compounds:hela_com",$form_values['hela_com']);
    $pc3 = $dom->createElement("compounds:pc3",$form_values['pc3']);
    $pc3_com = $dom->createElement("compounds:pc3_com",$form_values['pc3_com']);
    $are = $dom->createElement("compounds:are",$form_values['are']);
    $are_com = $dom->createElement("compounds:are_com",$form_values['are_com']);
    $antipro = $dom->createElement("compounds:antiproliferative",$form_values['antiproliferative']);
    $antipro_com = $dom->createElement("compounds:antiproliferative_com",$form_values['antiproliferative_com']);
    $location = $dom->createElement("compounds:location",$form_values['location']);
    $notes = $dom->createElement("compounds:notes",$form_values['notes']);
    // append elements
    $cmpd->appendChild($ident);
    $cmpd->appendChild($new);
    $cmpd->appendChild($ref);
    $cmpd->appendChild($weight);
    $cmpd->appendChild($pur);
    $cmpd->appendChild($inhib);
    $cmpd->appendChild($pt);
    $cmpd->appendChild($pt_com);
    $cmpd->appendChild($hct);
    $cmpd->appendChild($hct_com);
    $cmpd->appendChild($hela);
    $cmpd->appendChild($hela_com);
    $cmpd->appendChild($pc3);
    $cmpd->appendChild($pc3_com);
    $cmpd->appendChild($are);
    $cmpd->appendChild($are_com);
    $cmpd->appendChild($antipro);
    $cmpd->appendChild($antipro_com);
    $cmpd->appendChild($location);
    $cmpd->appendChild($notes);
    $dom->appendChild($cmpd);
    // form values
    $pid = $form_values['pid'];
    $dsId = 'COMPOUND';
    $params = array (
        "pid" => $pid,
        "dsID" => $dsId,
        "altIDs" => "",
        "dsLabel" => "COMPOUND",
        "MIMEType" => "text/xml",
        "formatURI" => "URL",
        "dsContent" => $dom->saveXML(), "checksumType" => "DISABLED", "checksum" => "none", "logMessage" => "datastream_modified", "force" => "true");

        try {
          $soapHelper = new ConnectionHelper();
          $client=$soapHelper->getSoapClient(variable_get('fedora_soap_manage_url', 'http://localhost:8080/fedora/services/management?wsdl'));

          if($client==null) {
            drupal_set_message(t('Error Getting Soap Client.'),'error');
            return;
          }
          $object = $client->__soapCall('ModifyDatastreamByValue', array ($params));
          return true;
        }
        catch(exception $e){
          drupal_set_message(t('Error Editing Compound! ').$e->getMessage(),'error');
          watchdog(t("Fedora_Repository"), t("Error Editing Compound!").$e->getMessage(), WATCHDOG_ERROR);

          return;
        }
  }
  function buildFractionEditForm() {
    module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
    $object = new ObjectHelper();
    $frac = $object->getStream($this->pid, 'FRACTION', 0);
    $doc = new DomDocument();
    if (!isset ($frac)) {
      drupal_set_message(t('Error getting fraction metadata stream'), 'error');
      return null;
    }
    $xml = new SimpleXMLElement($frac);
    $radioValues = array ();
    $radioValues['No_Assay'] = 'No_Assay';
	$radioValues['Hit'] = 'Hit';
    $radioValues['Strong'] = 'Strong';
    $radioValues['Medium'] = 'Medium';
    $radioValues['Low'] = 'Low';
    $radioValues['Inactive'] = 'Inactive';
    $form = array ();
    $identifier = $xml->xpath('//fractions:identifier');
    $form['identifier'] = array (
        '#type' => 'hidden',
        '#title' => t('<strong>Identifier</strong>'),
        '#value' => implode($identifier)
    );
    $form['ident'] = array (
        '#type' => 'item',
        '#title' => t('<strong>Identifier</strong>'),
        '#value' => implode($identifier)
    );

    $plate = $xml->xpath('//fractions:plate');
    $form['plate'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($plate),
        '#title' => t('<strong>Plate</strong>'
    ));
    $weight = $xml->xpath('//fractions:weight');
    $form['weight'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($weight),
        '#title' => t('<strong>Weight</strong>'
    ));
    $pt = $xml->xpath('//fractions:ptp1b');
    $form['pt'] = array (
        '#type' => 'radios',
        '#default_value' => implode($pt),
        '#options'=> $radioValues,
        '#title' => t('<strong>PTP1B</strong>'
    ));
    $pt_com = $xml->xpath('//fractions:ptp1b_com');
    $form['pt_com'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($pt_com),
        '#title' => t('<strong>PTP1B Comments</strong>'
    ));
    $hct = $xml->xpath('//fractions:hct116');
    $form['hct'] = array (
        '#type' => 'radios',
        '#default_value' => implode($hct),
        '#options'=> $radioValues,
        '#title' => t('<strong>HCT116</strong>'
    ));
    $hct_com = $xml->xpath('//fractions:hct116_com');
    $form['hct_com'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($hct_com),
        '#title' => t('<strong>HCT116 Comments</strong>'
    ));
    $hela = $xml->xpath('//fractions:hela');
    $form['hela'] = array (
        '#type' => 'radios',
        '#default_value' => implode($hela),
        '#options'=> $radioValues,
        '#title' => t('<strong>HELA</strong>'
    ));
    $hela_com = $xml->xpath('//fractions:hela_com');
    $form['hela_com'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($hela_com),
        '#title' => t('<strong>HELA Comments</strong>'
    ));
    $pc3 = $xml->xpath('//fractions:pc3');
    $form['pc3'] = array (
        '#type' => 'radios',
        '#default_value' => implode($pc3),
        '#options'=> $radioValues,
        '#title' => t('<strong>PC3</strong>'
    ));
    $pc3_com = $xml->xpath('//fractions:pc3_com');
    $form['pc3_com'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($pc3_com),
        '#title' => t('<strong>PC3 Comments</strong>'
    ));
    $are = $xml->xpath('//fractions:are');
    $form['are'] = array (
        '#type' => 'radios',
        '#default_value' => implode($are),
        '#options'=> $radioValues,
        '#title' => t('<strong>ARE</strong>'
    ));
    $are_com = $xml->xpath('//fractions:are_com');
    $form['are_com'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($are_com),
        '#title' => t('<strong>ARE Comments</strong>'
    ));
    $antiproliferative = $xml->xpath('//fractions:antiproliferative');
    $form['antiproliferative'] = array (
        '#type' => 'radios',
        '#default_value' => implode($antiproliferative),
        '#options'=> $radioValues,
        '#title' => t('<strong>Antiproliferative</strong>'
    ));
    $antiproliferative_com = $xml->xpath('//fractions:antiproliferative_com');
    $form['antiproliferative_com'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($antiproliferative_com),
        '#title' => t('<strong>Antiproliferative Comments</strong>'
    ));
    $location = $xml->xpath('//fractions:location');
    $form['location'] = array (
        '#type' => 'textfield',
        '#default_value' => implode($location),
        '#title' => t('<strong>Location</strong>'
    ));
    $notes = $xml->xpath('//fractions:notes');
    $form['notes'] = array (
        '#type' => 'textarea',
        '#default_value' => implode($notes),
        '#title' => t('<strong>Notes</strong>'
    ));

    $form['#redirect'] = 'fedora/repository/'.$this->pid;
    $form['pid'] = array (
        '#type' => 'hidden',
        '#value' => $this->pid
    );
    $form['dsid'] = array (
        '#type' => 'hidden',
        '#value' => 'FRACTION'
    );

    $form['submit'] = array (
        '#type' => 'submit',
        '#value' => 'Update'
    );

    return $form;
  }
  function handleFractionEditForm($form_id,$form_values,$soap_client) {
  // ======================================
  // = begin creation of foxml dom object =
  // ======================================
    $dom = new DomDocument("1.0", "UTF-8");
    $dom->formatOutput = true;
    ///begin writing fraction datastream
    $frac = $dom->createElement("fractions:sample");
    $frac->setAttribute('xmlns:fractions', "http://vre.upei.ca/fractions/");
    //fraction elements
    $ident = $dom->createElement("fractions:identifier",$form_values['identifier']);
    $plate = $dom->createElement("fractions:plate",$form_values['plate']);
    $weight = $dom->createElement("fractions:weight",$form_values['weight']);
    $pt = $dom->createElement("fractions:ptp1b",$form_values['pt']);
    $pt_com = $dom->createElement("fractions:ptp1b_com",$form_values['pt_com']);
    $hct = $dom->createElement("fractions:hct116",$form_values['hct']);
    $hct_com = $dom->createElement("fractions:hct116_com",$form_values['hct_com']);
    $hela = $dom->createElement("fractions:hela",$form_values['hela']);
    $hela_com = $dom->createElement("fractions:hela_com",$form_values['hela_com']);
    $pc3 = $dom->createElement("fractions:pc3",$form_values['pc3']);
    $pc3_com = $dom->createElement("fractions:pc3_com",$form_values['pc3_com']);
    $are = $dom->createElement("fractions:are",$form_values['are']);
    $are_com = $dom->createElement("fractions:are_com",$form_values['are_com']);
    $antipro = $dom->createElement("fractions:antiproliferative",$form_values['antiproliferative']);
    $antipro_com = $dom->createElement("fractions:antiproliferative_com",$form_values['antiproliferative_com']);
    $location = $dom->createElement("fractions:location",$form_values['location']);
    $notes = $dom->createElement("fractions:notes",$form_values['notes']);
    // append elements
    $frac->appendChild($ident);
    $frac->appendChild($plate);
    $frac->appendChild($weight);
    $frac->appendChild($pt);
    $frac->appendChild($pt_com);
    $frac->appendChild($hct);
    $frac->appendChild($hct_com);
    $frac->appendChild($hela);
    $frac->appendChild($hela_com);
    $frac->appendChild($pc3);
    $frac->appendChild($pc3_com);
    $frac->appendChild($are);
    $frac->appendChild($are_com);
    $frac->appendChild($antipro);
    $frac->appendChild($antipro_com);
    $frac->appendChild($location);
    $frac->appendChild($notes);
    $dom->appendChild($frac);
    // form values

    $pid = $form_values['pid'];
    $dsId = 'FRACTION';
    $params = array (
        "pid" => $pid,
        "dsID" => $dsId,
        "altIDs" => "",
        "dsLabel" => "FRACTION",
        "MIMEType" => "text/xml",
        "formatURI" => "URL",
        "dsContent" => $dom->saveXML(), "checksumType" => "DISABLED", "checksum" => "none", "logMessage" => "datastream_modified", "force" => "true");

    try {
      $soapHelper = new ConnectionHelper();
      $client=$soapHelper->getSoapClient(variable_get('fedora_soap_manage_url', 'http://localhost:8080/fedora/services/management?wsdl'));

      if($client==null) {
        drupal_set_message(t('Error Getting Soap Client.'),'error');
        return;
      }
      $object = $client->__soapCall('ModifyDatastreamByValue', array ($params));
      return true;
    } 	catch(exception $e){
      drupal_set_message(t('Error Editing Fraction Object! ').$e->getMessage(),'error');
      watchdog(t("Fedora_Repository"), t("Error Editing Fraction Object!").$e->getMessage(), WATCHDOG_ERROR);
      return;
    }
  }
}
?>