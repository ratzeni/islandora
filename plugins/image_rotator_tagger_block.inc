<?php

// $Id$

/* 
 * @file image_rotator_tagger_block.inc
 */


function get_random_image() {
  module_load_include('php', 'Fedora_Repository', 'CollectionClass');
  module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
  $itqlquery = 'select $object $title from <#ri>
where $object <info:fedora/fedora-system:def/relations-external#isMemberOfCollection> <info:fedora/ilives:figures>
and $object <fedora-model:state> <info:fedora/fedora-system:def/model#Active>
and $object <dc:title> $title';
  $collection = new CollectionClass('ilives:figures');
  $results = $collection->getRelatedItems('ilives:figures', $itqlquery);
  //drupal_set_message($results);

  
  
  $resultsdoc = new DomDocument();
  $resultsdoc->loadXML($results);
  
  $resultslist = $resultsdoc->getElementsByTagName('result');
  //return htmlentities(substr($results, 0, 1000));
  $randresult = $resultslist->item( rand()%$resultslist->length );
  if (empty($randresult)) {
    return null;
  }
  $objs = $randresult->getElementsByTagName('object');
  $obj = $objs->item(0);
  $pid = substr( $obj->getAttribute('uri'), 12);
  $titles = $randresult->getElementsByTagName('title');
  $title = $titles->item(0);

  return array('pid' => $pid,
               //'title' => $randresult->textContent);
               'title' => $title->nodeValue);

}


function _fedora_image_rotator_tagger_block_content() {
  module_load_include('php', 'Fedora_Repository', 'api/fedora_item');
  module_load_include('inc', 'fedora_repository', 'plugins/tagging_form');

  global $base_url;
  if (!empty($_POST['pid'])) {
    $image_item = new Fedora_Item($_POST['pid']);
    $result['pid'] = $_POST['pid'];
    $result['title'] = $image_item->object_profile->objLabel;
  }
  elseif (empty($_POST) && !empty($_SESSION['fedora_tagged_image']['pid'])) {
    $image_item = new Fedora_Item($_SESSION['fedora_tagged_image']['pid']);
    $result['pid'] = $_SESSION['fedora_tagged_image']['pid'];
    $result['title'] = $image_item->object_profile->objLabel;
    unset($_SESSION['fedora_tagged_image']);
  }
  else {
    $result = get_random_image();
    $image_item = new Fedora_Item($result['pid']);
  }

  return '<p>'
    . '<img src="' . $base_url . '/fedora/imageapi/' . $result['pid'] .  '/JPG?op=scale&height=500&width=300" /><p>This image is from <a href="'
    . $base_url . '/fedora/repository/' . $image_item->pid . '">' . $result['title'] . '</a>'
    . drupal_get_form('fedora_repository_image_tagging_form', $result['pid']).'</p>';
}


/*
function fedora_ilives_preprocess_page(&$variables) {
  drupal_add_js(drupal_get_path('module', 'fedora_ilives').'/fedora_ilives_ajax.js', 'theme');
  return TRUE;
}*/

class ShowILivesStreamsInFieldSets {

  private $pid =NULL;

  function ShowILivesStreamsInFieldSets($pid) {
    //drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
    $this->pid=$pid;
  }
  
  function showJPG() {
    module_load_include('inc', 'fedora_repository', 'plugins/tagging_form');
    global $base_url;
    $collection_fieldset = array(
      '#collapsible' => FALSE,
      '#value' => '<a href="' . $base_url . '/fedora/repository/' . $this->pid . '/JPG/"><img src="' . $base_url . '/fedora/repository/' . $this->pid . '/JPG/JPG.jpg' . '" /></a>',
    );
    return theme('fieldset', $collection_fieldset)
      . drupal_get_form('fedora_repository_image_tagging_form', $this->pid);
  } 
}