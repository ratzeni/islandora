<?php

//module_load_include('php', 'Fedora_Repository', 'plugins/FormBuilder');
class EditStreams {
	//private $pid = null;
	function EditStreams($pid) {
		include_once 'includes/bootstrap.inc';
		drupal_bootstrap(DRUPAL_BOOTSRTAP_FULL);
		$this->pid = $pid;
	}

	function buildAlbumEditForm() {
		module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
		$object = new ObjectHelper();
		$spec = $object->getStream($this->pid, 'MAG', 0);
		$doc = new DomDocument();
		if (!isset ($spec)) {
			drupal_set_message(t('Error getting MAG metadata stream'), 'error');
			return null;
		}
		$xml = new SimpleXMLElement($spec);

		$form = array ();

		$nomenclature = $xml->xpath('//nomenclature');
		if ($nomenclature)
			$form['img_nomenclature'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($nomenclature),
				'#description' => 'Denominazione o titolo dell\'immagine.',
				'#required' => 'TRUE',
				'#title' => t('<strong>Nomenclature</strong>')
			);

		$note = $xml->xpath('//note');
		if ($note)
			$form['img_note'] = array (
				'#type' => 'textfield',
				'#description' => 'Eventuali annotazioni all\'immagine.',
				'#default_value' => implode($note),
				'#title' => t('<strong>Note</strong>')
			);

		$stprog = $xml->xpath('//stprog');
		if ($stprog)
			$form['gen_strrprog'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($stprog),
				'#description' => 'Indicazione del progetto di digitalizzazione.',
				'#title' => t('<strong>Progetto</strong>')
			);

		$collection = $xml->xpath('//collection');
		if ($collection)
			$form['gen_collection'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($collection),
				'#description' => 'Riferimento alla collezione di cui la risorsa digitale farà parte.',
				'#title' => t('<strong>Collezione</strong>')
			);

		$agency = $xml->xpath('//agency');
		if ($agency)
			$form['gen_agency'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($agency),
				'#description' => 'Agenzia responsabile del processo di digitalizzazione.',
				'#title' => t('<strong>Agenzia</strong>')
			);

		$completeness = $xml->xpath('//completeness');
		if ($completeness)
			$form['gen_completeness'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($completeness),
				'#description' => 'Completezza della digitalizzazione',
				'#title' => t('<strong>Completezza</strong>')
			);

		$access_rights = $xml->xpath('//access_rights');
		if ($access_rights)
			$form['gen_access_rights'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($access_rights),
				'#description' => 'Condizioni di accesso all\'oggetto descritto nella sezione BIB',
				'#title' => t('<strong>Condizioni di accesso</strong>')
			);

		$title = $xml->xpath('//title');
		if ($title)
			$form['bib_title'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($title),
				'#description' => 'The name given to the resource',
				'#title' => t('<strong>Title/Caption/Image Name</strong>')
			);

		$creator = $xml->xpath('//creator');
		if ($creator)
			$form['bib_creator'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($creator),
				'#description' => 'An entity primarily responsible for making the content of the resource such as a person, organization or service.',
				'#title' => t('<strong>Creator/Photographer</strong>')
			);

		$subject = $xml->xpath('//subject');
		if ($subject)
			$form['bib_subject'] = array (
				'#type' => 'select',
				'#options' => array (
					"image" => "image",
					"photograph" => "photograph",
					"presentation" => "presentation",
					"art" => "art",

					
				),
				'#description' => 'Subject',
				'#default_value' => implode($subject),
				'#title' => t('<strong>Subject</strong>')
			);

		$description = $xml->xpath('//description');
		if ($description)
			$form['bib_description'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($description),
				'#required' => 'true',
				'#description' => 'Description of the Image.',
				'#title' => t('<strong>Description</strong>')
			);

		$publisher = $xml->xpath('//publisher');
		if ($publisher)
			$form['bib_publisher'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($publisher),
				'#description' => 'An entity, (including persons, organizations, or services), responsible for making the resource available.',
				'#title' => t('<strong>Publisher</strong>')
			);

		$contributor = $xml->xpath('//contributor');
		if ($contributor)
			$form['bib_contributor'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($contributor),
				'#description' => 'An entity responsible for contributing to the content of the resource such as a person, organization or service.',
				'#title' => t('<strong>Contributor</strong>')
			);

		$date = $xml->xpath('//date');
		if ($date)
			$form['bib_date'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($date),
				'#description' => 'Temporal scope of the content if known. Date format is YYYY-MM-DD (e.g. 1890,1910-10,or 2007-10-23).',
				'#title' => t('<strong>Date</strong>')
			);

		$type = $xml->xpath('//type');
		if ($type)
			$form['bib_type'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($type),
				'#description' => 'Genre of the content of the resource. Examples include: home page, novel, poem, working paper, technical report, essay, dictionary.',
				'#title' => t('<strong>Resource Type</strong>')
			);

		$source = $xml->xpath('//source');
		if ($source)
			$form['bib_source'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($source),
				'#description' => 'A reference to a resource from which the present resource is derived.',
				'#title' => t('<strong>Source</strong>')
			);

		$identifier = $xml->xpath('//identifier');
		if ($identifier)
			$form['bib_identifier'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($identifier),
				'#description' => 'A unique reference to the resource; In this instance, the accession number or collection number.',
				'#title' => t('<strong>Identifier</strong>')
			);

		$language = $xml->xpath('//language');
		if ($language)
			$form['bib_language'] = array (
				'#type' => 'select',
				'#options' => array (
					"eng" => "English",
					"it" => "Italiano",
					"fr" => "French",

					
				),
				'#default_value' => implode($language),
				'#description' => 'The language of the intellectual content of the resource.',
				'#title' => t('<strong>Language</strong>')
			);

		$relation = $xml->xpath('//relation');
		if ($relation)
			$form['bib_relation'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($relation),
				'#description' => 'Reference to a related resource.',
				'#title' => t('<strong>Relation</strong>')
			);

		$rights = $xml->xpath('//rights');
		if ($rights)
			$form['bib_rights'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($rights),
				'#description' => 'Information about intellectual property rights, copyright, and various property rights.',
				'#title' => t('<strong>Rights Management</strong>')
			);

		$library = $xml->xpath('//library');
		if ($library)
			$form['bib_library'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($library),
				'#description' => 'Nome dell\'istituzione proprietaria dell\'oggetto analogico o di parte dell\'oggetto analogico.',
				'#title' => t('<strong>Proprietario</strong>')
			);

		$inventory_number = $xml->xpath('//inventory_number');
		if ($inventory_number)
			$form['bib_inventory_number'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($inventory_number),
				'#description' => 'Numero di inventario attribuito all\'oggetto analogico dall\'istituzione che lo possiede.',
				'#title' => t('<strong>Inventario</strong>')
			);
		$form['#redirect'] = 'fedora/repository/' . $this->pid;

		$form['pid'] = array (
			'#type' => 'hidden',
			'#value' => $this->pid
		);

		$form['submit'] = array (
			'#type' => 'submit',
			'#value' => 'Update'
		);
		//$form['form_id'] = array('#type' => 'hidden', '#value'=>'fedora_repository_edit_mag_form');

		return $form;
	}

