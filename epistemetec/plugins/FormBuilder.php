<?php
module_load_include('php', 'Fedora_Repository', 'plugins/FormBuilder');
/*
 * Created on 19-Feb-0
 *
 *
 * implements methods from content model ingest form xml
 * builds a dc metadata form
 */
class EpistemetecFormBuilder extends FormBuilder {
	function EpistemetecFormBuilder() {
		module_load_include('php', 'Fedora_Repository', 'plugins/FormBuilder');
		drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

	}

	//Override this so we can specify the datastream id of FULL_SIZE  should make this easier
	function createFedoraDataStreams($form_values, & $dom, & $rootElement) {
		module_load_include('php', 'Fedora_Repository', 'mimetype');
		global $base_url;
		$mimetype = new mimetype();
		$server = null;
		$file = $form_values['ingest-file-location'];

		if (!empty ($file)) {
			$dformat = $mimetype->getType($file);
			$fileUrl = $base_url . '/' . drupal_urlencode($file);
			$beginIndex = strrpos($fileUrl, '/');
			$dtitle = substr($fileUrl, $beginIndex +1);
			$dtitle = urldecode($dtitle);
			//		$dtitle =  substr($dtitle, 0, strpos($dtitle, "."));
			$ds1 = $dom->createElement("foxml:datastream");
			$ds1->setAttribute("ID", "FULL_SIZE");
			$ds1->setAttribute("STATE", "A");
			$ds1->setAttribute("CONTROL_GROUP", "M");
			$ds1v = $dom->createElement("foxml:datastreamVersion");
			$rootElement->appendChild($ds1);

			$ds1v->setAttribute("ID", "FULL_SIZE.0");
			$ds1v->setAttribute("MIMETYPE", "$dformat");
			$ds1v->setAttribute("LABEL", "$dtitle");
			$ds1content = $dom->createElement('foxml:contentLocation');
			$ds1content->setAttribute("REF", "$fileUrl");
			$ds1content->setAttribute("TYPE", "URL");
			$ds1->appendChild($ds1v);
			$ds1v->appendChild($ds1content);
		}
		if (!empty ($_SESSION['fedora_ingest_files'])) {
			foreach ($_SESSION['fedora_ingest_files'] as $dsid => $createdFile) {
				$createdFile = strstr($createdFile, $file);
				$dformat = $mimetype->getType($createdFile);
				$fileUrl = $base_url . '/' . drupal_urlencode($createdFile);
				$beginIndex = strrpos($fileUrl, '/');
				$dtitle = substr($fileUrl, $beginIndex +1);
				$dtitle = urldecode($dtitle);
				//				$dtitle =  substr($dtitle, 0, strpos($dtitle, "."));
				$dtitle = $dtitle;
				$ds1 = $dom->createElement("foxml:datastream");
				$ds1->setAttribute("ID", "$dsid");
				$ds1->setAttribute("STATE", "A");
				$ds1->setAttribute("CONTROL_GROUP", "M");
				$ds1v = $dom->createElement("foxml:datastreamVersion");
				$ds1v->setAttribute("ID", "$dsid.0");
				$ds1v->setAttribute("MIMETYPE", "$dformat");
				$ds1v->setAttribute("LABEL", "$dtitle");
				$ds1content = $dom->createElement('foxml:contentLocation');
				$ds1content->setAttribute("REF", "$fileUrl");
				$ds1content->setAttribute("TYPE", "URL");
				$ds1->appendChild($ds1v);
				$ds1v->appendChild($ds1content);
				$rootElement->appendChild($ds1);
			}
		}

	}

