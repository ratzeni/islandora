<?php
module_load_include('php', 'Fedora_Repository', 'plugins/CollectionFormBuilder');
module_load_include('php', 'Fedora_Repository', 'epistemetec/plugins/ImageManipulation');

/*
 * Created on 19-Feb-08
 *
 *
 * implements methods from content model ingest form xml
 * builds a dc metadata form
 */
class EpistemetecCollectionFormBuilder extends CollectionFormBuilder {
	function EpistemetecCollectionFormBuilder() {
		module_load_include('nc', 'CollectionFormBuilder', '');
		drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

	}

	private function createNode($form_values) {
		global $base_url;
		module_load_include('php', 'Fedora_Repository', 'epistemetec/plugins/Node');
		$node = new EpistemetecNode();
		$ccks = $node->hashCCK($form_values, COLLECTION_DL);
		$dru_nid = $base_url . '/node/' . $node->createNode($ccks, COLLECTION_DL);
		
		$pid = $form_values["pid"];
		$object = new Fedora_Item($pid);
		$stream = "<mag><dru><nid>".$dru_nid."</nid></dru></mag>";
		$object->add_datastream_from_string($stream, "MAG", "Mag Metadata");
		
	}
	private function codeText($alias) {
		$alias = preg_replace('#\W#', '-', $alias);
		$alias = preg_split('%[-]+%', $alias, -1, PREG_SPLIT_NO_EMPTY);
		$alias = implode("-", $alias);
		return $alias;
	}

	function ingestPage($list, $form_values) {
		$output = "<stru>";
		//rsort($list);
		$index = 1;
		foreach ($list as $myfile) {
			//drupal_set_message("File: ".$myfile);

			$temp = explode(" ", $myfile);
			$file = $temp[sizeof($temp) - 1];
			$file = trim(str_replace($_SERVER['DOCUMENT_ROOT'] . base_path(), "", $file));
			$image = new EpistemetecImageManipulation();
			$parameterArray = array (
				'width' => 160,
				'height' => 120
			);
			$folder = basename($form_values["ingest-file-location"], '.zip');
			$image->manipulateImage($parameterArray, "MEDIUM_SIZE", $file, 'jpg', $folder);
			$parameterArray = array (
				'width' => 120,
				'height' => 120
			);
			$image->manipulateImage($parameterArray, "TN", $file, 'jpg', $folder);
			//drupal_set_message("File: ".$file);
			$form = array ();
			$form["step"] = 1;
			$form["ingest-file-location"] = $file;
			$form["content_model_name"] = "ISLANDORACM";
			$form["models"] = "epistemetec:imageCModel/ISLANDORACM";
			$form["fullpath"] = "";
			//			$form["gen_stprog"] = $form_values["gen_stprog"];
			//			$form["gen_collection"] = $form_values["gen_collection"];
			//			$form["gen_agency"] = $form_values["gen_agency"];
			//			$form["gen_access_rights"] = $form_values["gen_access_rights"];
			//			$form["bib_title"] = $form_values["bib_title"];
			//			$form["bib_creator"] = $form_values["bib_creator"];
			//			$form["bib_subject"] = $form_values["bib_subject"];
			//			$form["bib_description"] = $form_values["bib_description"];
			//			$form["bib_publisher"] = $form_values["bib_publisher"];
			//			$form["bib_contributor"] = $form_values["bib_contributor"];
			//			$form["bib_date"] = $form_values["bib_date"];
			//			$form["bib_type"] = $form_values["bib_type"];
			//			$form["bib_source"] = $form_values["bib_source"];
			//			$form["bib_identifier"] = $form_values["bib_identifier"];
			//			$form["bib_language"] = $form_values["bib_language"];
			//			$form["bib_relation"] = $form_values["bib_relation"];
			//			$form["bib_rights"] = $form_values["bib_rights"];
			//			$form["bib_library"] = $form_values["bib_library"];
			//			$form["bib_inventory_number"] = $form_values["bib_inventory_number"];
			//			$form["bib_shelfmark"] = $form_values["bib_shelfmark"];

			$form["dis_item"] = basename($file, ".jpg");
			$form["img_sequence_number"] = $index;
			$form["img_nomenclature"] = basename($file, ".jpg");

			$form["collection_pid"] = $form_values["pid"];
			$form["op"] = "Ingest";
			$form["submit"] = "Ingest";
			$form["form_build_id"] = "";
			$form["form_token"] = "";
			$form["form_id"] = "fedora_repository_ingest_form";
			$form["user_id"] = "admin";

			$form["pid"] = $form["collection_pid"] . "-" . $index;
			$form["content_model_pid"] = "epistemetec:imageCModel";
			$form["relationship"] = "isMemberOfCollection";
			module_load_include('php', 'Fedora_Repository', 'epistemetec/plugins/FormBuilder');
			$handle = new EpistemetecFormBuilder();
			$handle->handleImageForm($form, TRUE);
			$index++;
			$output .= "<sequence>";
			$output .= "<sequence_number>" . $form["img_sequence_number"] . "</sequence_number>";
			$output .= "<nomenclature>" . $form["img_nomenclature"] . "</nomenclature>";
			$output .= "</sequence>";
		}
		$output .= "</stru>";
		return $output;

	}