	function buildAudioEditForm() {
		module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
		$object = new ObjectHelper();
		$spec = $object->getStream($this->pid, 'MAG', 0);
		$doc = new DomDocument();
		if (!isset ($spec)) {
			drupal_set_message(t('Error getting MAG metadata stream'), 'error');
			return null;
		}
		$xml = new SimpleXMLElement($spec);

		$form = array ();

		$nomenclature = $xml->xpath('//nomenclature');
		if ($nomenclature)
			$form['aud_nomenclature'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($nomenclature),
				'#description' => 'Denominazione o titolo della traccia audio.',
				'#required' => 'TRUE',
				'#title' => t('<strong>Nomenclature</strong>')
			);

		$note = $xml->xpath('//note');
		if ($note)
			$form['aud_note'] = array (
				'#type' => 'textfield',
				'#description' => 'Eventuali annotazioni alla traccia audio.',
				'#default_value' => implode($note),
				'#title' => t('<strong>Note</strong>')
			);

		$stprog = $xml->xpath('//stprog');
		if ($stprog)
			$form['gen_strrprog'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($stprog),
				'#description' => 'Indicazione del progetto di digitalizzazione.',
				'#title' => t('<strong>Progetto</strong>')
			);

		$collection = $xml->xpath('//collection');
		if ($collection)
			$form['gen_collection'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($collection),
				'#description' => 'Riferimento alla collezione di cui la risorsa digitale farà parte.',
				'#title' => t('<strong>Collezione</strong>')
			);

		$agency = $xml->xpath('//agency');
		if ($agency)
			$form['gen_agency'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($agency),
				'#description' => 'Agenzia responsabile del processo di digitalizzazione.',
				'#title' => t('<strong>Agenzia</strong>')
			);

		$completeness = $xml->xpath('//completeness');
		if ($completeness)
			$form['gen_completeness'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($completeness),
				'#description' => 'Completezza della digitalizzazione',
				'#title' => t('<strong>Completezza</strong>')
			);

		$access_rights = $xml->xpath('//access_rights');
		if ($access_rights)
			$form['gen_access_rights'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($access_rights),
				'#description' => 'Condizioni di accesso all\'oggetto descritto nella sezione BIB',
				'#title' => t('<strong>Condizioni di accesso</strong>')
			);

		$title = $xml->xpath('//title');
		if ($title)
			$form['bib_title'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($title),
				'#description' => 'The name given to the resource',
				'#title' => t('<strong>Title/Caption/Image Name</strong>')
			);

		$creator = $xml->xpath('//creator');
		if ($creator)
			$form['bib_creator'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($creator),
				'#description' => 'An entity primarily responsible for making the content of the resource such as a person, organization or service.',
				'#title' => t('<strong>Creator/Photographer</strong>')
			);

		$subject = $xml->xpath('//subject');
		if ($subject)
			$form['bib_subject'] = array (
				'#type' => 'select',
				'#options' => array (
					"audio" => "audio",
					"image" => "image",
					"photograph" => "photograph",
					"presentation" => "presentation",
					"art" => "art",

					
				),
				'#description' => 'Subject',
				'#default_value' => implode($subject),
				'#title' => t('<strong>Subject</strong>')
			);

		$description = $xml->xpath('//description');
		if ($description)
			$form['bib_description'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($description),
				'#required' => 'true',
				'#description' => 'Description of the Tracks.',
				'#title' => t('<strong>Description</strong>')
			);

		$publisher = $xml->xpath('//publisher');
		if ($publisher)
			$form['bib_publisher'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($publisher),
				'#description' => 'An entity, (including persons, organizations, or services), responsible for making the resource available.',
				'#title' => t('<strong>Publisher</strong>')
			);

		$contributor = $xml->xpath('//contributor');
		if ($contributor)
			$form['bib_contributor'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($contributor),
				'#description' => 'An entity responsible for contributing to the content of the resource such as a person, organization or service.',
				'#title' => t('<strong>Contributor</strong>')
			);

		$date = $xml->xpath('//date');
		if ($date)
			$form['bib_date'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($date),
				'#description' => 'Temporal scope of the content if known. Date format is YYYY-MM-DD (e.g. 1890,1910-10,or 2007-10-23).',
				'#title' => t('<strong>Date</strong>')
			);

		$type = $xml->xpath('//type');
		if ($type)
			$form['bib_type'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($type),
				'#description' => 'Genre of the content of the resource. Examples include: home page, novel, poem, working paper, technical report, essay, dictionary.',
				'#title' => t('<strong>Resource Type</strong>')
			);

		$source = $xml->xpath('//source');
		if ($source)
			$form['bib_source'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($source),
				'#description' => 'A reference to a resource from which the present resource is derived.',
				'#title' => t('<strong>Source</strong>')
			);

		$identifier = $xml->xpath('//identifier');
		if ($identifier)
			$form['bib_identifier'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($identifier),
				'#description' => 'A unique reference to the resource; In this instance, the accession number or collection number.',
				'#title' => t('<strong>Identifier</strong>')
			);

		$language = $xml->xpath('//language');
		if ($language)
			$form['bib_language'] = array (
				'#type' => 'select',
				'#options' => array (
					"eng" => "English",
					"it" => "Italiano",
					"fr" => "French",

					
				),
				'#default_value' => implode($language),
				'#description' => 'The language of the intellectual content of the resource.',
				'#title' => t('<strong>Language</strong>')
			);

		$relation = $xml->xpath('//relation');
		if ($relation)
			$form['bib_relation'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($relation),
				'#description' => 'Reference to a related resource.',
				'#title' => t('<strong>Relation</strong>')
			);

		$rights = $xml->xpath('//rights');
		if ($rights)
			$form['bib_rights'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($rights),
				'#description' => 'Information about intellectual property rights, copyright, and various property rights.',
				'#title' => t('<strong>Rights Management</strong>')
			);

		$library = $xml->xpath('//library');
		if ($library)
			$form['bib_library'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($library),
				'#description' => 'Nome dell\'istituzione proprietaria dell\'oggetto analogico o di parte dell\'oggetto analogico.',
				'#title' => t('<strong>Proprietario</strong>')
			);

		$inventory_number = $xml->xpath('//inventory_number');
		if ($inventory_number)
			$form['bib_inventory_number'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($inventory_number),
				'#description' => 'Numero di inventario attribuito all\'oggetto analogico dall\'istituzione che lo possiede.',
				'#title' => t('<strong>Inventario</strong>')
			);
		$form['#redirect'] = 'fedora/repository/' . $this->pid;

		$form['pid'] = array (
			'#type' => 'hidden',
			'#value' => $this->pid
		);

		$form['submit'] = array (
			'#type' => 'submit',
			'#value' => 'Update'
		);
		//$form['form_id'] = array('#type' => 'hidden', '#value'=>'fedora_repository_edit_mag_form');

		return $form;
	}

