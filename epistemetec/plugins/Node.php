<?php
class EpistemetecNode {

	function EpistemetecNode() {
		define('ITEM_DL', 'fedora_obj');
		define('COLLECTION_DL', 'fedora_collectiondl');
		define('IMAGE_DL', 'img_');
		define('AUDIO_DL', 'aud_');
		define('VIDEO_DL', 'vid_');
		drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
	}

	private function getNid($pid) {
		module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
		//get Nid from Pid
		$object = new ObjectHelper($pid);
		$spec = $object->getStream($pid, 'MAG',0);
		
		$xml = new SimpleXMLElement($spec);
		$nid = implode($xml->xpath('//nid'));
//		$urlNid = implode($xml->xpath('//nurl'));
//		$strNid = explode('node/', $urlNid);
//		$nid = $strNid[1];

		return $nid;
	}

	function hashCCK($form_values, $type = null, $isEditing = FALSE) {
		
		//module_load_include('php', 'Epistemetec', 'config/config.php');
		if (!is_null($type) && $type == COLLECTION_DL) {
				$hash_cck['field_fedora_pid'] = 'pid';
				if (! $isEditing) $hash_cck['field_fedora_title'] = 'dc:title';
				else $hash_cck['field_fedora_title'] = 'dc:title-0';
				$hash_cck['field_fedora_code'] = 'pid';
		} else {
			$hash_cck['field_fedora_title'] = 'bib_title';
			$hash_cck['field_fedora_pid'] = 'pid';
			$hash_cck['field_fedora_code'] = 'pid';
			$hash_cck['field_fedora_resource'] = 'bib_subject';
			$hash_cck['field_fedora_collection_pid'] = 'collection_pid';
			if (!is_null($type))
				$hash_cck['field_fedora_file'] = $type . 'file';
			$hash_cck['field_fedora_distro'] = 'bib_rights';
		
		}

		$ccks = array ();
		foreach ($hash_cck as $key => $value)
			$ccks[$key] = $form_values[$value];

		return $ccks;
	}

	function createNode($ccks, $type) {
		//echo "<pre>";print_r($ccks);echo "</pre>";exit;
		global $base_url;
		$nodeUrl = $base_url . '/fedora/repository/' . $ccks['field_fedora_pid'];
		// add node properties
		$newNode = (object) NULL;
		$newNode->type = $type;
		$newNode->title = $ccks['field_fedora_title'];
		$newNode->uid = 0;
		$newNode->created = strtotime("now");
		$newNode->changed = strtotime("now");
		$newNode->status = 1;
		$newNode->comment = 0;
		$newNode->promote = 0;
		$newNode->moderate = 0;
		$newNode->sticky = 0;
		$newNode->body = "<a href=\"" . $nodeUrl . "\">" . $ccks['field_fedora_title'] . "</a>";
		// add CCK field data
		foreach ($ccks as $key => $value)
			eval ("\$newNode->" .
			$key . "[0]['value']=\"$value\";");
		// save node
		
		node_save($newNode);
		$nid = trim($newNode->nid);
		return $nid;
	}

	function editNode($ccks) {
		global $base_url;
		$nodeUrl = $base_url . '/fedora/repository/' . $ccks['field_fedora_pid'];
		$pid = $ccks['field_fedora_pid'];
		$nid = $this->getNid($pid);

		//load and edit 
		$node = node_load($nid);
		foreach ($ccks as $key => $value)
			eval ("\$node->" .
			$key . "[0]['value']=\"$value\";");
		$node->title = $ccks['field_fedora_title'];
		$node->body = "<a href=\"" . $nodeUrl . "\">" . $ccks['field_fedora_title'] . "</a>";
		node_save($node);
	}
	
	function addNodeReference($ccks) {
		//print_r($ccks);exit;
		$pidParent = $ccks['field_fedora_collection_pid'];
		$nidParent = $this->getNid($pidParent);
		$pidChild = $ccks['field_fedora_pid'];
		//echo $pidChild; exit;
		$nidChild = $this->getNid($pidChild);
		
		//echo $nidChild; exit;
		//load and edit 
		try {
		
		$node = node_load($nidParent);
		$node->field_fedora_reference[sizeof($node->field_fedora_reference)] = array('nid'=>$nidChild);
		node_save($node);
		}
		
		catch (exception $e) {
			//node_delete($nid);
			drupal_set_message(t('Error Ingesting Object! ') . $e->getMessage(), 'error');
			watchdog(t("Fedora_Repository"), t("Error Ingesting Object!") . $e->getMessage(), NULL, WATCHDOG_ERROR);
			return;
		}
		//echo "<pre>"; var_dump($node);echo "</pre>";exit;
	}

	function deleteNode($pid) {
		$nid = $this->getNid($pid);
		node_delete($nid);
	}

}
?>
