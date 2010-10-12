<?php
module_load_include('php', 'Fedora_Repository','plugins/FormBuilder');
session_start();
/*
 * implements methods from content model ingest form xml
 * builds a fraction metadata form for ingest and edit
 */
class FractionsFormBuilder extends FormBuilder{


    function FractionsFormBuilder(){
        include_once 'includes/bootstrap.inc';
        drupal_bootstrap(DRUPAL_BOOTSRTAP_FULL);

    }
    function setIdentifier($form_values){
        require_once (drupal_get_path('module', 'Fedora_Repository') . '/ObjectHelper.php');
        require_once (drupal_get_path('module', 'Fedora_Repository') . '/CollectionClass.php');
        $collectionHelper= new CollectionClass();
        $ppid = $_SESSION['pid'];
        $itqlquery = 'select $object from <#ri> where $object <fedora-rels-ext:isPartOf><info:fedora/'.$ppid.'> and $object <fedora-rels-ext:isMemberOfCollection><info:fedora/vre:mnpl-fractions>and $object <fedora-model:state> <info:fedora/fedora-system:def/model#Active>';
        $relatedItems=$collectionHelper->getRelatedItems($this->pid,$itqlquery);
        $sxe=new SimpleXMLElement($relatedItems);
        $nmspace = $sxe->getNamespaces(true);
        $regspace = $sxe->registerXPathNamespace('ri',implode($nmspace));
        $link = $sxe->xpath('//@uri');
        $labid = $_SESSION['labid'];
        if(empty($link)){
            $ident = $labid.'-F-0';
        }
			else{ //loop through returns , trim to create identifier and increment highest value
			$xia = array();
			foreach ($link as $path){
			$path1= substr($path,'30');
			$path2=	strrchr($path1,"-");
			$path= str_replace($path2,'',$path1);
			$xi= ltrim($path2,"-");
			$xnew = array_push($xia,$xi);
			}
			$num = max($xia);
			$numinc = ($num+1);
			$ident = $labid.'-F-'.$numinc;
			}

			return $ident;
		}