	function buildVideoEditForm() {
		module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
		$object = new ObjectHelper();
		$spec = $object->getStream($this->pid, 'MAG', 0);
		$doc = new DomDocument();
		if (!isset ($spec)) {
			drupal_set_message(t('Error getting MAG metadata stream'), 'error');
			return null;
		}
		$xml = new SimpleXMLElement($spec);

		$form = array ();

		$nomenclature = $xml->xpath('//nomenclature');
		if ($nomenclature)
			$form['vid_nomenclature'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($nomenclature),
				'#description' => 'Denominazione o titolo della traccia audio.',
				'#required' => 'TRUE',
				'#title' => t('<strong>Nomenclature</strong>')
			);

		$note = $xml->xpath('//note');
		if ($note)
			$form['vid_note'] = array (
				'#type' => 'textfield',
				'#description' => 'Eventuali annotazioni alla traccia audio.',
				'#default_value' => implode($note),
				'#title' => t('<strong>Note</strong>')
			);

		$stprog = $xml->xpath('//stprog');
		if ($stprog)
			$form['gen_strrprog'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($stprog),
				'#description' => 'Indicazione del progetto di digitalizzazione.',
				'#title' => t('<strong>Progetto</strong>')
			);

		$collection = $xml->xpath('//collection');
		if ($collection)
			$form['gen_collection'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($collection),
				'#description' => 'Riferimento alla collezione di cui la risorsa digitale farà parte.',
				'#title' => t('<strong>Collezione</strong>')
			);

		$agency = $xml->xpath('//agency');
		if ($agency)
			$form['gen_agency'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($agency),
				'#description' => 'Agenzia responsabile del processo di digitalizzazione.',
				'#title' => t('<strong>Agenzia</strong>')
			);

		$completeness = $xml->xpath('//completeness');
		if ($completeness)
			$form['gen_completeness'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($completeness),
				'#description' => 'Completezza della digitalizzazione',
				'#title' => t('<strong>Completezza</strong>')
			);

		$access_rights = $xml->xpath('//access_rights');
		if ($access_rights)
			$form['gen_access_rights'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($access_rights),
				'#description' => 'Condizioni di accesso all\'oggetto descritto nella sezione BIB',
				'#title' => t('<strong>Condizioni di accesso</strong>')
			);

		$title = $xml->xpath('//title');
		if ($title)
			$form['bib_title'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($title),
				'#description' => 'The name given to the resource',
				'#title' => t('<strong>Title/Caption/Image Name</strong>')
			);

		$creator = $xml->xpath('//creator');
		if ($creator)
			$form['bib_creator'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($creator),
				'#description' => 'An entity primarily responsible for making the content of the resource such as a person, organization or service.',
				'#title' => t('<strong>Creator/Photographer</strong>')
			);

		$subject = $xml->xpath('//subject');
		if ($subject)
			$form['bib_subject'] = array (
				'#type' => 'select',
				'#options' => array (
					"audio" => "audio",
					"image" => "image",
					"photograph" => "photograph",
					"presentation" => "presentation",
					"art" => "art",

					
				),
				'#description' => 'Subject',
				'#default_value' => implode($subject),
				'#title' => t('<strong>Subject</strong>')
			);

		$description = $xml->xpath('//description');
		if ($description)
			$form['bib_description'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($description),
				'#required' => 'true',
				'#description' => 'Description of the Tracks.',
				'#title' => t('<strong>Description</strong>')
			);

		$publisher = $xml->xpath('//publisher');
		if ($publisher)
			$form['bib_publisher'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($publisher),
				'#description' => 'An entity, (including persons, organizations, or services), responsible for making the resource available.',
				'#title' => t('<strong>Publisher</strong>')
			);

		$contributor = $xml->xpath('//contributor');
		if ($contributor)
			$form['bib_contributor'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($contributor),
				'#description' => 'An entity responsible for contributing to the content of the resource such as a person, organization or service.',
				'#title' => t('<strong>Contributor</strong>')
			);

		$date = $xml->xpath('//date');
		if ($date)
			$form['bib_date'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($date),
				'#description' => 'Temporal scope of the content if known. Date format is YYYY-MM-DD (e.g. 1890,1910-10,or 2007-10-23).',
				'#title' => t('<strong>Date</strong>')
			);

		$type = $xml->xpath('//type');
		if ($type)
			$form['bib_type'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($type),
				'#description' => 'Genre of the content of the resource. Examples include: home page, novel, poem, working paper, technical report, essay, dictionary.',
				'#title' => t('<strong>Resource Type</strong>')
			);

		$source = $xml->xpath('//source');
		if ($source)
			$form['bib_source'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($source),
				'#description' => 'A reference to a resource from which the present resource is derived.',
				'#title' => t('<strong>Source</strong>')
			);

		$identifier = $xml->xpath('//identifier');
		if ($identifier)
			$form['bib_identifier'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($identifier),
				'#description' => 'A unique reference to the resource; In this instance, the accession number or collection number.',
				'#title' => t('<strong>Identifier</strong>')
			);

		$language = $xml->xpath('//language');
		if ($language)
			$form['bib_language'] = array (
				'#type' => 'select',
				'#options' => array (
					"eng" => "English",
					"it" => "Italiano",
					"fr" => "French",

					
				),
				'#default_value' => implode($language),
				'#description' => 'The language of the intellectual content of the resource.',
				'#title' => t('<strong>Language</strong>')
			);

		$relation = $xml->xpath('//relation');
		if ($relation)
			$form['bib_relation'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($relation),
				'#description' => 'Reference to a related resource.',
				'#title' => t('<strong>Relation</strong>')
			);

		$rights = $xml->xpath('//rights');
		if ($rights)
			$form['bib_rights'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($rights),
				'#description' => 'Information about intellectual property rights, copyright, and various property rights.',
				'#title' => t('<strong>Rights Management</strong>')
			);

		$library = $xml->xpath('//library');
		if ($library)
			$form['bib_library'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($library),
				'#description' => 'Nome dell\'istituzione proprietaria dell\'oggetto analogico o di parte dell\'oggetto analogico.',
				'#title' => t('<strong>Proprietario</strong>')
			);

		$inventory_number = $xml->xpath('//inventory_number');
		if ($inventory_number)
			$form['bib_inventory_number'] = array (
				'#type' => 'textfield',
				'#default_value' => implode($inventory_number),
				'#description' => 'Numero di inventario attribuito all\'oggetto analogico dall\'istituzione che lo possiede.',
				'#title' => t('<strong>Inventario</strong>')
			);
		$form['#redirect'] = 'fedora/repository/' . $this->pid;

		$form['pid'] = array (
			'#type' => 'hidden',
			'#value' => $this->pid
		);

		$form['submit'] = array (
			'#type' => 'submit',
			'#value' => 'Update'
		);
		//$form['form_id'] = array('#type' => 'hidden', '#value'=>'fedora_repository_edit_mag_form');

		return $form;
	}

