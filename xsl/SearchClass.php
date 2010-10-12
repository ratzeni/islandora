<?php
class SearchClass {
    public static $ADVANCED_SEARCH_NUMBER_FIELDS = 5;
	function quickSearch($type, $query,$showForm=1,$orderBy=0,& $userArray) {
		module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
                module_load_include('inc', 'Fedora_Repository', 'api/fedora_utils');
		if (user_access('view fedora collection')) {
			$numberOfHistPerPage = '5000';//hack for IR they do not want next button
			$luceneQuery = null;
			//demo search string ?operation=gfindObjects&indexName=DemoOnLucene&query=fgs.DS.first.text%3Achristmas&hitPageStart=11&hitPageSize=10
			$keywords = explode(' ', $query);

			foreach ($keywords as $keyword) {
				$luceneQuery .= $type . ':' . $keyword . '+AND+';
			}
			$luceneQuery = substr($luceneQuery, 0, strlen($luceneQuery) - 5);

			$indexName = variable_get('fedora_index_name', 'DemoOnLucene');
			$keys = htmlentities(urlencode($query));
			$searchUrl = variable_get('fedora_fgsearch_url', 'http://localhost:8080/fedoragsearch/rest');
			$searchString = '?operation=gfindObjects&indexName=' . $indexName . '&restXslt=copyXml&query=' . $luceneQuery;
			$searchString .= '&hitPageSize='.$numberOfHistPerPage.'&hitPageStart=1';
			//$searchString = htmlentities($searchString);
			$searchUrl .= $searchString;

			//$objectHelper = new ObjectHelper();

			$resultData = do_curl($searchUrl,1);
			if(isset($userArray)){
				$doc = new DOMDocument();
				$doc->loadXML($resultData);
				$xPath = new DOMXPath($doc);
				//add users to department list.  This is a hack as not all users will be in dupal
				$nodeList = $xPath->query('//field[@name="refworks.u1"]');
				foreach($nodeList as $node){
					if(!in_array($node->nodeValue,$userArray)){
					$userArray[]=$node->nodeValue;
					}
				}


			}
			if($showForm){
				$output = '<Strong>Quick Search</strong><br /><table class="table-form"><tr>'.drupal_get_form('fedora_repository_quick_search_form').'</tr></table>';
			}
			$output.=$this->applyXSLT($resultData,$orderBy);
			return $output;

		}

	}

    
//gets term from a lucene index and displays them in a list
   function getTerms($fieldName, $startTerm, $displayName=null){
       module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
       module_load_include('inc', 'Fedora_Repository', 'api/fedora_utils');
       $indexName = variable_get('fedora_index_name', 'DemoOnLucene');
       $searchUrl = variable_get('fedora_fgsearch_url', 'http://localhost:8080/fedoragsearch/rest');
       if($startTerm==null){
           $startTerm="";
       }
       $startTerm = drupal_urlencode($startTerm);
	   $query = 'operation=browseIndex&startTerm='.$startTerm.'&fieldName='.$fieldName.'&termPageSize=20&indexName='.$indexName.'&restXslt=copyXml&resultPageXslt=copyXml';
       //$query=drupal_urlencode($query);
       $query = '?'.$query;
       $searchString=$searchUrl.$query;
      
       $objectHelper = new ObjectHelper();
      
       $resultData = do_curl($searchString,1);
       $path = drupal_get_path('module', 'Fedora_Repository');
            
       $output .= $this->applySpecifiedXSLT($resultData,$path.'/xsl/browseIndexToResultPage.xslt',$displayName);
       //$output .= '<br />'.$alpha_out;
       return $output;

       }

   /* function custom_search($query,$pathToXslt=null){
        module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
        module_load_include('inc', 'Fedora_Repository', 'api/fedora_utils');
		if (user_access('view fedora collection')) {
			$numberOfHistPerPage = '1000';//hack for IR they do not want next button
			$luceneQuery = null;
			//demo search string ?operation=gfindObjects&indexName=DemoOnLucene&query=fgs.DS.first.text%3Achristmas&hitPageStart=11&hitPageSize=10


			$indexName = variable_get('fedora_index_name', 'DemoOnLucene');
            $query=htmlentities(urlencode($query));

			$searchUrl = variable_get('fedora_fgsearch_url', 'http://localhost:8080/fedoragsearch/rest');
			$searchString = '?operation=gfindObjects&indexName=' . $indexName . '&restXslt=copyXml&query=' . $query;
			$searchString .= '&hitPageSize='.$numberOfHistPerPage.'&hitPageStart=1';
			//$searchString = htmlentities($searchString);
			$searchUrl .= $searchString;

			$objectHelper = new ObjectHelper();

			$resultData = do_curl($searchUrl,1);
			//var_dump($resultData);exit(0);
			//	$doc = new DOMDocument();
			//	$doc->loadXML($resultData);
      if($pathToXslt==null) {
        $output.=$this->applyLuceneXSLT($resultData,$query);
      }else{
        $output.=$this->applySpecifiedXSLT($resultData,$pathToXslt);
      }
            
			return $output;

		}
    }*/