    function setPid($form_values){
        // set pid custom pid for fractions... add identifier to base... finish with getNextPid pid is generated at form_values level as 'pid'
        $root_pid = substr($form_values['collection_pid'],0,17);
        $form_id = $this->setIdentifier($form_values);
        $pid = $root_pid.'-'.$form_id;
        return $pid;
    }

/*
 * method overrides method in FormBuilder.  We changed the dsid from OBJ to FRACTION
 */
    function createFractionStream($form_values,&$dom,&$rootElement){
        $datastream = $dom->createElement("foxml:datastream");
        $datastream->setAttribute("ID","FRACTION");
        $datastream->setAttribute("STATE","A");
        $datastream->setAttribute("CONTROL_GROUP","X");
        $version = $dom->createElement("foxml:datastreamVersion");
        $version->setAttribute("ID","FRACTION.0");
        $version->setAttribute("MIMETYPE","text/xml");
        $version->setAttribute("LABEL",$this->setIdentifier($form_values).' Fraction');
        $datastream->appendChild($version);
        $content = $dom->createElement("foxml:xmlContent");
        $version->appendChild($content);
        ///begin writing fraction
        $oai = $dom->createElement("fractions:sample");
        $oai->setAttribute('xmlns:fractions',"http://vre.upei.ca/fractions/");
        $content->appendChild($oai);
        //dc elements
        $element = $dom->createElement('fractions:identifier', $this->setIdentifier($form_values));
        $oai->appendChild($element);
        $element = $dom->createElement('fractions:plate', $form_values['fractions:plate']);
        $oai->appendChild($element);
        $element = $dom->createElement('fractions:weight', $form_values['fractions:weight']);
        $oai->appendChild($element);
        $element = $dom->createElement('fractions:ptp1b', $form_values['fractions:ptp1b']);
        $oai->appendChild($element);
        $element = $dom->createElement('fractions:ptp1b_com', $form_values['fractions:ptp1b_com']);
        $oai->appendChild($element);
        $element = $dom->createElement('fractions:hct116', $form_values['fractions:hct116']);
        $oai->appendChild($element);
        $element = $dom->createElement('fractions:hct116_com', $form_values['fractions:hct116_com']);
        $oai->appendChild($element);
        $element = $dom->createElement('fractions:hela', $form_values['fractions:hela']);
        $oai->appendChild($element);
        $element = $dom->createElement('fractions:hela_com', $form_values['fractions:hela_com']);
        $oai->appendChild($element);
        $element = $dom->createElement('fractions:pc3', $form_values['fractions:pc3']);
        $oai->appendChild($element);
        $element = $dom->createElement('fractions:pc3_com', $form_values['fractions:pc3_com']);
        $oai->appendChild($element);
        $element = $dom->createElement('fractions:are', $form_values['fractions:are']);
        $oai->appendChild($element);
        $element = $dom->createElement('fractions:are_com', $form_values['fractions:are_com']);
        $oai->appendChild($element);
        $element = $dom->createElement('fractions:antiproliferative', $form_values['fractions:antiproliferative']);
        $oai->appendChild($element);
        $element = $dom->createElement('fractions:antiproliferative_com',$form_values['fractions:antiproliferative_com']);
        $oai->appendChild($element);
        $element = $dom->createElement('fractions:location', $form_values['fractions:location']);
        $oai->appendChild($element);
        $element = $dom->createElement('fractions:notes', $form_values['fractions:notes']);
        $oai->appendChild($element);

        $rootElement->appendChild($datastream);
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
        $prop3->setAttribute("VALUE",$this->setIdentifier($form_values).' Fraction');
        $prop5 = $dom->createElement("foxml:property");
        $prop5->setAttribute("NAME","info:fedora/fedora-system:def/model#ownerId");
        $prop5->setAttribute("VALUE",$form_values['user_id']);
        //$objproperties->appendChild($prop1);
        $objproperties->appendChild($prop2);
        $objproperties->appendChild($prop3);
        $objproperties->appendChild($prop5);
        $rootElement->appendChild($objproperties);

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
        $rdfdesc->setAttribute("rdf:about",'info:fedora/'.$this->setPid($form_values));
        session_start();
        $parent= $_SESSION['pid'];
        $part = $dom->createElement("fedora:isPartOf");
        $part->setAttribute("rdf:resource","info:fedora/$parent");
        $member = $dom->createElement("fedora:isMemberOfCollection");
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
        $rdfdesc->appendChild($part);
        $rdfdesc->appendChild($rdfHasModel);
        $rootElement->appendChild($drdf);
        // unset($_SESSION['pid']);
    }