	function handleAlbumEditForm($form_id, $form_values, $soap_client) {
		//echo "FORM_ID: ".$form_id;
		$pid = $form_values['pid'];
		module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
		$object = new ObjectHelper($pid);
		$spec = $object->getStream($pid, 'MAG', TRUE);
		$xml = new SimpleXMLElement($spec);
		$dom = new DomDocument("1.0", "UTF-8");
		$dom->formatOutput = true;
		$mag = $dom->createElement("mag");

		//		$dom->appendChild($mag);

		$img = $dom->createElement("img");
		$dis = $dom->createElement("dis");
		$gen = $dom->createElement("gen");
		$bib = $dom->createElement("bib");
		$drup = $dom->createElement("dru");
		$img->appendChild($dom->createElement('sequence_number', implode($xml->xpath('//sequence_number'))));
		$img->appendChild($dom->createElement('size', implode($xml->xpath('//size'))));
		$img->appendChild($dom->createElement('md5', implode($xml->xpath('//md5'))));
		$img->appendChild($dom->createElement('file', implode($xml->xpath('//file'))));
		$img->appendChild($dom->createElement('imagelength', implode($xml->xpath('//imagelength'))));
		$img->appendChild($dom->createElement('imagewidth', implode($xml->xpath('//imagewidth'))));
		$img->appendChild($dom->createElement('format', implode($xml->xpath('//format'))));
		$img->appendChild($dom->createElement('mime', implode($xml->xpath('//mime'))));
		$img->appendChild($dom->createElement('samplingfrequencyunit', implode($xml->xpath('//samplingfrequencyunit'))));
		$img->appendChild($dom->createElement('samplingfrequencyplane', implode($xml->xpath('//samplingfrequencyplane'))));

		$dis->appendChild($dom->createElement('item', implode($xml->xpath('//item'))));
		$drup->appendChild($dom->createElement('nid', implode($xml->xpath('//nid'))));
		$drup->appendChild($dom->createElement('nurl', implode($xml->xpath('//nurl'))));

		$previousElement = null; //used in case we have to nest elements for qualified dublin core
		foreach ($form_values as $key => $value) {
			$index = strrpos($key, '-');
			if ($index > 01) {
				$key = substr($key, 0, $index);
			}

			$test = substr($key, 0, 3);

			if ($test == 'img') { //don't try to process other form values
				try {

					$previousElement = $dom->createElement(substr($key, 4), $value);
					$img->appendChild($previousElement);

				} catch (exception $e) {
					drupal_set_message(t($e->getMessage()), 'error');
					continue;
				}
			}
			if ($test == 'dis') { //don't try to process other form values
				try {

					$previousElement = $dom->createElement(substr($key, 4), $value);
					$dis->appendChild($previousElement);

				} catch (exception $e) {
					drupal_set_message(t($e->getMessage()), 'error');
					continue;
				}
			}

			if ($test == 'gen') { //don't try to process other form values
				try {

					$previousElement = $dom->createElement(substr($key, 4), $value);
					$gen->appendChild($previousElement);

				} catch (exception $e) {
					drupal_set_message(t($e->getMessage()), 'error');
					continue;
				}
			}

			if ($test == 'bib') { //don't try to process other form values
				try {

					$previousElement = $dom->createElement(substr($key, 4), $value);
					$bib->appendChild($previousElement);

				} catch (exception $e) {
					drupal_set_message(t($e->getMessage()), 'error');
					continue;
				}
			}

			if ($test == 'dru') { //don't try to process other form values
				try {

					$previousElement = $dom->createElement(substr($key, 4), $value);
					$drup->appendChild($previousElement);

				} catch (exception $e) {
					drupal_set_message(t($e->getMessage()), 'error');
					continue;
				}
			}

			//$rootElement->appendChild($datastream);

		}
		$mag->appendChild($img);
		$mag->appendChild($gen);
		$mag->appendChild($bib);
		$mag->appendChild($dis);
		$mag->appendChild($drup);

		$dom->appendChild($mag);

		$params = array (
			"pid" => "$pid",
			"dsID" => "MAG",
			"altIDs" => "",
			"dsLabel" => "Mag Metadata",
			"MIMEType" => "text/xml",
			"formatURI" => "",
			"dsContent" => $dom->saveXML(),
			"checksumType" => "DISABLED",
			"checksum" => "none",
			"logMessage" => "datastream_modified",
			"force" => "true"
		);

		$ccks = array ();
		module_load_include('php', 'Fedora_Repository', 'epistemetec/plugins/Node');
		$node = new EpistemetecNode();
		$ccks = $node->hashCCK($form_values);

		try {

			//$soapHelper = new ConnectionHelper();
			//$client = $soapHelper->getSoapClient(variable_get('fedora_soap_manage_url', 'http://localhost:8080/fedora/services/management?wsdl'));
			$object = $soap_client->__soapCall('ModifyDatastreamByValue', array (
				$params
			));

			$node->editNode($ccks);

			return $form_id;

		} catch (exception $e) {
			drupal_set_message(t("Error updating  MAG metadata ") . $e->getMessage(), 'error');

		}
	}