   function custom_search($query,$startPage=1,$xslt='/xsl/advanced_search_results.xsl',$numberOfHistPerPage=50) {
    module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
    module_load_include('inc', 'Fedora_Repository', 'api/fedora_utils');

    if (user_access('view fedora collection')) {
    	$numberOfHistPerPage = variable_get('fedora_repository_advanced_block_hist', t('50'));
      //$numberOfHistPerPage = '50';//hack for IR they do not want next button
      $luceneQuery = null;
      $indexName = variable_get('fedora_index_name', 'DemoOnLucene');
      $copyXMLFile='copyXml';
     // if($indexName=='ilives' || $indexName=='BasicIndex'){
     //   $copyXMLFile = 'copyXmliLives';
     // }
      $query = trim($query);
      $query=htmlentities(urlencode($query));
      $searchUrl = variable_get('fedora_fgsearch_url', 'http://localhost:8080/fedoragsearch/rest');
      $searchString = '?operation=gfindObjects&indexName=' . $indexName . '&restXslt='.$copyXMLFile.'&query=' . $query;
      $searchString .= '&hitPageSize='.$numberOfHistPerPage.'&hitPageStart='.$startPage;
      //$searchString = htmlentities($searchString);
      $searchUrl .= $searchString;

      //$objectHelper = new ObjectHelper();

      $resultData = do_curl($searchUrl,1);
      //var_dump($resultData);exit(0);
      //	$doc = new DOMDocument();
      //	$doc->loadXML($resultData);

      $output.=$this->applyLuceneXSLT($resultData,$startPage,$xslt,$query);
      return $output;

    }
  }


