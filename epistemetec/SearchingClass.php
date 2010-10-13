<?php
module_load_include('php', 'Fedora_Repository', 'SearchClass');

class SearchingClass extends SearchClass {

	function quick_search($query,$startPage=1,$xslt) {
    module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
    module_load_include('inc', 'Fedora_Repository', 'api/fedora_utils');

    if (user_access('view fedora collection')) {
    	$numberOfHistPerPage = variable_get('fedora_repository_advanced_block_hist', t('50'));
      
      $luceneQuery = null;
      $indexName = variable_get('fedora_index_name', 'DemoOnLucene');
      $copyXMLFile='copyXml';
    
      $query = trim($query);
      $query=htmlentities(urlencode($query));
      $searchUrl = variable_get('fedora_fgsearch_url', 'http://localhost:8080/fedoragsearch/rest');
      $searchString = '?operation=gfindObjects&indexName=' . $indexName . '&restXslt='.$copyXMLFile.'&query=' . $query;
      $searchString .= '&hitPageSize='.$numberOfHistPerPage.'&hitPageStart='.$startPage;
    
      $searchUrl .= $searchString;

     

      $resultData = do_curl($searchUrl,1);
  

      $output.=$this->applyLuceneXSLT($resultData,$startPage,$xslt,$query);
      return $output;

    }
  }
	
	function build_home_search_form($pathToSearchTerms = null, $query = null) {
		$form = array ();
		$form['pattern'] = array (
			'#size' => '24',
			'#type' => 'textfield',
			'#title' => t(''),
			'#required' => true,
			'#default_value' => ''
		);

		$form['submit'] = array (
			'#type' => 'submit',
			'#value' => t('search')
		);
		return $form;
	}

	function theme_home_search_form($form) {
		$output .= drupal_render($form['pattern']);
		$output .= drupal_render($form['submit']);
		$output .= drupal_render($form);
		return $output;
	}

	function submit_home_search_form($form, & $form_state) {
		//print_r($form_state);exit;
		$path = drupal_get_path('module', 'Fedora_Repository'); 
		$searchTerms = $this->get_search_terms_array($path."/epistemetec/xml");
		$types = array_keys($searchTerms);
		$searchString = '';
		foreach ($types as $type) {
			if ($type != 'dsm.text') {
				$searchString .= $type . ':' . $form_state['values']['pattern'];
				if (next($types))
					$searchString .= '+OR+';
			}
		}
		
		drupal_goto("fedora/repository/epistemetec_search/$searchString");
	}
}
?>