	function handleAudioEditForm($form_id, $form_values, $soap_client) {
		//echo "FORM_ID: ".$form_id;
		$pid = $form_values['pid'];
		module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
		$object = new ObjectHelper($pid);
		$spec = $object->getStream($pid, 'MAG', TRUE);
		$xml = new SimpleXMLElement($spec);
		$dom = new DomDocument("1.0", "UTF-8");
		$dom->formatOutput = true;
		$mag = $dom->createElement("mag");

		//		$dom->appendChild($mag);

		$aud = $dom->createElement("audio");
		$proxies = $dom->createElement("proxies");
		$metrics = $dom->createElement("audio_metrics");
		$format = $dom->createElement("format");
		$dis = $dom->createElement("dis");
		$gen = $dom->createElement("gen");
		$bib = $dom->createElement("bib");
		$drup = $dom->createElement("dru");
		$aud->appendChild($dom->createElement('sequence_number', implode($xml->xpath('//sequence_number'))));
		$proxies->appendChild($dom->createElement('size', implode($xml->xpath('//size'))));
		$proxies->appendChild($dom->createElement('md5', implode($xml->xpath('//md5'))));
		$proxies->appendChild($dom->createElement('file', implode($xml->xpath('//file'))));
		$format->appendChild($dom->createElement('compression', implode($xml->xpath('//compression'))));
		$metrics->appendChild($dom->createElement('bitrate', implode($xml->xpath('//bitrate'))));
		$metrics->appendChild($dom->createElement('samplingfrequency', implode($xml->xpath('//samplingfrequency'))));
		$format->appendChild($dom->createElement('mime', implode($xml->xpath('//mime'))));
		$format->appendChild($dom->createElement('channel', implode($xml->xpath('//channel'))));
		//		$aud->appendChild($dom->createElement('samplingfrequencyplane', implode($xml->xpath('//samplingfrequencyplane'))));

		$dis->appendChild($dom->createElement('item', implode($xml->xpath('//item'))));
		$drup->appendChild($dom->createElement('nid', implode($xml->xpath('//nid'))));
		$drup->appendChild($dom->createElement('nurl', implode($xml->xpath('//nurl'))));

		$previousElement = null; //used in case we have to nest elements for qualified dublin core
		foreach ($form_values as $key => $value) {
			$index = strrpos($key, '-');
			if ($index > 01) {
				$key = substr($key, 0, $index);
			}

			$value = trim($value);
			$test = substr($key, 0, 3);
			$element = substr($key, 4);

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
			if ($test == 'dis') { //don't try to process other form values
				try {

					$previousElement = $dom->createElement(substr($key, 4), $value);
					$dis->appendChild($previousElement);

				} catch (exception $e) {
					drupal_set_message(t($e->getMessage()), 'error');
					continue;
				}
			}

			if ($test == 'gen') { //don't try to process other form values
				try {

					$previousElement = $dom->createElement(substr($key, 4), $value);
					$gen->appendChild($previousElement);

				} catch (exception $e) {
					drupal_set_message(t($e->getMessage()), 'error');
					continue;
				}
			}

			if ($test == 'bib') { //don't try to process other form values
				try {

					$previousElement = $dom->createElement(substr($key, 4), $value);
					$bib->appendChild($previousElement);

				} catch (exception $e) {
					drupal_set_message(t($e->getMessage()), 'error');
					continue;
				}
			}

			if ($test == 'dru') { //don't try to process other form values
				try {

					$previousElement = $dom->createElement(substr($key, 4), $value);
					$drup->appendChild($previousElement);

				} catch (exception $e) {
					drupal_set_message(t($e->getMessage()), 'error');
					continue;
				}
			}

			//$rootElement->appendChild($datastream);

		}
		$proxies->appendChild($metrics);
		$proxies->appendChild($format);
		$aud->appendChild($proxies);
		$mag->appendChild($aud);
		$mag->appendChild($gen);
		$mag->appendChild($bib);
		$mag->appendChild($dis);
		$mag->appendChild($drup);

		$dom->appendChild($mag);

		$params = array (
			"pid" => "$pid",
			"dsID" => "MAG",
			"altIDs" => "",
			"dsLabel" => "Mag Metadata",
			"MIMEType" => "text/xml",
			"formatURI" => "",
			"dsContent" => $dom->saveXML(),
			"checksumType" => "DISABLED",
			"checksum" => "none",
			"logMessage" => "datastream_modified",
			"force" => "true"
		);

		$ccks = array ();
		module_load_include('php', 'Fedora_Repository', 'epistemetec/plugins/Node');
		$node = new EpistemetecNode();
		$ccks = $node->hashCCK($form_values);
		$node->editNode($ccks);
		//drupal_set_message($mag);
		try {

			//$soapHelper = new ConnectionHelper();
			//$client = $soapHelper->getSoapClient(variable_get('fedora_soap_manage_url', 'http://localhost:8080/fedora/services/management?wsdl'));
			$object = $soap_client->__soapCall('ModifyDatastreamByValue', array (
				$params
			));

			return $form_id;

		} catch (exception $e) {
			drupal_set_message(t("Error updating  MAG metadata ") . $e->getMessage(), 'error');

		}
	}