    function applySpecifiedXSLT($resultData,$pathToXSLT, $displayName=null){
        $proc = null;
        global $user;
		if (!$resultData) {
			drupal_set_message(t('No data Found!'));
			return ' '; //no results
		}

        try {
			$proc = new XsltProcessor();
		} catch (Exception $e) {
			drupal_set_message(t('Error loading '.$pathToXSLT.' xslt! ').$e->getMessage())	;
			return ' ';
		}

        //$proc->setParameter('', 'searchUrl', url('search') . '/fedora_repository'); //needed in our xsl
		$proc->setParameter('', 'objectsPage', base_path());
    $proc->setParameter('', 'userID', $user->uid);
        if(isset($displayName)){
            $proc->setParameter('','displayName',$displayName);
        }else{
            $proc->setParameter('','displayName',"test");
        }
       
        $xsl = new DomDocument();

		$test= $xsl->load($pathToXSLT);
       
		if (!isset($test)) {
			drupal_set_message(t('Error loading '.$pathToXSLT.' xslt!'));
			return t('Error loading '.$pathToXSLT.' xslt!');
		}

		$input = new DomDocument();
       
		$didLoadOk = $input->loadXML($resultData);

		if (!isset($didLoadOk)) {
			drupal_set_message(t('Error loading XML Data!'));
			return t('Error loading XML Data. ');


		} else {

			 $proc->importStylesheet($xsl);

			$newdom = $proc->transformToDoc($input);
             
             
			return $newdom->saveXML();


		}




    }
//default function for lucene results
/*    function applyLuceneXSLT($resultData,$query=NULL){
        $path = drupal_get_path('module', 'Fedora_Repository');
		$proc = null;
		if (!$resultData) {
			drupal_set_message(t('No Results!'));
			return ' '; //no results
		}


		try {
			$proc = new XsltProcessor();
		} catch (Exception $e) {
			drupal_set_message(t('Error loading results xslt! ').$e->getMessage())	;
			return ' ';
		}

		//inject into xsl stylesheet
    if(isset($query)){
      $proc->setParameter('', 'fullQuery',$query);
    }
		$proc->setParameter('', 'searchToken', drupal_get_token('fedora_repository_advanced_search')); //token generated by Drupal, keeps tack of what tab etc we are on
		$proc->setParameter('', 'searchUrl', url('search') . '/fedora_repository'); //needed in our xsl
		$proc->setParameter('', 'objectsPage', base_path());
		$proc->setParameter('', 'allowedPidNameSpaces', variable_get('fedora_pids_allowed', 'demo: changeme:'));
		$proc->registerPHPFunctions();
		$xsl = new DomDocument();

		$test= $xsl->load($path . '/xsl/results.xsl');
		if (!isset($test)) {
			drupal_set_message(t('Error loading search results xslt!'));
			return t('Error loading search results xslt! ');
		}

		$input = new DomDocument();
		$didLoadOk = $input->loadXML($resultData);
        
		if (!isset($didLoadOk)) {
			drupal_set_message(t('Error loading search results!'));
			return t('Error loading search results! ');


		} else {
           
			 $proc->importStylesheet($xsl);
            
			$newdom = $proc->transformToDoc($input);
            
			return $newdom->saveXML();
          

		}
        


    }*/
     /**
   * apply an xslt to lucene gsearch search results
   *
   * @param <type> $resultData
   * @param <type> $startPage
   * @param <type> $xslt_file
   * @param <type> $query the query that was executed.  May want to pass this on.
   */
    function applyLuceneXSLT($resultData, $startPage = 1, $xslt_file='/xsl/results.xsl',$query=null) {
    $path = drupal_get_path('module', 'Fedora_Repository');
    $proc = null;
    if (!$resultData) {
      //drupal_set_message(t('No Results!'));
      return ' '; //no results
    }


    try {
      $proc = new XsltProcessor();
    } catch (Exception $e) {
      drupal_set_message(t('Error loading results xslt! ').$e->getMessage())	;
      return ' ';
    }
    if(isset($query)){
      $proc->setParameter('', 'fullQuery', $query);

    }
    //inject into xsl stylesheet
    global $user;
    $proc->setParameter('', 'userID', $user->uid);
    $proc->setParameter('', 'searchToken', drupal_get_token('fedora_repository_advanced_search')); //token generated by Drupal, keeps tack of what tab etc we are on
    $proc->setParameter('', 'searchUrl', url('search') . '/fedora_repository'); //needed in our xsl
    $proc->setParameter('', 'objectsPage', base_path());
    $proc->setParameter('', 'allowedPidNameSpaces', variable_get('fedora_pids_allowed', 'demo: changeme:'));
    $proc->setParameter('', 'hitPageStart',$startPage);
    $proc->registerPHPFunctions();
    $xsl = new DomDocument();

    $test= $xsl->load($path . $xslt_file);
    if (!isset($test)) {
      drupal_set_message(t('Error loading search results xslt!'));
      return t('Error loading search results xslt! ');
    }

    $input = new DomDocument();
    $didLoadOk = $input->loadXML($resultData);

    if (!isset($didLoadOk)) {
      drupal_set_message(t('Error loading search results!'));
      return t('Error loading search results! ');


    } else {

      $proc->importStylesheet($xsl);

      $newdom = $proc->transformToDoc($input);

      return $newdom->saveXML();


    }



  }

//xslt for islandscholar these xslt functions can probably be pulled into one
	function applyXSLT($resultData,$orderBy=0) {

		$path = drupal_get_path('module', 'Fedora_Repository');
		$proc = null;
		if (!$resultData) {
			//drupal_set_message(t('No Results!'));
			return ' '; //no results
		}


		try {
			$proc = new XsltProcessor();
		} catch (Exception $e) {
			drupal_set_message(t('Error loading results xslt! ').$e->getMessage())	;
			return ' ';
		}

		//inject into xsl stylesheet
		//$proc->setParameter('', 'searchToken', drupal_get_token('search_form')); //token generated by Drupal, keeps tack of what tab etc we are on
		$proc->setParameter('', 'userID', $user->uid);
    $proc->setParameter('', 'searchUrl', url('search') . '/fedora_repository'); //needed in our xsl
		$proc->setParameter('', 'objectsPage', base_path());
		$proc->setParameter('', 'allowedPidNameSpaces', variable_get('fedora_pids_allowed', 'demo: changeme:'));
		$proc->setParameter('', 'orderBy', $orderBy);
		$xsl = new DomDocument();

		$test=$xsl->load($path . '/ir/xsl/results.xsl');
		if (!isset($test)) {
			drupal_set_message(t('Error loading search results xslt!'));
			return t('Error loading search results xslt! ');
		}

		$input = new DomDocument();
		$didLoadOk = $input->loadXML($resultData);


		if (!isset($didLoadOk)) {
			drupal_set_message(t('Error loading search results!'));
			return t('Error loading search results! ');


		} else {

			$xsl = $proc->importStylesheet($xsl);

			$newdom = $proc->transformToDoc($input);

			return $newdom->saveXML();

		}


	}

function theme_advanced_search_form($form, $repeat=null) {
    if(!isset($repeat)){
		$repeat = variable_get('fedora_repository_advanced_block_repeat',  t('3'));
    }
       
		$output .= drupal_render($form['search_type']['type1']) ;
		$output .= drupal_render($form['fedora_terms1']) ;
		$output .=  drupal_render($form['andor1'])  . drupal_render($form['search_type']['type2']) ;
		$output .= drupal_render($form['fedora_terms2']);
          if($repeat>2 && $repeat < 9){
        for($i=3;$i<$repeat+1;$i++){
            $t=$i-1;
            $output .=  drupal_render($form["andor$t"])  . drupal_render($form['search_type']["type$i"]) ;
            $output .= drupal_render($form["fedora_terms$i"]) ;
        }

        }
		
		$output .=  drupal_render($form['submit']) ;
		$output .= drupal_render($form);
		return $output;
	}