    function createReference(&$dom,&$rootElement){
         module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
        $objectHelper = new ObjectHelper();
         $parent= $_SESSION['pid'];
         $dsid = 'CRITTER';        
        $critterStreamDoc = $objectHelper->getStream($parent,$dsid,true);        
        try {
            $xml = new SimpleXMLElement($critterStreamDoc);
        } catch (Exception $e) {
            watchdog(t("Fedora_Repository"), "Problem getting Critter Stream!", WATCHDOG_ERROR);
            drupal_set_message(t('Problem getting Critter Stream! ') . $e->getMessage(), 'error');
            return false;
        }
        $critterElement = $dom->createDocumentFragment();
        if (!$critterElement) {
			drupal_set_message(t('error parsing Critter stream!'));
			watchdog(t("Fedora_Repository"), "Error parsing Critter stream, could not parse policy stream!", WATCHDOG_NOTICE);
			return false;
		}
		$dom->importNode($critterElement,true);
        $value=$critterElement->appendXML($critterStreamDoc);
		if(!$value){
			drupal_set_message(t('error creating specimen stream!'));
			watchdog(t("Fedora_Repository"), "Error creating specimen stream, could not parse critter template!", WATCHDOG_NOTICE);
			return false;
		}

        $ds1 = $dom->createElement("foxml:datastream");
		$rootElement->appendChild($ds1);
		$ds1->setAttribute("ID", "CRITTER");
		$ds1->setAttribute("STATE", "A");
		$ds1->setAttribute("CONTROL_GROUP", "X");
		$ds1v = $dom->createElement("foxml:datastreamVersion");
		$ds1->appendChild($ds1v);
		$ds1v->setAttribute("ID", "CRITTER.0");
		$ds1v->setAttribute("MIMETYPE", "text/xml");
		$ds1v->setAttribute("LABEL", "Parent Critter");
		
		$content = $dom->createElement("foxml:xmlContent");
		$ds1v->appendChild($content);
		$content->appendChild($critterElement);
		return true;
      
    }

    
    function createPolicy($collectionPid,&$dom,&$rootElement){
        module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
        $objectHelper = new ObjectHelper();

        $dsid = 'CHILD_SECURITY';
        $policyStreamDoc = $objectHelper->getStream($collectionPid,$dsid,true);
        
        try {
            $xml = new SimpleXMLElement($policyStreamDoc);
        } catch (Exception $e) {
            watchdog(t("Fedora_Repository"), "Problem getting Security Policy!", WATCHDOG_ERROR);
            drupal_set_message(t('Problem getting Security Policy! ') . $e->getMessage(), 'error');
            return false;
        }
        $policyElement = $dom->createDocumentFragment();
        if (!$policyElement) {
			drupal_set_message(t('error parsing security policy stream!'));
			watchdog(t("Fedora_Repository"), "Error parsing security policy stream, could not parse policy stream!", WATCHDOG_NOTICE);
			return false;
		}
		$dom->importNode($policyElement,true);
        $value=$policyElement->appendXML($policyStreamDoc);
		if(!$value){
			drupal_set_message(t('error creating security policy stream!'));
			watchdog(t("Fedora_Repository"), "Error creating security policy stream, could not parse collection policy template file!", WATCHDOG_NOTICE);
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



    function buildFractionForm(&$form,$ingest_form_definition,&$form_values){

        $form['indicator2']=array(
                '#type' => 'fieldset',
                '#title' => t('Add a New Fraction')
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
            if($element->type=='radios'){
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
                '#options'=>$options,
                '#default_value'=>'No_Assay'
                );
	$form['#redirect'] = 'fedora/repository/'.$_SESSION['pid'];
            }else{
                $form['indicator2']["$name"]=array(
                '#title' => $title,
                '#required' => $required,
                '#description' => $description,
                '#type' => $type
                );
	$form['#redirect'] = 'fedora/repository/'.$_SESSION['pid'];
            }

        }

        return $form;
    }

    function handleFractionForm($form_values){
        // ingest form
        $dom = new DomDocument("1.0","UTF-8");
        $dom->formatOutput = true;
        $rootElement = $dom->createElement("foxml:digitalObject");
        $rootElement->setAttribute('VERSION','1.1');
        $pid=$this->setPid($form_values);
        $rootElement->setAttribute('PID',$pid);
        $rootElement->setAttribute('xmlns:foxml',"info:fedora/fedora-system:def/foxml#");
        $rootElement->setAttribute('xmlns:xsi',"http://www.w3.org/2001/XMLSchema-instance");
        $rootElement->setAttribute('xsi:schemaLocation',"info:fedora/fedora-system:def/foxml# http://www.fedora.info/definitions/1/0/foxml1-0.xsd");
        $dom->appendChild($rootElement);
        //create standard fedora stuff
        $this->createStandardFedoraStuff($form_values,$dom, $rootElement);
        //create relationships
        $this->createRelationShips($form_values,$dom, $rootElement);
        //create fraction
        $this->createFractionStream($form_values,$dom, $rootElement);
        $this->createReference($dom, $rootElement);
        $parent= $form_values['collection_pid'];
        $this->createPolicy($parent,$dom,$rootElement);
        // test form output
        //$test = $dom->saveXML();
        //var_dump($test);exit(0);


        $params = array('objectXML' => $dom->saveXML(), 'format' =>"info:fedora/fedora-system:FOXML-1.1", 'logMessage'=>"Fedora Object Ingested");

        try{
            $soapHelper = new ConnectionHelper();
            $client=$soapHelper->getSoapClient(variable_get('fedora_soap_manage_url', 'http://localhost:8080/fedora/services/management?wsdl'));

            if($client==null){
                drupal_set_message(t('Error Getting Soap Client.'),'error');
                return;
            }
            $object=$client->__soapCall('ingest', array($params));
        }catch(exception $e){
            drupal_set_message(t('Error Ingesting Fraction Object! ').$e->getMessage(),'error');
            watchdog(t("Fedora_Repository"), t("Error Ingesting Fraction Object!").$e->getMessage(), WATCHDOG_ERROR);

            return;
        }
    }
}
?>