	//create the Mag datastream
	function createMAGStream($form_values, & $dom, & $rootElement, $type) {
		$datastream = $dom->createElement("foxml:datastream");
		$datastream->setAttribute("ID", "MAG");
		$datastream->setAttribute("STATE", "A");
		$datastream->setAttribute("CONTROL_GROUP", "X");
		$version = $dom->createElement("foxml:datastreamVersion");
		$version->setAttribute("ID", "MAG.0");
		$version->setAttribute("MIMETYPE", "text/xml");
		$version->setAttribute("LABEL", "Mag Metadata");
		$datastream->appendChild($version);
		$content = $dom->createElement("foxml:xmlContent");
		$version->appendChild($content);
		///begin writing qdc
		$mag = $dom->createElement("mag");
		// 	  $oai->setAttribute('xmlns:oai_dc',"http://www.openarchives.org/OAI/2.0/oai_dc/");
		// 	  $oai->setAttribute('xmlns:dc',"http://purl.org/dc/elements/1.1/");
		// 	  $oai->setAttribute('xmlns:dcterms',"http://purl.org/dc/terms/");
		// 	  $oai->setAttribute('xmlns:xsi',"http://www.w3.org/2001/XMLSchema-instance");
		$content->appendChild($mag);

		if ($type == 'audio') {
			$aud = $dom->createElement("audio");
			$proxies = $dom->createElement("proxies");
			$metrics = $dom->createElement("audio_metrics");
			$format = $dom->createElement("format");
			$proxies->appendChild($metrics);
			$proxies->appendChild($format);
			$aud->appendChild($proxies);
			$mag->appendChild($aud);

		}
		if ($type == 'image') {
			$img = $dom->createElement("img");
			$mag->appendChild($img);
		}
		if ($type == 'video') {
			$vid = $dom->createElement("video");
			$proxies = $dom->createElement("proxies");
			$metrics = $dom->createElement("video_metrics");
			$dimensions = $dom->createElement("video_dimensions");
			$format = $dom->createElement("format");
			$proxies->appendChild($metrics);
			$proxies->appendChild($dimensions);
			$proxies->appendChild($format);
			$vid->appendChild($proxies);
			$mag->appendChild($vid);
		}

		$dis = $dom->createElement("dis");
		$gen = $dom->createElement("gen");
		$bib = $dom->createElement("bib");
		$drup = $dom->createElement("dru");

		$mag->appendChild($gen);
		$mag->appendChild($bib);
		$mag->appendChild($dis);
		$mag->appendChild($drup);
		//dc elements
		$previousElement = null; //used in case we have to nest elements for qualified dublin core
		foreach ($form_values as $key => $value) {
			$index = strrpos($key, '-');
			if ($index > 01) {
				$key = substr($key, 0, $index);
			}
			$value = trim($value);
			$test = substr($key, 0, 3);
			$element = substr($key, 4);
			if ($test == 'img') { //don't try to process other form values
				try {

					$previousElement = $dom->createElement($element, $value);
					$img->appendChild($previousElement);

				} catch (exception $e) {
					drupal_set_message(t($e->getMessage()), 'error');
					continue;
				}
			}
			if ($test == 'aud') { //don't try to process other form values
				try {
					$previousElement = $dom->createElement($element, $value);
					if (in_array($element, array (
							'file',
							'md5',
							'size',
							'note'
						)))
						$proxies->appendChild($previousElement);
					elseif (in_array($element, array (
						'bitrate',
						'samplingfrequency'
					))) $metrics->appendChild($previousElement);
					elseif (in_array($element, array (
						'compression',
						'mime',
						'channel'
					))) $format->appendChild($previousElement);
					else
						$aud->appendChild($previousElement);

				} catch (exception $e) {
					drupal_set_message(t($e->getMessage()), 'error');
					continue;
				}
			}
			if ($test == 'vid') { //don't try to process other form values
				try {

					$previousElement = $dom->createElement($element, $value);
					if (in_array($element, array (
							'file',
							'md5',
							'size',
							'note'
						)))
						$proxies->appendChild($previousElement);
					elseif (in_array($element, array (
						'bitrate',
						'framerate'
					))) $metrics->appendChild($previousElement);
					elseif (in_array($element, array (
						'framewidth',
						'frameheight'
					))) $dimensions->appendChild($previousElement);
					elseif (in_array($element, array (
						//'compression',
						'mime',
						//'channel'
					))) $format->appendChild($previousElement);
					else
						$vid->appendChild($previousElement);

				} catch (exception $e) {
					drupal_set_message(t($e->getMessage()), 'error');
					continue;
				}
			}
			if ($test == 'dis') { //don't try to process other form values
				try {

					$previousElement = $dom->createElement($element, $value);
					$dis->appendChild($previousElement);

				} catch (exception $e) {
					drupal_set_message(t($e->getMessage()), 'error');
					continue;
				}
			}

			if ($test == 'gen') { //don't try to process other form values
				try {

					$previousElement = $dom->createElement($element, $value);
					$gen->appendChild($previousElement);

				} catch (exception $e) {
					drupal_set_message(t($e->getMessage()), 'error');
					continue;
				}
			}

			if ($test == 'bib') { //don't try to process other form values
				try {

					$previousElement = $dom->createElement($element, $value);
					$bib->appendChild($previousElement);

				} catch (exception $e) {
					drupal_set_message(t($e->getMessage()), 'error');
					continue;
				}
			}

			if ($test == 'dru') { //don't try to process other form values
				try {

					$previousElement = $dom->createElement($element, $value);
					$drup->appendChild($previousElement);

				} catch (exception $e) {
					drupal_set_message(t($e->getMessage()), 'error');
					continue;
				}
			}

			$rootElement->appendChild($datastream);

		}
	}

