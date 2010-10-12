<?php
module_load_include('php', 'Fedora_Repository', 'FormBuilder');
/*
 * Created on Feb 1, 2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class ObjectEpistemetecHelper extends ObjectHelper {

  function ObjectEpistemetecHelper( ) {
    drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
    module_load_include('php', 'Fedora_Repository', 'ConnectionHelper');
    $connectionHelper = new ConnectionHelper();
  //$this->fedoraUser = $connectionHelper->getUser();
  //$this->fedoraPass = $connectionHelper->getPassword();
  }

//function getImageData($pid) {
//
//        global $base_url;
//        $path = drupal_get_path('module', 'Fedora_Repository');
//        module_load_include('php', 'Fedora_Repository', 'ConnectionHelper');
//
//        $soapHelper = new ConnectionHelper();
//        $client = $soapHelper->getSoapClient(variable_get('fedora_soap_url', 'http://localhost:8080/fedora/services/access?wsdl'));
//
//        $dsId = 'MAG';
//        $params = array (
//            'pid' => "$pid",
//            'dsID' => "$dsId",
//            'asOfDateTime' => ""
//        );
//        try {
//            $object = $client->__soapCAll('getDatastreamDissemination', array (
//                'parameters' => $params
//                ));
//        } catch (Exception $e2) {
//            try { //probably no QDC so we will try for the DC stream.
//                $dsId = 'MAG';
//                $params = array (
//                    'pid' => "$pid",
//                    'dsID' => "$dsId",
//                    'asOfDateTime' => ""
//                );
//                $object = $client->__soapCAll('getDatastreamDissemination', array (
//                    'parameters' => $params
//                    ));
//            } catch (exception $e2) {
//                drupal_set_message($e2->getMessage(), 'error');
//                return;
//            }
//        }
//        $xmlstr = $object->dissemination->stream;
//        try {
//            $proc = new XsltProcessor();
//        } catch (Exception $e) {
//            drupal_set_message($e->getMessage(), 'error');
//            return;
//        }
//
//        $proc->setParameter('', 'baseUrl', $base_url);
//        $proc->setParameter('', 'path', $base_url . '/' . $path);
//        $input = null;
//        $xsl = new DomDocument();
//        try {
//            $xsl->load($path . '/xsl/imageData.xsl');
//            $input = new DomDocument();
//            $input->loadXML(trim($xmlstr));
//        } catch (exception $e) {
//            watchdog("Fedora_Repository", "Problem loading xsl file!");
//
//        }
//        $xsl = $proc->importStylesheet($xsl);
//        $newdom = $proc->transformToDoc($input);
//        $output = $newdom->saveXML();
//        $baseUrl = base_path();
//        $baseUrl=substr($baseUrl, 0, (strpos($baseUrl, "/")-1));
//        if (user_access(ObjectHelper :: $EDIT_FEDORA_METADATA)) {
//            $output .= '<br /><a title = "' . t('Edit Meta Data') . '" href="' . $base_url . '/fedora/repository/' . 'editmetadata/' . $pid . '/' . $dsId . '"><img src="' . $base_url . '/' . $path . '/images/edit.gif" alt="' . t('Edit Meta Data') . '" /></a>';
//        }
//        return $output;
//    }
    
    function getMagData($pid) {

        global $base_url;
        $path = drupal_get_path('module', 'Fedora_Repository');
        module_load_include('php', 'Fedora_Repository', 'ConnectionHelper');

        $soapHelper = new ConnectionHelper();
        $client = $soapHelper->getSoapClient(variable_get('fedora_soap_url', 'http://localhost:8080/fedora/services/access?wsdl'));

        $dsId = 'MAG';
        $params = array (
            'pid' => "$pid",
            'dsID' => "$dsId",
            'asOfDateTime' => ""
        );
        try {
            $object = $client->__soapCAll('getDatastreamDissemination', array (
                'parameters' => $params
                ));
        } catch (Exception $e2) {
            try { //probably no QDC so we will try for the DC stream.
                $dsId = 'MAG';
                $params = array (
                    'pid' => "$pid",
                    'dsID' => "$dsId",
                    'asOfDateTime' => ""
                );
                $object = $client->__soapCAll('getDatastreamDissemination', array (
                    'parameters' => $params
                    ));
            } catch (exception $e2) {
                drupal_set_message($e2->getMessage(), 'error');
                return;
            }
        }
        $xmlstr = $object->dissemination->stream;
        try {
            $proc = new XsltProcessor();
        } catch (Exception $e) {
            drupal_set_message($e->getMessage(), 'error');
            return;
        }

        $proc->setParameter('', 'baseUrl', $base_url);
        $proc->setParameter('', 'path', $base_url . '/' . $path);
        $input = null;
        $xsl = new DomDocument();
        try {
            $xsl->load($path . '/epistemetec/xsl/magData.xsl');
            $input = new DomDocument();
            $input->loadXML(trim($xmlstr));
        } catch (exception $e) {
            watchdog("Fedora_Repository", "Problem loading xsl file!");

        }
        $xsl = $proc->importStylesheet($xsl);
        $newdom = $proc->transformToDoc($input);
        $output = $newdom->saveXML();
        $baseUrl = base_path();
        $baseUrl=substr($baseUrl, 0, (strpos($baseUrl, "/")-1));
        if (user_access(ObjectHelper :: $EDIT_FEDORA_METADATA)) {
            $output .= '<br /><a title = "' . t('Edit Meta Data') . '" href="' . $base_url . '/fedora/repository/' . 'editmetadata/' . $pid . '/' . $dsId . '"><img src="' . $base_url . '/' . $path . '/images/edit.gif" alt="' . t('Edit Meta Data') . '" /></a>';
        }
        return $output;
    }
    
    function getEpistemetecData($pid) {

        global $base_url;
        $path = drupal_get_path('module', 'Fedora_Repository');
        module_load_include('php', 'Fedora_Repository', 'ConnectionHelper');

        $soapHelper = new ConnectionHelper();
        $client = $soapHelper->getSoapClient(variable_get('fedora_soap_url', 'http://localhost:8080/fedora/services/access?wsdl'));

        $dsId = 'MAG';
        $params = array (
            'pid' => "$pid",
            'dsID' => "$dsId",
            'asOfDateTime' => ""
        );
        try {
            $object = $client->__soapCAll('getDatastreamDissemination', array (
                'parameters' => $params
                ));
        } catch (Exception $e2) {
            try { //probably no QDC so we will try for the DC stream.
                $dsId = 'MAG';
                $params = array (
                    'pid' => "$pid",
                    'dsID' => "$dsId",
                    'asOfDateTime' => ""
                );
                $object = $client->__soapCAll('getDatastreamDissemination', array (
                    'parameters' => $params
                    ));
            } catch (exception $e2) {
                drupal_set_message($e2->getMessage(), 'error');
                return;
            }
        }
        $xmlstr = $object->dissemination->stream;
        try {
            $proc = new XsltProcessor();
        } catch (Exception $e) {
            drupal_set_message($e->getMessage(), 'error');
            return;
        }

        $proc->setParameter('', 'baseUrl', $base_url);
        $proc->setParameter('', 'path', $base_url . '/' . $path);
        $input = null;
        $xsl = new DomDocument();
        try {
            $xsl->load($path . '/epistemetec/xsl/epistemetecData.xsl');
            $input = new DomDocument();
            $input->loadXML(trim($xmlstr));
        } catch (exception $e) {
            watchdog("Fedora_Repository", "Problem loading xsl file!");

        }
        $xsl = $proc->importStylesheet($xsl);
        $newdom = $proc->transformToDoc($input);
        $output = $newdom->saveXML();
        $baseUrl = base_path();
        $baseUrl=substr($baseUrl, 0, (strpos($baseUrl, "/")-1));
//        if (user_access(ObjectHelper :: $EDIT_FEDORA_METADATA)) {
//            $output .= '<br /><a title = "' . t('Edit Meta Data') . '" href="' . $base_url . '/fedora/repository/' . 'editmetadata/' . $pid . '/' . $dsId . '"><img src="' . $base_url . '/' . $path . '/images/edit.gif" alt="' . t('Edit Meta Data') . '" /></a>';
//        }
        return $output;
    }
  
}
?>