	function createFedoraDataStreams($form_values, & $dom, & $rootElement) {
		module_load_include('php', 'Fedora_Repository', 'mimetype');
		global $base_url;

		$mimetype = new mimetype();
		$server = null;
		$file = $form_values['ingest-file-location'];
		$dformat = $mimetype->getType($file);
		$fileUrl = $base_url . '/' . drupal_urlencode($file);
		$beginIndex = strrpos($fileUrl, '/');
		$dtitle = substr($fileUrl, $beginIndex +1);
		$dtitle = substr($dtitle, 0, strpos($dtitle, "."));
		$ds1 = $dom->createElement("foxml:datastream");
		$ds1->setAttribute("ID", "COLLECTION_POLICY"); //set the ID
		$ds1->setAttribute("STATE", "A");
		$ds1->setAttribute("CONTROL_GROUP", "M");
		$ds1v = $dom->createElement("foxml:datastreamVersion");
		$ds1v->setAttribute("ID", "COLLECTION_POLICY.0");
		$ds1v->setAttribute("MIMETYPE", "$dformat");
		$ds1v->setAttribute("LABEL", "$dtitle");
		$ds1content = $dom->createElement('foxml:contentLocation');
		$ds1content->setAttribute("REF", "$fileUrl");
		$ds1content->setAttribute("TYPE", "URL");
		$ds1->appendChild($ds1v);
		$ds1v->appendChild($ds1content);
		$rootElement->appendChild($ds1);

	}

	//create the Mag datastream
	function createBookStream($form_values, & $dom, & $rootElement) {
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

		$gen = $dom->createElement("gen");
		$bib = $dom->createElement("bib");

		$mag->appendChild($gen);
		$mag->appendChild($bib);
		//dc elements
		$previousElement = null; //used in case we have to nest elements for qualified dublin core
		foreach ($form_values as $key => $value) {
			$index = strrpos($key, '-');
			if ($index > 01) {
				$key = substr($key, 0, $index);
			}

			$test = substr($key, 0, 3);

			if ($test == 'gen') { //don't try to process other form values
				try {
					if (!strcmp(substr($key, 0, 4), 'app_')) {
						$key = substr($key, 4);
						$previousElement->appendChild($dom->createElement($key, $value));

					} else {
						$previousElement = $dom->createElement(substr($key, 4), $value);
						$gen->appendChild($previousElement);
					}

				} catch (exception $e) {
					drupal_set_message(t($e->getMessage()), 'error');
					continue;
				}
			}

			if ($test == 'bib') { //don't try to process other form values
				try {
					if (!strcmp(substr($key, 0, 4), 'app_')) {
						$key = substr($key, 4);
						$previousElement->appendChild($dom->createElement($key, $value));

					} else {
						$previousElement = $dom->createElement(substr($key, 4), $value);
						$bib->appendChild($previousElement);
					}

				} catch (exception $e) {
					drupal_set_message(t($e->getMessage()), 'error');
					continue;
				}
			}
			$rootElement->appendChild($datastream);

		}
	}