	function buildImageForm(& $form, $ingest_form_definition, & $form_values) {
		//		$form['indicator2'] = array (
		//			'#type' => 'fieldset',
		//			'#title' => t('Dublin CORE'),
		//			'#collapsible' => TRUE,
		//			'#collapsed' => TRUE,
		//			
		//		);
		$form['indicator3'] = array (
			'#type' => 'fieldset',
			'#title' => t('IMG DATA'),
			'#collapsible' => TRUE,
			'#collapsed' => TRUE,

			
		);
		$form['indicator4'] = array (
			'#type' => 'fieldset',
			'#title' => t('DIS DATA'),
			'#collapsible' => TRUE,
			'#collapsed' => TRUE,

			
		);
		$form['indicator5'] = array (
			'#type' => 'fieldset',
			'#title' => t('GEN DATA'),
			'#collapsible' => TRUE,
			'#collapsed' => TRUE,

			
		);
		$form['indicator6'] = array (
			'#type' => 'fieldset',
			'#title' => t('BIB DATA'),
			'#collapsible' => TRUE,
			'#collapsed' => TRUE,

			
		);
		foreach ($ingest_form_definition->form_elements->element as $element) {
			$name = strip_tags($element->name->asXML());
			$title = strip_tags($element->label->asXML());
			$required = strip_tags($element->required->asXML());
			$required = strtolower($required);
			if ($required != 'true') {
				$required = '0';

			}

			$description = strip_tags($element->description->asXML());
			$type = strip_tags($element->type->asXML());
			$options = array ();
			//			if (stripos($name, 'dc:') !== false)
			//				$indicator = 'indicator2';
			//			else
			if (stripos($name, 'img_') !== false)
				$indicator = 'indicator3';
			else
				if (stripos($name, 'dis_') !== false)
					$indicator = 'indicator4';
				else
					if (stripos($name, 'gen_') !== false)
						$indicator = 'indicator5';
					else
						if (stripos($name, 'bib_') !== false)
							$indicator = 'indicator6';
			if ($element->type == 'select') {
				foreach ($element->authoritative_list->item as $item) {
					$field = strip_tags($item->field->asXML());
					$value = strip_tags($item->value->asXML());
					$options["$field"] = $value;
				}

				$form["$indicator"]["$name"] = array (
					'#title' => $title,
					'#required' => $required,
					'#description' => $description,
					'#type' => $type,
					'#options' => $options
				);

			} else {
				$form["$indicator"]["$name"] = array (
					'#title' => $title,
					'#required' => $required,
					'#description' => $description,
					'#type' => $type
				);
			}
		}

		return $form;
	}
	function buildAudioForm(& $form, $ingest_form_definition, & $form_values) {

		$form['indicator3'] = array (
			'#type' => 'fieldset',
			'#title' => t('AUDIO DATA'),
			'#collapsible' => TRUE,
			'#collapsed' => TRUE,

			
		);
		$form['indicator4'] = array (
			'#type' => 'fieldset',
			'#title' => t('DIS DATA'),
			'#collapsible' => TRUE,
			'#collapsed' => TRUE,

			
		);
		$form['indicator5'] = array (
			'#type' => 'fieldset',
			'#title' => t('GEN DATA'),
			'#collapsible' => TRUE,
			'#collapsed' => TRUE,

			
		);
		$form['indicator6'] = array (
			'#type' => 'fieldset',
			'#title' => t('BIB DATA'),
			'#collapsible' => TRUE,
			'#collapsed' => TRUE,

			
		);
		foreach ($ingest_form_definition->form_elements->element as $element) {
			$name = strip_tags($element->name->asXML());
			$title = strip_tags($element->label->asXML());
			$required = strip_tags($element->required->asXML());
			$required = strtolower($required);
			if ($required != 'true') {
				$required = '0';

			}

			$description = strip_tags($element->description->asXML());
			$type = strip_tags($element->type->asXML());
			$options = array ();
			//			if (stripos($name, 'dc:') !== false)
			//				$indicator = 'indicator2';
			//			else
			if (stripos($name, 'aud_') !== false)
				$indicator = 'indicator3';
			else
				if (stripos($name, 'dis_') !== false)
					$indicator = 'indicator4';
				else
					if (stripos($name, 'gen_') !== false)
						$indicator = 'indicator5';
					else
						if (stripos($name, 'bib_') !== false)
							$indicator = 'indicator6';
			if ($element->type == 'select') {
				foreach ($element->authoritative_list->item as $item) {
					$field = strip_tags($item->field->asXML());
					$value = strip_tags($item->value->asXML());
					$options["$field"] = $value;
				}

				$form["$indicator"]["$name"] = array (
					'#title' => $title,
					'#required' => $required,
					'#description' => $description,
					'#type' => $type,
					'#options' => $options
				);

			} else {
				$form["$indicator"]["$name"] = array (
					'#title' => $title,
					'#required' => $required,
					'#description' => $description,
					'#type' => $type
				);
			}
		}

		return $form;
	}