	function handleVideoEditForm($form_id, $form_values, $soap_client) {
		//echo "FORM_ID: ".$form_id;
		$pid = $form_values['pid'];
		module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
		$object = new ObjectHelper($pid);
		$spec = $object->getStream($pid, 'MAG', TRUE);
		$xml = new SimpleXMLElement($spec);
		$dom = new DomDocument("1.0", "UTF-8");
		$dom->formatOutput = true;
		$mag = $dom->createElement("mag");

		//		$dom->appendChild($mag);

		$vid = $dom->createElement("video");
		$proxies = $dom->createElement("proxies");
		$metrics = $dom->createElement("video_metrics");
		$dimensions = $dom->createElement("video_dimensions");
		$format = $dom->createElement("format");
		$dis = $dom->createElement("dis");
		$gen = $dom->createElement("gen");
		$bib = $dom->createElement("bib");
		$drup = $dom->createElement("dru");
		$vid->appendChild($dom->createElement('sequence_number', implode($xml->xpath('//sequence_number'))));
		$proxies->appendChild($dom->createElement('size', implode($xml->xpath('//size'))));
		$proxies->appendChild($dom->createElement('md5', implode($xml->xpath('//md5'))));
		$proxies->appendChild($dom->createElement('file', implode($xml->xpath('//file'))));
		$metrics->appendChild($dom->createElement('framerate', implode($xml->xpath('//framerate'))));
		$dimensions->appendChild($dom->createElement('framewidth', implode($xml->xpath('//framewidth'))));
		$dimensions->appendChild($dom->createElement('frameheight', implode($xml->xpath('//frameheight'))));
		$metrics->appendChild($dom->createElement('bitrate', implode($xml->xpath('//bitrate'))));
		$format->appendChild($dom->createElement('mime', implode($xml->xpath('//mime'))));
		//		$vid->appendChild($dom->createElement('samplingfrequencyunit', implode($xml->xpath('//samplingfrequencyunit'))));
		//		$vid->appendChild($dom->createElement('samplingfrequencyplane', implode($xml->xpath('//samplingfrequencyplane'))));

		$dis->appendChild($dom->createElement('item', implode($xml->xpath('//item'))));
		$drup->appendChild($dom->createElement('nid', implode($xml->xpath('//nid'))));
		$drup->appendChild($dom->createElement('nurl', implode($xml->xpath('//nurl'))));

		$previousElement = null; //used in case we have to nest elements for qualified dublin core
		foreach ($form_values as $key => $value) {
			$index = strrpos($key, '-');
			if ($index > 01) {
				$key = substr($key, 0, $index);
			}

			$value = trim($value);
			$test = substr($key, 0, 3);
			$element = substr($key, 4);

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

					$previousElement = $dom->createElement(substr($key, 4), $value);
					$dis->appendChild($previousElement);

				} catch (exception $e) {
					drupal_set_message(t($e->getMessage()), 'error');
					continue;
				}
			}