    //build search form, custom blocks should set the number of repeats or it will use the default
function build_advanced_search_form($repeat=null,$pathToSearchTerms=null,$query=null){  
    $types = $this->get_search_terms_array($pathToSearchTerms);
    $queryArray=null;
    if(isset($query)){
       $queryArray = explode('AND', $query);
    }
    
		$andOrArray = array (
			'AND' => 'and',
			//'OR' => 'or' //removed or for now as it would be a pain to parse
		);
		$form = array ();
        if(!isset($repeat)){
            $repeat = variable_get('fedora_repository_advanced_block_repeat',  t('3'));
        }
     $var0=explode(':',$queryArray[0]);
     $var1=explode(':',$queryArray[1]);
		$form['search_type']['type1'] = array (
			'#title' => t(''
		), '#type' => 'select', '#options' => $types,'#default_value' => trim($var0[0]) );
		$form['fedora_terms1'] = array (
             '#size' =>'24',
			'#type' => 'textfield',
			'#title' => t(''
		), '#required' => true ,'#default_value' => trim($var0[1]));
		$form['andor1'] = array (
			'#title' => t(''
		), '#type' => 'select', '#default_value' =>  'AND'  , '#options' => $andOrArray);
		$form['search_type']['type2'] = array (           
			'#title' => t(''
		), '#type' => 'select', '#options' => $types,'#default_value' => trim($var1[0]));
		$form['fedora_terms2'] = array (
             '#size' =>'24',
			'#type' => 'textfield',
			'#title' => t(''
		),'#default_value' => $var1[1]);
    if($repeat>2 && $repeat < 9){ //don't want less then 2 or more then 9
        for($i=3;$i<$repeat+1;$i++){
            $t=$i-1;
            $field_and_term=explode(':',$queryArray[$t]);
            $form["andor$t"] = array (
			'#title' => t(''
		), '#type' => 'select', '#default_value' => 'AND', '#options' => $andOrArray);
		$form['search_type']["type$i"] = array (
			'#title' => t(''
		), '#type' => 'select', '#options' => $types,'#default_value' => trim($field_and_term[0]));
		$form["fedora_terms$i"] = array (
             '#size' =>'24',
			'#type' => 'textfield',
			'#title' => t(''
		),'#default_value' => trim($field_and_term[1]));
        }
    }
  
		$form['submit'] = array (
			'#type' => 'submit',
			'#value' => t('search'
		));
		return $form;
}


function get_search_terms_array($path = null){

    if(!isset($path)){
        $path = drupal_get_path('module', 'Fedora_Repository');
    }
     $xmlDoc = new DomDocument();
            $xmlDoc->load($path . '/searchTerms.xml');
            $nodeList = $xmlDoc->getElementsByTagName('term');
            $types = array ();
            for ($i = 0; $i < $nodeList->length; $i++) {
              $field = $nodeList->item($i)->getElementsByTagName('field');
              $value = $nodeList->item($i)->getElementsByTagName('value');
              $fieldValue = $field->item(0)->nodeValue;
              $valueValue = $value->item(0)->nodeValue;
              $types["$fieldValue"] = "$valueValue";


            }

            return $types;
           
}
}
?>