	function buildVideoForm(& $form, $ingest_form_definition, & $form_values) {

		$form['indicator3'] = array (
			'#type' => 'fieldset',
			'#title' => t('VIDEO DATA'),
			'#collapsible' => TRUE,
			'#collapsed' => TRUE,

			
		);
		$form['indicator4'] = array (
			'#type' => 'fieldset',
			'#title' => t('DIS DATA'),
			'#collapsible' => TRUE,
			'#collapsed' => TRUE,

			
		);
		$form['indicator5'] = array (
			'#type' => 'fieldset',
			'#title' => t('GEN DATA'),
			'#collapsible' => TRUE,
			'#collapsed' => TRUE,

			
		);
		$form['indicator6'] = array (
			'#type' => 'fieldset',
			'#title' => t('BIB DATA'),
			'#collapsible' => TRUE,
			'#collapsed' => TRUE,

			
		);
		foreach ($ingest_form_definition->form_elements->element as $element) {
			$name = strip_tags($element->name->asXML());
			$title = strip_tags($element->label->asXML());
			$required = strip_tags($element->required->asXML());
			$required = strtolower($required);
			if ($required != 'true') {
				$required = '0';

			}

			$description = strip_tags($element->description->asXML());
			$type = strip_tags($element->type->asXML());
			$options = array ();
			//			if (stripos($name, 'dc:') !== false)
			//				$indicator = 'indicator2';
			//			else
			if (stripos($name, 'vid_') !== false)
				$indicator = 'indicator3';
			else
				if (stripos($name, 'dis_') !== false)
					$indicator = 'indicator4';
				else
					if (stripos($name, 'gen_') !== false)
						$indicator = 'indicator5';
					else
						if (stripos($name, 'bib_') !== false)
							$indicator = 'indicator6';
			if ($element->type == 'select') {
				foreach ($element->authoritative_list->item as $item) {
					$field = strip_tags($item->field->asXML());
					$value = strip_tags($item->value->asXML());
					$options["$field"] = $value;
				}

				$form["$indicator"]["$name"] = array (
					'#title' => $title,
					'#required' => $required,
					'#description' => $description,
					'#type' => $type,
					'#options' => $options
				);

			} else {
				$form["$indicator"]["$name"] = array (
					'#title' => $title,
					'#required' => $required,
					'#description' => $description,
					'#type' => $type
				);
			}
		}

		return $form;
	}