			if ($test == 'gen') { //don't try to process other form values
				try {

					$previousElement = $dom->createElement(substr($key, 4), $value);
					$gen->appendChild($previousElement);

				} catch (exception $e) {
					drupal_set_message(t($e->getMessage()), 'error');
					continue;
				}
			}

			if ($test == 'bib') { //don't try to process other form values
				try {

					$previousElement = $dom->createElement(substr($key, 4), $value);
					$bib->appendChild($previousElement);

				} catch (exception $e) {
					drupal_set_message(t($e->getMessage()), 'error');
					continue;
				}
			}

			if ($test == 'dru') { //don't try to process other form values
				try {

					$previousElement = $dom->createElement(substr($key, 4), $value);
					$drup->appendChild($previousElement);

				} catch (exception $e) {
					drupal_set_message(t($e->getMessage()), 'error');
					continue;
				}
			}

			//$rootElement->appendChild($datastream);

		}
		$proxies->appendChild($metrics);
		$proxies->appendChild($dimensions);
		$proxies->appendChild($format);
		$vid->appendChild($proxies);
		$mag->appendChild($vid);
		$mag->appendChild($gen);
		$mag->appendChild($bib);
		$mag->appendChild($dis);
		$mag->appendChild($drup);

		$dom->appendChild($mag);

		$params = array (
			"pid" => "$pid",
			"dsID" => "MAG",
			"altIDs" => "",
			"dsLabel" => "Mag Metadata",
			"MIMEType" => "text/xml",
			"formatURI" => "",
			"dsContent" => $dom->saveXML(),
			"checksumType" => "DISABLED",
			"checksum" => "none",
			"logMessage" => "datastream_modified",
			"force" => "true"
		);
		//drupal_set_message($mag);

		$ccks = array ();
		module_load_include('php', 'Fedora_Repository', 'epistemetec/plugins/Node');
		$node = new EpistemetecNode();
		$ccks = $node->hashCCK($form_values);

		try {

			//$soapHelper = new ConnectionHelper();
			//$client = $soapHelper->getSoapClient(variable_get('fedora_soap_manage_url', 'http://localhost:8080/fedora/services/management?wsdl'));
			$object = $soap_client->__soapCall('ModifyDatastreamByValue', array (
				$params
			));

			$node->editNode($ccks);
			return $form_id;

		} catch (exception $e) {
			drupal_set_message(t("Error updating  MAG metadata ") . $e->getMessage(), 'error');

		}
	}
}
?>
