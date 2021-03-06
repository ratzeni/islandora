<?php


/*
 * Created on 22-Oct-08
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

class SecurityClass {
  public static $SECURITYSTREAM = 'CHILD_SECURITY';
  function SecurityClass() {
    module_load_include('nc', 'SecurityClass', '');
    drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
  }

  function canIngestHere($collection_pid) {
    global $user;
    module_load_include('php', 'Fedora_Repository', 'ObjectHelper');
    $objectHelper = new ObjectHelper();
    //get the childsecurity policy from the collection.
    $policyStream = $objectHelper->getStream($collection_pid, SECURITYCLASS :: $SECURITYSTREAM, false);

    if ($policyStream == null) {
    //no child policy stream so collection is wide open to anyone to ingest, that has the permission ingest in Drupal.
    //maybe we should return false here?? would be more secure.
      return true;
    }
    $allowedUsersAndRoles = $this->getAllowedUsersAndRoles($policyStream);
    if (!$allowedUsersAndRoles) {
    //error processing stream so don't let them ingest here.
      return false;
    }
    $allowedUsers = $allowedUsersAndRoles["users"];
    $allowedRoles = $allowedUsersAndRoles["roles"];

    foreach ($user->roles as $role) {
      if (in_array($role, $allowedRoles)) {
        return true;
      }
    }

    if (in_array($user->name, $allowedUsers)) {
      return true;
    }
    return false;
  }

  //parses our simple xacml policies checking for users or roles that are allowed to ingest
  function getAllowedUsersAndRoles($policyStream) {
    $allowedRoles = array ();
    $allowedUsers = array ();
    $usersAndRoles = array ();
    try {
      $xml = new SimpleXMLElement($policyStream);
    } catch (Exception $e) {
      watchdog(t("Fedora_Repository"), t("No roles found in Security Policy, could not parse policy stream!"),NULL, WATCHDOG_ERROR);
      //we may not want to send this to the screen.
      drupal_set_message(t('No roles found in Security Policy, could not parse policy stream! ') . $e->getMessage(), 'error');
      return NULL;
    }
    $xml->registerXPathNamespace('default', 'urn:oasis:names:tc:xacml:1.0:policy');

    $conditions = $xml->xpath("//default:Condition");

    foreach ($conditions as $condition) {
      $designator = $condition->Apply->SubjectAttributeDesignator;
      if(empty($designator)) {//$disignator may be wrapped by an or
        $designator=$condition->Apply->Apply->SubjectAttributeDesignator;
      }
      $attributeId = strip_tags($designator['AttributeId']);

      if ($attributeId == "fedoraRole") {
        foreach ($condition->Apply->Apply->AttributeValue as $attributeValue) {
          $allowedRoles[] = strip_tags($attributeValue->asXML());
        }
        foreach ($condition->Apply->Apply->Apply->AttributeValue as $attributeValue) {
          $allowedRoles[] = strip_tags($attributeValue->asXML());
        }
      }
      if ($attributeId == "urn:fedora:names:fedora:2.1:subject:loginId") {
        foreach ($condition->Apply->Apply->AttributeValue as $attributeValue) {
          $allowedUsers[] = strip_tags($attributeValue->asXML());
        }
        foreach ($condition->Apply->Apply->Apply->AttributeValue as $attributeValue) {
          $allowedUsers[] = strip_tags($attributeValue->asXML());
        }
      }
    }
    $usersAndRoles['users'] = $allowedUsers;
    $usersAndRoles['roles'] = $allowedRoles;
    return $usersAndRoles;

  }
  //When a user's profile is saved in drupal we will attempt to create a collection for them in Fedora
  //this will be their personal space.  In the IR it is editable by users with the same role in the VRE
  //it probably would not be.
  function createPersonalPolicy($user) {
    $doc = new DOMDocument();
    try {
      $doc->load(drupal_get_path('module', 'Fedora_Repository') . '/policies/noObjectEditPolicy.xml');
    }catch(exception $e ){
      watchdog(t("Fedora_Repository"), t("Problem loading Policy file."),NULL,WATCHDOG_ERROR);

    }
    $conditions = $doc->getElementsByTagName('Condition');
    foreach ($conditions as $condition) {
      $designator = $condition->getElementsByTagName('SubjectAttributeDesignator');
      foreach ($designator as $des) {
        $attributeId = $des->getAttribute('AttributeId');
        if ($attributeId == 'fedoraRole') {
          $applies = $condition->getElementsByTagName('Apply');
          foreach ($applies as $apply) {
            $functionId = $apply->getAttribute('FunctionId');
            if ($functionId == 'urn:oasis:names:tc:xacml:1.0:function:string-bag') {
              foreach ($user->roles as $role) {
                if (!($role == 'authenticated user' || $role == 'administrator')) { //don't want authenticated user included administrator already is included'
                  $newAttributeValue=$doc->createElement('AttributeValue','<![CDATA['.$role.']]>');

                  $newAttributeValue->setAttribute('DataType', 'http://www.w3.org/2001/XMLSchema#string');
                  //$newAttributeValue->setAttribute('MustBePresent', 'false');
                  $apply->appendChild($newAttributeValue);
                }
              }
            }
          }

        }

        if ($attributeId == 'urn:fedora:names:fedora:2.1:subject:loginId') {
          $applies = $condition->getElementsByTagName('Apply');
          foreach ($applies as $apply) {
            $functionId = $apply->getAttribute('FunctionId');
            if ($functionId == 'urn:oasis:names:tc:xacml:1.0:function:string-bag') {
              $newAttributeValue=$doc->createElement('AttributeValue',$user->name);
              $newAttributeValue->setAttribute('DataType', 'http://www.w3.org/2001/XMLSchema#string');
              //$newAttributeValue->setAttribute('MustBePresent', 'false');
              $apply->appendChild($newAttributeValue);


            }
          }

        }

      }

    }

    return $doc;//null; //$xml;

  }

 /**
   * Add a list of allowed users and roles to the given policy stream and return it.
   *
   * @param string $policy_stream
   * @param array $users_and_roles
   * @return DOMDocument
   */
  function set_allowed_users_and_roles( &$policy_stream, $users_and_roles ) {
    $allowed_roles = $users_and_roles['roles'];
    $allowed_users = $users_and_roles['users'];
    $dom = new DOMDocument();
    $dom->loadXML( $policy_stream );
    $conditions = $dom->getElementsByTagName('Condition');
    foreach ($conditions as $condition) {
      $designator = $condition->getElementsByTagName('SubjectAttributeDesignator');
      foreach ($designator as $des) {
        $attributeId = $des->getAttribute('AttributeId');
        if ($attributeId == 'fedoraRole') {
          //$applies = $condition->getElementsByTagName('Apply');
          $applies = $des->parentNode->getElementsByTagName('Apply');
          foreach ($applies as $apply) {
            $functionId = $apply->getAttribute('FunctionId');
            if ($functionId == 'urn:oasis:names:tc:xacml:1.0:function:string-bag') {
              foreach ( $allowed_roles as $role ) {
                if (!($role == 'authenticated user' || $role == 'administrator')) { //don't want authenticated user included administrator already is included'
                  $newAttributeValue=$dom->createElement('AttributeValue',$role);
                  $newAttributeValue->setAttribute('DataType', 'http://www.w3.org/2001/XMLSchema#string');
                  //$newAttributeValue->setAttribute('MustBePresent', 'false');
                  $apply->appendChild($newAttributeValue);
                }
              }
            }
          }
        }

        if ($attributeId == 'urn:fedora:names:fedora:2.1:subject:loginId') {
          //$applies = $condition->getElementsByTagName('Apply');
          $applies = $des->parentNode->getElementsByTagName('Apply');
          foreach ($applies as $apply) {
            $functionId = $apply->getAttribute('FunctionId');
            if ($functionId == 'urn:oasis:names:tc:xacml:1.0:function:string-bag') {
              foreach ( $allowed_users as $username ) {
                $newAttributeValue=$dom->createElement('AttributeValue',$username );
                $newAttributeValue->setAttribute('DataType', 'http://www.w3.org/2001/XMLSchema#string');
                //$newAttributeValue->setAttribute('MustBePresent', 'false');
                $apply->appendChild($newAttributeValue);
              }
            }
          }
        }
      }
    }
//    $this->collection_policy_stream = $dom->saveXML();
    return $dom->saveXML();
  }


}
?>