	function handleImageForm($form_values, $isBook = FALSE) {
		global $base_url;
		module_load_include('php', 'Fedora_Repository', 'api/fedora_item');
		$dom = new DomDocument("1.0", "UTF-8");
		$dom->formatOutput = true;
		$pid = $form_values['pid'];
		$title = $form_values['img_nomenclature'];
		$rootElement = $dom->createElement("foxml:digitalObject");
		$rootElement->setAttribute('VERSION', '1.1');
		$rootElement->setAttribute('PID', "$pid");
		$rootElement->setAttribute('xmlns:foxml', "info:fedora/fedora-system:def/foxml#");
		$rootElement->setAttribute('xmlns:xsi', "http://www.w3.org/2001/XMLSchema-instance");
		$rootElement->setAttribute('xsi:schemaLocation', "info:fedora/fedora-system:def/foxml# http://www.fedora.info/definitions/1/0/foxml1-1.xsd");
		$dom->appendChild($rootElement);
		//create standard fedora stuff
		$this->createStandardFedoraStuff($form_values, $dom, $rootElement);
		//create relationships
		$this->createRelationShips($form_values, $dom, $rootElement);
		//create dublin core
		$myForm = array ();
		$myForm['dc:title'] = $title;
		$this->createQDCStream($myForm, $dom, $rootElement);
		//create mag metadata
		module_load_include('php', 'Fedora_Repository', 'epistemetec/plugins/ImageManipulation');
		$image = new EpistemetecImageManipulation();
		$image->getMetadata($form_values);
		$ccks = array ();

		if (!$isBook) {
			module_load_include('php', 'Fedora_Repository', 'epistemetec/plugins/Node');
			$node = new EpistemetecNode();
			$ccks = $node->hashCCK($form_values, IMAGE_DL);
			$nid = $node->createNode($ccks, ITEM_DL);
			$form_values['dru_nurl'] = $base_url . '/node/' . $nid;
			$form_values['dru_nid'] =  $nid;
		}

		$this->createMAGStream($form_values, $dom, $rootElement, 'image');
		if (!empty ($form_values['ingest-file-location'])) {
			$this->createFedoraDataStreams($form_values, $dom, $rootElement);
		}
		$collectionPid = $form_values['collection_pid'];
		$this->createPolicy($collectionPid, & $dom, & $rootElement);

		try {
			$object = Fedora_Item :: ingest_from_FOXML($dom);
			if (!empty ($object->pid)) {
				drupal_set_message("Item " . l($object->pid, 'fedora/repository/' . $object->pid) . " created successfully.", "status");
				//TODO: creare qui il nuovo nodo drupal e caricare il valore del pid in un campo CCK
			}
			if (!empty ($_SESSION['fedora_ingest_files'])) {
				foreach ($_SESSION['fedora_ingest_files'] as $dsid => $createdFile) {
					file_delete($createdFile);
				}
			}
			file_delete($form_values['ingest-file-location']);
			if (!$isBook) {
				$node->addNodeReference($ccks);
			}
		} catch (exception $e) {
			node_delete($nid);
			drupal_set_message(t('Error Ingesting Object! ') . $e->getMessage(), 'error');
			watchdog(t("Fedora_Repository"), t("Error Ingesting Object!") . $e->getMessage(), NULL, WATCHDOG_ERROR);
			return;
		}

	}