	function buildBookForm(& $form, $ingest_form_definition, & $form_values) {

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

	function handleBookForm($form_values) {
		global $base_url;
		//		print_r($form_values);
		//		exit;
		module_load_include('php', 'Fedora_Repository', 'api/fedora_item');
		module_load_include('php', 'Fedora_Repository', 'epistemetec/plugins/FileManipulation');
		$dom = new DomDocument("1.0", "UTF-8");
		$dom->formatOutput = true;
		$pid = $form_values['pid'];
		$title = $form_values['gen_collection'];
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
		//module_load_include('php', 'Fedora_Repository', 'plugins/EpistemetecImageManipulation');
		//		$image = new EpistemetecImageManipulation();
		//		$image->getMetadata($form_values);
		//		//$form_values['dru_nid'] = $base_url . '/node/' . $this->createNode($pid, $title);
		$file = new FileManipulation();
		$list = $file->manipulateZip($form_values['ingest-file-location']);
		$ccks = array ();
		module_load_include('php', 'Fedora_Repository', 'epistemetec/plugins/Node');
		$node = new EpistemetecNode();
		$ccks = $node->hashCCK($form_values);
		$form_values['dru_nid'] = $base_url . '/node/' . $node->createNode($ccks, ITEM_DL);
		$this->createBookStream($form_values, $dom, $rootElement);

		//drupal_set_message($form_values['ingest-file-location']);
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
		} catch (exception $e) {
			node_delete($nid);
			drupal_set_message(t('Error Ingesting Object! ') . $e->getMessage(), 'error');
			watchdog(t("Fedora_Repository"), t("Error Ingesting Object!") . $e->getMessage(), NULL, WATCHDOG_ERROR);
			return;
		}

		$object = new Fedora_Item($pid);
		$stream = file_get_contents("/var/www/drupal/sites/default/modules/fedora_repository/epistemetec/xml/BOOK_COLLECTION_VIEW.xml");
		$object->add_datastream_from_string($stream, "COLLECTION_VIEW", "Collection View");

		$temp = explode(" ", $list[0]);
		$file = $temp[sizeof($temp) - 1];

		$image = new EpistemetecImageManipulation();
		$image = imageapi_image_open($file);
		imageapi_image_scale($image, 120, 120);
		$file = $file . "_TN";
		imageapi_image_close($image, $file);
		$object->add_datastream_from_file($file, "TN", "Thumbnail");

		$stream = $this->ingestPage($list, $form_values);
		$object->add_datastream_from_string($stream, "STRU", "Struttura Libro");
	}

	function handleImageCollectionForm($form_values) {

		$this->handleQDCForm($form_values);

		$pid = $form_values["pid"];
		$object = new Fedora_Item($pid);
		$stream = file_get_contents("/var/www/drupal/sites/default/modules/fedora_repository/epistemetec/xml/IMAGE_COLLECTION_POLICY.xml");
		$object->add_datastream_from_string($stream, "COLLECTION_POLICY", "Collection Policy");

		$file = "/var/www/drupal/sites/default/modules/fedora_repository/epistemetec/files/cover-img.png";
		$object->add_datastream_from_file($file, "TN", "Thumbnail");
		
		$this->createNode($form_values);
	}

	function handleAudioCollectionForm($form_values) {

		$this->handleQDCForm($form_values);
		$pid = $form_values["pid"];
		$object = new Fedora_Item($pid);
		$stream = file_get_contents("/var/www/drupal/sites/default/modules/fedora_repository/epistemetec/xml/AUDIO_COLLECTION_POLICY.xml");
		$object->add_datastream_from_string($stream, "COLLECTION_POLICY", "Collection Policy");

		$file = "/var/www/drupal/sites/default/modules/fedora_repository/epistemetec/files/cover-audio.png";
		$object->add_datastream_from_file($file, "TN", "Thumbnail");
		
		$this->createNode($form_values);

	}

	function handleVideoCollectionForm($form_values) {

		$this->handleQDCForm($form_values);

		$pid = $form_values["pid"];
		$object = new Fedora_Item($pid);
		$stream = file_get_contents("/var/www/drupal/sites/default/modules/fedora_repository/epistemetec/xml/VIDEO_COLLECTION_POLICY.xml");
		$object->add_datastream_from_string($stream, "COLLECTION_POLICY", "Collection Policy");

		$file = "/var/www/drupal/sites/default/modules/fedora_repository/epistemetec/files/cover-video.png";
		$object->add_datastream_from_file($file, "TN", "Thumbnail");
		
		$this->createNode($form_values);

	}
}
?>