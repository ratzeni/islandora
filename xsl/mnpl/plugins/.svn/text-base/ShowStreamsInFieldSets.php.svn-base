<?php
/*
 * Created on 17-Apr-08
 *
 *
 */
class ShowStreamsInFieldSets{
 	private $pid =null;
 	function ShowStreamsInFieldSets($pid){
 	  $this->pid=$pid;
 	}
 	function showFractionTN() {
 	  global $base_url;
 	  $collection_fieldset = array(
		'#title' => 'Thumbnail Image',
	   '#collapsible' => FALSE,
		'#value' => '<a href="'.$base_url.'/fedora/repository/'.$this->pid.'/FRACTION/"><img src="'.$base_url.'/fedora/repository/'.$this->pid.'/TN/TN'.'" /></a>'
		);
	 return theme('fieldset', $collection_fieldset);

 	}
 	function showFlv(){
 	  //FLV is the datastream id
 	  $path = drupal_get_path('module', 'Fedora_Repository');
 	  $fullPath=base_path().$path;
 	  $content="";
 	  $pathTojs = drupal_get_path('module', 'Fedora_Repository').'/js/swfobject.js';
 	  drupal_add_js("$pathTojs");
 	  $content.='<div id="player'.$this->pid.'FLV"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this player.</div>';
 	  drupal_add_js('var s1 = new SWFObject("'.$fullPath.'/flash/flvplayer.swf","single","320","240","7");
		s1.addParam("allowfullscreen","true");
		s1.addVariable("file","'.base_path().'fedora/repository/'.$this->pid.'/FLV/FLV.flv");
		s1.write("player'.$this->pid.'FLV");','inline','footer');
 	  $collection_fieldset = array(
     	 '#title' => t('Flash Video'),
     	 '#collapsible' => TRUE,
     	 '#collapsed' => FALSE,
      	'#value' => $content);
 	  return theme('fieldset',$collection_fieldset);
 	}

 	function showQdc(){
 	  require_once (drupal_get_path('module', 'Fedora_Repository') . '/ObjectHelper.php');
 	  $objectHelper=new ObjectHelper();
 	  //$returnValue['title']="Description";
 	  $content=$objectHelper->getQDC($this->pid);
 	  $collection_fieldset = array(
     	 '#title' => t('Description'),
     	 '#collapsible' => TRUE,
     	 '#collapsed' => TRUE,
      	'#value' => $content);
 	  return theme('fieldset',$collection_fieldset);
 	}

 	function show_fraction_details( $fraction_pid ) {
 	  module_load_include( 'php', 'fedora_repository', 'ObjectHelper' );
 	  $object_helper = new ObjectHelper();
 	  $fraction_stream = new SimpleXMLElement( $object_helper->getStream( $fraction_pid, 'FRACTION' ) );
    $fields = array( 'weight', 'ptp1b', 'hct116', 'hela', 'pc3', 'are', 'antiproliferative' );
 	  foreach ( $fields as $field ) {
		if (implode($fraction_stream->xpath("//fractions:$field")) == 'Hit'){$td = '<td bgcolor="red">';}
		elseif(implode($fraction_stream->xpath("//fractions:$field")) == 'Strong'){$td = '<td bgcolor = "yellow">';}
		elseif(implode($fraction_stream->xpath("//fractions:$field")) == 'Medium'){$td = '<td bgcolor = "orange">';}
		elseif(implode($fraction_stream->xpath("//fractions:$field")) == 'Low'){$td = '<td bgcolor = "grey">';}
		else{$td = '<td>';}
   	  $row_html .= $td.implode($fraction_stream->xpath("//fractions:$field")).'</td>';
   	}
 	  return $row_html;
 	}

 	function show_compound_details( $compound_pid ) {
    module_load_include( 'php', 'fedora_repository', 'ObjectHelper' );
    $object_helper = new ObjectHelper();
    $compound_stream = new SimpleXMLElement( $object_helper->getStream( $compound_pid, 'COMPOUND' ) );
    $fields = array( 'weight', 'ptp1b', 'hct116', 'hela', 'pc3', 'are', 'antiproliferative' );
    foreach ( $fields as $field ) {
		if (implode($compound_stream->xpath("//compounds:$field")) == 'Hit'){$td = '<td bgcolor="red">';}
		elseif(implode($compound_stream->xpath("//compounds:$field")) == 'Strong'){$td = '<td bgcolor = "yellow">';}
		elseif(implode($compound_stream->xpath("//compounds:$field")) == 'Medium'){$td = '<td bgcolor = "orange">';}
		elseif(implode($compound_stream->xpath("//compounds:$field")) == 'Low'){$td = '<td bgcolor = "grey">';}
		else{$td = '<td>';}
   	  $row_html .= $td.implode($compound_stream->xpath("//compounds:$field")).'</td>';

    }
    return $row_html;
  }
 	
 	
 	function showCritter(){
 	  $dsid = 'CRITTER';
 	  $path=drupal_get_path('module', 'Fedora_Repository');
 	  require_once (drupal_get_path('module', 'Fedora_Repository') . '/ObjectHelper.php');
 	  require_once (drupal_get_path('module', 'Fedora_Repository') . '/CollectionClass.php');
      $objectHelper = new ObjectHelper();
 	  $collectionHelper= new CollectionClass();
 	  $xmlstr=$collectionHelper->getStream($this->pid,"CRITTER");
 	  html_entity_decode($xmlstr);
 	  if($xmlstr==null||strlen($xmlstr)<5){
 	    return " ";
 	  }
 	  try {
 	    $proc = new XsltProcessor();
 	  } catch (Exception $e) {
 	    drupal_set_message(t($e->getMessage()),'error');
 	    return " ";
 	  }
 	  $xsl = new DomDocument();
 	  $xsl->load($path.'/mnpl/xsl/critter.xsl');
 	  $input = new DomDocument();
 	  $input->loadXML(trim($xmlstr));
 	  $xsl = $proc->importStylesheet($xsl);
 	  $newdom = $proc->transformToDoc($input);
 	  $content=$newdom->saveXML();
 	  $pid=$this->pid;
 	  session_start();
 	  $_SESSION['pid'] = $pid;
 	  // set labid as session variable to build identifier in add fraction, etc
 	  $cxl=new SimpleXMLElement($xmlstr);
 	  $labid = implode($cxl->xpath('//critters:lab_id'));
 	  $_SESSION['labid'] = $labid;
 	  $itqlQuery = 'select $object $title  from <#ri> where $object <fedora-model:label> $title  and $object <fedora-rels-ext:isPartOf> <info:fedora/'.$pid.'> and $object <fedora-model:state> <info:fedora/fedora-system:def/model#Active> order by $title';
 	  $relatedItems=$collectionHelper->getRelatedItems($this->pid,$itqlQuery);

 	  $sxe=new SimpleXMLElement($relatedItems);
 	  $nmspace = $sxe->getNamespaces(true);
 	  $regspace = $sxe->registerXPathNamespace('ri',implode($nmspace));

 	  // begin fractions
 	  global $base_url;
    if (user_access('edit fedora meta data')){
 	    $editspec = '<a href ='.$base_url.'/fedora/repository/editmetadata/'.$pid.'/'.$dsid.'>Edit This Specimen</a><br>';
    } else {
      $editspec = '';
    }
    if (user_access('ingest new fedora objects')){ 	  
      $flabel = '<h4>Fractions &mdash; <a href="'.$base_url.'/fedora/ingestObject/vre:mnpl-fractions/MNPL%20Fraction%20Collection/mnpl-fractionCModel">Add Fraction</a></h4>';}
    else {
      $flabel = '<h4>Fractions</h4>';
    }
 	  if(stristr(implode($sxe->xpath('//ri:title')),'fraction')){
      $fraction .= '<table width="100%" cellpadding="2"><tr><td>Identifier</td><td>Weight</td><td>PTP1B</td><td>HCT116</td><td>HELA</td><td>PC3</td><td>ARE</td><td>Antiproliferative</td></tr>';
 	    foreach ($sxe->xpath('//@uri') as $link){
 	      if(strstr($link,'fraction')){
 	        $link =  substr($link,12);
          $fraction .= '<tr>';          
 	        $fraction .= '<td><a href = "'.$base_url.'/fedora/repository/'.$link.'">'.substr($link,18).'</a></td>';
 	        $fraction .= $this->show_fraction_details( $link );
 	        $fraction .= '</tr>';
 	      }
 	    }
 	    $fraction .= '</table>';
 	  } else { 
 	    $fraction = "<div>No Fractions present for this Specimen</div>";
 	  }
 	  // return compounds
 	  $clabel = '<br /><h4>Compounds';

    if (user_access('ingest new fedora objects')){
 	    $clabel .= ' &mdash; <a href="'.$base_url.'/fedora/ingestObject/vre:mnpl-compounds">Add Compound</a></h4>';
    }
 	  if(stristr(implode($sxe->xpath('//ri:title')),'compound')){
 	    // if(in_array('COMPOUND',$sxe->xpath('//ri:contentmod'))){

 	    $compound .= '<table width="100%" cellpadding="2"><tr><td>Identifier</td><td>Weight</td><td>PTP1B</td><td>HCT116</td><td>HELA</td><td>PC3</td><td>ARE</td><td>Antiproliferative</td></tr>';
      foreach ($sxe->xpath('//@uri') as $link){
        if(strstr($link,'compound')){
          $link =  substr($link,12);
          $compound .= '<tr>';          
          $compound .= '<td><a href = "'.$base_url.'/fedora/repository/'.$link.'">'.substr($link,18).'</a></td>';
          $compound .= $this->show_compound_details( $link );
          $compound .= '</tr>';
        }
      }
      $compound .= '</table>';
 	  } else {
 	    $compound = "<div>No Compounds present for this Specimen</div>";
 	  }
    $datastream_list = $objectHelper->get_datastreams_list_asSimpleXML($this->pid);
    $thumbs = '<div><p>';
 	  $thumbs .= '<a href="'.$base_url.'/fedora/repository/'.$pid.'/OBJ" target="_blank"><img src="'.$base_url.'/fedora/imageapi/'.$pid.'/OBJ?op=scale&height=100" /></a>&nbsp;';
         foreach ($datastream_list as $datastream) {
            foreach ($datastream as $datastreamValue) {
                $test=substr($datastreamValue->ID,0,5);                
                if ($test == 'IMAGE' ) {
                   $thumbs .= '<a href="'.$base_url.'/fedora/repository/'.$pid.'/'.$datastreamValue->ID.'" target="_blank"><img src="'.$base_url.'/fedora/imageapi/'.$pid.'/'.$datastreamValue->ID.'?op=scale&height=100" /></a>&nbsp;';
                }
            }
        }
        $thumbs .= '</p></div><br />';
 	  
 	  $collection_fieldset = array(
		     	 '#title' => t('MNPL Critter Record'),
		     	 '#collapsible' => TRUE,
		     	 '#collapsed' => FALSE,
		      	'#value' => $thumbs.$content.$editspec.$flabel.$fraction.$clabel.$compound);

 	  return theme('fieldset',$collection_fieldset);
 	}
 	function showFraction(){
 	  $dsid = 'FRACTION';
 	  $path=drupal_get_path('module', 'Fedora_Repository');
 	  require_once (drupal_get_path('module', 'Fedora_Repository') . '/ObjectHelper.php');
 	  require_once (drupal_get_path('module', 'Fedora_Repository') . '/CollectionClass.php');

 	  $collectionHelper= new CollectionClass();
 	  $xmlstr=$collectionHelper->getStream($this->pid,$dsid);
 	  html_entity_decode($xmlstr);

 	  if($xmlstr==null||strlen($xmlstr)<5){
 	    return " ";
 	  }
 	  try {
 	    $proc = new XsltProcessor();
 	  } catch (Exception $e) {
 	    drupal_set_message(t($e->getMessage()),'error');
 	    return " ";
 	  }
 	  $xsl = new DomDocument();
 	  $xsl->load($path.'/mnpl/xsl/fraction.xsl');
 	  $input = new DomDocument();
 	  $input->loadXML(trim($xmlstr));
 	  $xsl = $proc->importStylesheet($xsl);
 	  $newdom = $proc->transformToDoc($input);
 	  $content=$newdom->saveXML();
 	  // get parent pid and build link
 	  $pid = $this->pid;
 	  $itqlquery = 'select $object from <#ri> where <info:fedora/'.$pid.'> <fedora-rels-ext:isPartOf> $object and $object <fedora-model:state> <info:fedora/fedora-system:def/model#Active>';
   	$relatedItems=$collectionHelper->getRelatedItems($this->pid,$itqlquery);
   	$sxe=new SimpleXMLElement($relatedItems);
   	$nmspace = $sxe->getNamespaces(true);
   	$regspace = $sxe->registerXPathNamespace('ri',implode($nmspace));
   	// begin fractions
   	$flabel = '<h4>Parent Specimen Record for This Fraction</h4><p>';
   	$link = implode($sxe->xpath('//@uri'));
   	$link = substr($link,12);

   	global $base_url;
   	$plink = '<a href ='.$base_url.'/fedora/repository/'.$link.'>Parent Specimen</a><br>';
   	$editfrac = '<a href ='.$base_url.'/fedora/repository/editmetadata/'.$pid.'/'.$dsid.'>Edit This Fraction</a><br>';
   	// display other fractions for this parent
    $itqlQuery = 'select $object $title  from <#ri> where $object <fedora-model:label> $title  and $object <fedora-rels-ext:isPartOf> <info:fedora/'.$link.'> and $object <fedora-model:state> <info:fedora/fedora-system:def/model#Active> order by $title';
    $relatedItems=$collectionHelper->getRelatedItems($this->pid,$itqlQuery);
    $sxe=new SimpleXMLElement($relatedItems);
    $nmspace = $sxe->getNamespaces(true);
    $regspace = $sxe->registerXPathNamespace('ri',implode($nmspace));
    // begin display fractions
    global $base_url;

    $altfrac = '<h4> Other Fractions For Parent Sepcimen</h4><div>';
    if(stristr(implode($sxe->xpath('//ri:title')),'fraction')){
      // if(in_array('FRACTION',$sxe->xpath('//ri:contentmod'))){
      foreach ($sxe->xpath('//@uri') as $link){
        if(strstr($link,'fraction')){
          $pidlink =  substr($link,12);

          if($pidlink !== $pid){
            $fraction .= '<a href = "'.$base_url.'/fedora/repository/'.$pidlink.'">'.substr($link,30).'</a><br>';
          }
        }
      }
    }
    if($fraction == NULL){$fraction = " No other fractions for this parent specimen";}




    $collection_fieldset = array(
		     	 '#title' => t('MNPL Fraction Record'),
		     	 '#collapsible' => TRUE,
		     	 '#collapsed' => FALSE,
		      	'#value' => $content.$editfrac.$flabel.$plink.$altfrac.$fraction);

    return theme('fieldset',$collection_fieldset);
 	}
 	function showCompound(){
 	  $dsid = 'COMPOUND';
 	  $path=drupal_get_path('module', 'Fedora_Repository');
 	  require_once (drupal_get_path('module', 'Fedora_Repository') . '/ObjectHelper.php');
 	  require_once (drupal_get_path('module', 'Fedora_Repository') . '/CollectionClass.php');

 	  $collectionHelper= new CollectionClass();
 	  $xmlstr=$collectionHelper->getStream($this->pid,"COMPOUND");
 	  html_entity_decode($xmlstr);

 	  if($xmlstr==null||strlen($xmlstr)<5){
 	    return " ";
 	  }
 	  try {
 	    $proc = new XsltProcessor();
 	  } catch (Exception $e) {
 	    drupal_set_message(t($e->getMessage()),'error');
 	    return " ";
 	  }
 	  $xsl = new DomDocument();
 	  $xsl->load($path.'/mnpl/xsl/compound.xsl');
 	  $input = new DomDocument();
 	  $input->loadXML(trim($xmlstr));
 	  $xsl = $proc->importStylesheet($xsl);
 	  $newdom = $proc->transformToDoc($input);
 	  $content=$newdom->saveXML();
 	  // get parent pid and build link
 	  $pid = $this->pid;
 	  $itqlquery = 'select $object from <#ri> where <info:fedora/'.$pid.'><fedora-rels-ext:isPartOf> $object ';
 	  $relatedItems=$collectionHelper->getRelatedItems($this->pid,$itqlquery);
 	  $sxe=new SimpleXMLElement($relatedItems);
 	  $nmspace = $sxe->getNamespaces(true);
 	  $regspace = $sxe->registerXPathNamespace('ri',implode($nmspace));
 	  // begin fractions
 	  $flabel = '<h4>Parent Specimen Record for This Compound</h4><p>';
 	  $link = implode($sxe->xpath('//@uri'));

 	  $link = substr($link,12);
 	  global $base_url;
 	  $plink .= '<a href ='.$base_url.'/fedora/repository/'.$link.'>Parent Specimen</a><br>';
if (user_access('edit fedora meta data')){
 	  $editcomp = '<a href ='.$base_url.'/fedora/repository/editmetadata/'.$pid.'/'.$dsid.'>Edit This Compound</a><br>';}
else{$editcomp = '';}

 	  // display other compounds for this parent
 	  $itqlQuery = 'select $object $title  from <#ri> where $object <fedora-model:label> $title  and $object <fedora-rels-ext:isPartOf> <info:fedora/'.$link.'> and $object <fedora-model:state> <info:fedora/fedora-system:def/model#Active> order by $title';
 	  $relatedItems=$collectionHelper->getRelatedItems($this->pid,$itqlQuery);
 	  $sxe=new SimpleXMLElement($relatedItems);
 	  $nmspace = $sxe->getNamespaces(true);
 	  $regspace = $sxe->registerXPathNamespace('ri',implode($nmspace));
 	  // begin display compounds
 	  global $base_url;
 	  $altcomp = '<h4> Other Compounds For Parent Sepcimen</h4><div>';
 	  if(stristr(implode($sxe->xpath('//ri:title')),'compound')){
 	    foreach ($sxe->xpath('//@uri') as $link2){
 	      if(strstr($link2,'compound')){
 	        $pidlink =  substr($link2,12);
 	        if($pidlink != $pid){
 	          $compound .= '<a href = "'.$base_url.'/fedora/repository/'.$pidlink.'">'.substr($link2,30).'</a><br>';
 	        }
 	      }
 	    }
 	  }
 	  if($compound == NULL){$compound = "<div>No other Compounds present for this Specimen</div>";}

 	  $collection_fieldset = array(
		     	 '#title' => t('MNPL Compound Record'),
		     	 '#collapsible' => TRUE,
		     	 '#collapsed' => FALSE,
		      	'#value' => $content.$editcomp.$flabel.$plink.$altcomp.$compound);

 	  return theme('fieldset',$collection_fieldset);
 	}


}
?>