	function handleAudioForm($form_values) {
		global $base_url;
		module_load_include('php', 'Fedora_Repository', 'api/fedora_item');
		$dom = new DomDocument("1.0", "UTF-8");
		$dom->formatOutput = true;
		$pid = $form_values['pid'];
		$title = $form_values['aud_nomenclature'];
		$rootElement = $dom->createElement("foxml:digitalObject");
		$rootElement->setAttribute('VERSION', '1.1');
		$rootElement->setAttribute('PID', "$pid");
		$rootElement->setAttribute('xmlns:foxml', "info:fedora/fedora-system:def/foxml#");
		$rootElement->setAttribute('xmlns:xsi', "http://www.w3.org/2001/XMLSchema-instance");
		$rootElement->setAttribute('xsi:schemaLocation', "info:fedora/fedora-system:def/foxml# http://www.fedora.info/definitions/1/0/foxml1-1.xsd");
		$dom->appendChild($rootElement);
		//create standard fedora stuff
		$this->createStandardFedoraStuff($form_values, $dom, $rootElement);
		//create relationships
		$this->createRelationShips($form_values, $dom, $rootElement);
		//create dublin core
		$myForm = array ();
		$myForm['dc:title'] = $title;
		$this->createQDCStream($myForm, $dom, $rootElement);
		//create mag metadata
		module_load_include('php', 'Fedora_Repository', 'epistemetec/plugins/FileManipulation');
		$audio = new FileManipulation();
		$audio->getAudioMetadata($form_values);
		$ccks = array ();
		module_load_include('php', 'Fedora_Repository', 'epistemetec/plugins/Node');
		$node = new EpistemetecNode();
		$ccks = $node->hashCCK($form_values, AUDIO_DL);
		$nid = $node->createNode($ccks, ITEM_DL);
			$form_values['dru_nurl'] = $base_url . '/node/' . $nid;
			$form_values['dru_nid'] =  $nid;
		//$form_values['dru_nid'] = $base_url . '/node/' . $node->createNode($ccks, ITEM_DL);
		$this->createMAGStream($form_values, $dom, $rootElement, 'audio');
		if (!empty ($form_values['ingest-file-location'])) {
			$this->createFedoraDataStreams($form_values, $dom, $rootElement);
		}
		$collectionPid = $form_values['collection_pid'];
		$this->createPolicy($collectionPid, & $dom, & $rootElement);

		try {
			$object = Fedora_Item :: ingest_from_FOXML($dom);
			$file = "/var/www/drupal/sites/default/modules/fedora_repository/epistemetec/files/cover-audio.png";
			$object->add_datastream_from_file($file, "TN", "Thumbnail");
			if (!empty ($object->pid)) {
				drupal_set_message("Item " . l($object->pid, 'fedora/repository/' . $object->pid) . " created successfully.", "status");
				//TODO: creare qui il nuovo nodo drupal e caricare il valore del pid in un campo CCK
			}
			if (!empty ($_SESSION['fedora_ingest_files'])) {
				foreach ($_SESSION['fedora_ingest_files'] as $dsid => $createdFile) {
					file_delete($createdFile);
				}
			}
			file_delete($form_values['ingest-file-location']);
			$node->addNodeReference($ccks);
		} catch (exception $e) {
			node_delete($nid);
			drupal_set_message(t('Error Ingesting Object! ') . $e->getMessage(), 'error');
			watchdog(t("Fedora_Repository"), t("Error Ingesting Object!") . $e->getMessage(), NULL, WATCHDOG_ERROR);
			return;
		}

	}

