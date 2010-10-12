<?php
module_load_include('php', 'Fedora_Repository', 'formClass');

class FormingClass extends formClass {
	 /*
   * create the paths for urls and map them to php functions
   */
  function createEpistemetecMenu(){
    $items = array ();

  
   
    $items['fedora/repository/epistemetec_search'] = array (
          'title' => t('Repository Epistemetec Search'),
           'page callback' => 'epistemetec_home_search',
            'type'=> MENU_CALLBACK,
            'access arguments' => array('view fedora collection')
    );

   
    return $items;
  }
}
?>