	function handleVideoForm($form_values) {
		global $base_url;
		module_load_include('php', 'Fedora_Repository', 'api/fedora_item');
		$dom = new DomDocument("1.0", "UTF-8");
		$dom->formatOutput = true;
		$pid = $form_values['pid'];
		$title = $form_values['vid_nomenclature'];
		$rootElement = $dom->createElement("foxml:digitalObject");
		$rootElement->setAttribute('VERSION', '1.1');
		$rootElement->setAttribute('PID', "$pid");
		$rootElement->setAttribute('xmlns:foxml', "info:fedora/fedora-system:def/foxml#");
		$rootElement->setAttribute('xmlns:xsi', "http://www.w3.org/2001/XMLSchema-instance");
		$rootElement->setAttribute('xsi:schemaLocation', "info:fedora/fedora-system:def/foxml# http://www.fedora.info/definitions/1/0/foxml1-1.xsd");
		$dom->appendChild($rootElement);
		//create standard fedora stuff
		$this->createStandardFedoraStuff($form_values, $dom, $rootElement);
		//create relationships
		$this->createRelationShips($form_values, $dom, $rootElement);
		//create dublin core
		$myForm = array ();
		$myForm['dc:title'] = $title;
		$this->createQDCStream($myForm, $dom, $rootElement);
		//create mag metadata
		module_load_include('php', 'Fedora_Repository', 'epistemetec/plugins/FileManipulation');
		$audio = new FileManipulation();
		$audio->getVideoMetadata($form_values);
		$ccks = array ();
		module_load_include('php', 'Fedora_Repository', 'epistemetec/plugins/Node');
		$node = new EpistemetecNode();
		$ccks = $node->hashCCK($form_values, VIDEO_DL);
		$nid = $node->createNode($ccks, ITEM_DL);
			$form_values['dru_nurl'] = $base_url . '/node/' . $nid;
			$form_values['dru_nid'] =  $nid;
		//$form_values['dru_nid'] = $base_url . '/node/' . $node->createNode($ccks, ITEM_DL);
		$this->createMAGStream($form_values, $dom, $rootElement, 'video');
		if (!empty ($form_values['ingest-file-location'])) {
			$this->createFedoraDataStreams($form_values, $dom, $rootElement);
		}
		$collectionPid = $form_values['collection_pid'];
		$this->createPolicy($collectionPid, & $dom, & $rootElement);

		try {
			$object = Fedora_Item :: ingest_from_FOXML($dom);
			$file = "/var/www/drupal/sites/default/modules/fedora_repository/epistemetec/files/cover-video.png";
			$object->add_datastream_from_file($file, "TN", "Thumbnail");
			if (!empty ($object->pid)) {
				drupal_set_message("Item " . l($object->pid, 'fedora/repository/' . $object->pid) . " created successfully.", "status");
				//TODO: creare qui il nuovo nodo drupal e caricare il valore del pid in un campo CCK
			}
			if (!empty ($_SESSION['fedora_ingest_files'])) {
				foreach ($_SESSION['fedora_ingest_files'] as $dsid => $createdFile) {
					file_delete($createdFile);
				}
			}
			file_delete($form_values['ingest-file-location']);
			$node->addNodeReference($ccks);
		} catch (exception $e) {
			node_delete($nid);
			drupal_set_message(t('Error Ingesting Object! ') . $e->getMessage(), 'error');
			watchdog(t("Fedora_Repository"), t("Error Ingesting Object!") . $e->getMessage(), NULL, WATCHDOG_ERROR);
			return;
		}

	}
	function updateTags($pid, $terms) {

		$object = new Fedora_Item($pid);
		$stream = $object->get_datastream_dissemination("TAGS");
		if (strcasecmp($stream, ""))
			$object->purge_datastream("TAGS");

		$toinject = "<tags>";
		foreach ($terms as $term)
			$toinject .= "<tag>" . $term->name . "</tag>";
		$toinject .= "</tags>";
		$object->add_datastream_from_string($toinject, "TAGS", "Tag Utenti");

	}
}
?>
