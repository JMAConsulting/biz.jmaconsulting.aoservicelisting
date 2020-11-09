<?php
/*
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC. All rights reserved.                        |
 |                                                                    |
 | This work is published under the GNU AGPLv3 license with some      |
 | permitted exceptions and without any warranty. For full license    |
 | and copyright information, see https://civicrm.org/licensing       |
 +--------------------------------------------------------------------+
 */

/**
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 */

/**
 * This class generates form components for processing Event.
 */
class CRM_Aoservicelisting_Form_ProviderApplication extends CRM_Core_Form {

/**
 * Submitted form values
 * @var array
 */
public $formValues = [];

public $organizationId;
public $_loggedInContactID;
public $_mapFields;
public $_elementNames;


  public function preProcess() {
    parent::preProcess();
    CRM_Core_Resources::singleton()->addStyleFile('biz.jmaconsulting.aoservicelisting', 'css/providerformstyle.css');
    $invalidUser = FALSE;
    $loggedInContactId = $this->getLoggedInUserContactID();
    $cid = CRM_Utils_Request::retrieve('cid', 'Positive', $this, FALSE);
    $cs = CRM_Utils_Request::retrieve('cs', 'String', $this, FALSE);
    if (empty($cs) && !empty($_GET['cs'])) {
      $cs = $_GET['cs'];
    }
    if (empty($cid) && !empty($_GET['cid'])) {
      $cid = $_GET['cid'];
    }
    // Validate checksum only if specified in the URL, for non-logged in users.
    if (!empty($cid) && !empty($cs)) {
      if (!CRM_Contact_BAO_Contact_Utils::validChecksum($cid, $cs)) {
        $invalidUser = TRUE;
      }
      else {
        // This is a valid user.
        $loggedInContactId = $cid;
      }
    }
    // Validate if checksum not found in URL, and contact is not logged in, but has specified cid in the URL.
    if (empty($cs) && !empty($cid)) {
      $invalidUser = TRUE;
    }
    if ($invalidUser) {
      CRM_Core_Error::statusBounce(ts('You do not have privilege to edit this application'), CRM_Utils_System::url('civicrm/service-listing-application', 'reset=1'));
    }
    if (!empty($loggedInContactId)) {
      $this->_loggedInContactID = $loggedInContactId;
      $relationship = civicrm_api3('Relationship', 'get', [
        'contact_id_a' => $loggedInContactId,
        'relationship_type_id' => PRIMARY_CONTACT_REL,
      ]);
      if (!empty($relationship['values'])) {
        $this->organizationId = $relationship['values'][$relationship['id']]['contact_id_b'];
        $this->set('organizationId', $relationship['values'][$relationship['id']]['contact_id_b']);
      }
      else {
        $this->_loggedInContactID = NULL;
      }
    }
    if (!empty($cs) && empty($this->getLoggedInUserContactID())) {
      $relationship = civicrm_api3('Relationship', 'get', [
        'contact_id_b' => $loggedInContactId,
        'relationship_type_id' => PRIMARY_CONTACT_REL,
      ]);
      if (!empty($relationship['values'])) {
        $this->_loggedInContactID = $relationship['values'][$relationship['id']]['contact_id_a'];
      }
      else {
        $this->_loggedInContactID = NULL;
      }
      $this->organizationId = $loggedInContactId;
      $this->set('organizationId', $loggedInContactId);
    }
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label) && !strstr($label, 'captcha')) {
        $name = $element->getName();
        if (strstr($name, '[')) {
          $parts = explode('[', str_replace(']', '', $name));
          if (empty($elementNames[$parts[0]])) {
            $elementNames[$parts[0]] = [];
          }
          if (!empty($parts[1])) {
            $elementNames[$parts[0]][$parts[1]] = $label . ' ' . $parts[1];
          }
        }
        else {
          $elementNames[$name] = $label;
        }
      }
    }
    return $elementNames;
  }

  public function buildCustom($id, $name, $viewOnly = FALSE, $ignoreContact = FALSE) {
    if ($id) {
      $button = substr($this->controller->getButtonName(), -4);
      $cid = CRM_Utils_Request::retrieve('cid', 'Positive', $this);
      $session = CRM_Core_Session::singleton();
      $contactID = $session->get('userID');
      $contactID = NULL;
      // we don't allow conflicting fields to be
      // configured via profile
      $fieldsToIgnore = array(
        'participant_fee_amount' => 1,
        'participant_fee_level' => 1,
      );
      if ($contactID && !$ignoreContact) {
        //FIX CRM-9653
        if (is_array($id)) {
          $fields = array();
          foreach ($id as $profileID) {
            $field = CRM_Core_BAO_UFGroup::getFields($profileID, FALSE, CRM_Core_Action::ADD,
              NULL, NULL, FALSE, NULL,
              FALSE, NULL, CRM_Core_Permission::CREATE,
              'field_name', TRUE
            );
            $fields = array_merge($fields, $field);
          }
        }
        else {
          if (CRM_Core_BAO_UFGroup::filterUFGroups($id, $contactID)) {
            $fields = CRM_Core_BAO_UFGroup::getFields($id, FALSE, CRM_Core_Action::ADD,
              NULL, NULL, FALSE, NULL,
              FALSE, NULL, CRM_Core_Permission::CREATE,
              'field_name', TRUE
            );
          }
        }
      }
      else {
        $fields = CRM_Core_BAO_UFGroup::getFields($id, FALSE, CRM_Core_Action::ADD,
          NULL, NULL, FALSE, NULL,
          FALSE, NULL, CRM_Core_Permission::CREATE,
          'field_name', TRUE
        );
      }

      if (array_intersect_key($fields, $fieldsToIgnore)) {
        $fields = array_diff_key($fields, $fieldsToIgnore);
        CRM_Core_Session::setStatus(ts('Some of the profile fields cannot be configured for this page.'));
      }
      $addCaptcha = FALSE;

      if (!empty($this->_fields)) {
        $fields = @array_diff_assoc($fields, $this->_fields);
      }

      $this->assign($name, $fields);
      if (is_array($fields)) {
        foreach ($fields as $key => $field) {
          if ($viewOnly &&
            isset($field['data_type']) &&
            $field['data_type'] == 'File' || ($viewOnly && $field['name'] == 'image_URL')
          ) {
            // ignore file upload fields
            //continue;
          }
          //make the field optional if primary participant
          //have been skip the additional participant.
          if ($button == 'skip') {
            $field['is_required'] = FALSE;
          }
          // CRM-11316 Is ReCAPTCHA enabled for this profile AND is this an anonymous visitor
          elseif ($field['add_captcha'] && !$contactID) {
            // only add captcha for first page
            $addCaptcha = TRUE;
          }
          list($prefixName, $index) = CRM_Utils_System::explode('-', $key, 2);
          if ($viewOnly) {
            $field['is_view'] = $viewOnly;
            if ($field['data_type'] == 'File' || $field['name'] == 'image_URL') {
              $this->add('text', $field['name'], $field['title'], []);
              $this->freeze($field['name']);
              continue;
            }
          }
          CRM_Core_BAO_UFGroup::buildProfile($this, $field, CRM_Profile_Form::MODE_CREATE, $contactID, TRUE);

          $this->_fields[$key] = $field;
        }
      }

      if ($addCaptcha && !$viewOnly) {
        $captcha = CRM_Utils_ReCAPTCHA::singleton();
        $captcha->add($this);
        $this->assign('isCaptcha', TRUE);
      }
    }
  }

  public function getFieldArray($formValues) {
    $this->_elementNames = $this->getRenderableElementNames();
    $this->_mapFields = [
      'listing_type' => [
        1 => ['website'],
        2 => ['organization_name', 'organization_email', 'website'],
      ],
      'primary_section' => [
        'primary_first_name',
        'primary_last_name',
        DISPLAY_NAME,
        'email-Primary',
        DISPLAY_EMAIL,
        'phone-Primary-6',
        DISPLAY_PHONE,
      ],
      'address_section' => [
        'count' => 10,
        'phone',
        'work_address',
        'city',
        'postal_code',
      ],
      'description' => [SERVICE_DESCRIPTION],
      'ABA_section' => [
        ABA_SERVICES => ['yesno'],
        ABA_CREDENTIALS => ['aba_credentials_held_20200401123810'],
        'aba_staff' => [
          'count' => 20,
          'aba_first_name',
          'aba_last_name',
          CERTIFICATE_NUMBER,
        ],
      ],
      'staff_section' => [
        IS_REGULATED_SERVICE => ['yesno'],
        REGULATED_SERVICE_CF => ['regulated_services_provided_20200226231106'],
        'staff' => [
          'count' => 20,
          'staff_first_name',
          'staff_last_name',
          'staff_record_regulator',
        ],
      ],
      'profile_3' => [
        ACCEPTING_NEW_CLIENTS => ['yesno'],
        SERVICES_ARE_PROVIDED => ['service_provided_20200226231158'],
        AGE_GROUPS_SERVED => ['age_groups_served_20200226231233'],
        LANGUAGES => ['language_20180621140924'],
        OTHER_LANGUAGE => [],
      ],
      'camp_section' => [
        'count' => 20,
        CAMP_SESSION_NAME,
        CAMP_FROM,
        CAMP_TO,
      ],
    ];
    $logger = [];
    foreach ($this->_mapFields as $section => $fields) {
      if ($section == 'listing_type') {
        foreach ($fields[$formValues[$section]] as $fieldName) {
          if (!empty($formValues[$fieldName])) {
            $logger[$section] .= sprintf('<br/> <b>%s:</b> %s', $this->_elementNames[$fieldName], $formValues[$fieldName]);
          }
        }
      }
      elseif ($section == 'primary_section') {
        foreach ($fields as $fieldName) {
          if (in_array($fieldName, [DISPLAY_NAME, DISPLAY_EMAIL, DISPLAY_PHONE])) {
            self::yesNo($formValues[$fieldName]);
          }
          $logger[$section] .= sprintf('<br/> <b>%s:</b> %s', $this->_elementNames[$fieldName], $formValues[$fieldName]);
        }
      }
      elseif ($section == 'address_section') {
        $count = $fields['count'];
        unset($fields['count']);
        $entryFound = FALSE;
        for ($i = 1; $i <= $count; $i++) {
          if ($entryFound) {
            break;
          }
          foreach ($fields as $name) {
            if (empty($formValues[$name][$i])) {
              $entryFound = TRUE;
            }
            if (!empty($formValues[$name][$i])) {
              $logger[$section] .= sprintf('<br/> <b>%s:</b> %s', $this->_elementNames[$name][$i], $formValues[$name][$i]);
            }
          }
        }
      }
      elseif ($section == 'description') {
        if (!empty($formValues[$fields[0]])) {
          $logger[$section] .= sprintf('<br/> <b>%s:</b> %s', $this->_elementNames[$fields[0]], $formValues[$fields[0]]);
        }
      }
      elseif ($section == 'ABA_section' || $section == 'staff_section') {
        $key = $section == 'ABA_section' ? 'aba_staff' : 'staff';
        $abaFields = $fields[$key];
        unset($fields[$key]);
        foreach ($fields as $fieldName => $options) {
          if (is_array($formValues[$fieldName])) {
            $result = $formValues[$fieldName] = array_filter($formValues[$fieldName], 'strlen');
            if (!empty($result)) {
              if (!empty($options)) {
                $allOptions = CRM_Core_OptionGroup::values($options[0]);
              }
              $newArray = self::replaceKeys($formValues[$fieldName], $allOptions);
              $newValue = implode(', ', array_keys($newArray));
              $logger[$section] .= sprintf('<br/> <b>%s:</b> %s', $this->_elementNames[$fieldName], $newValue);
            }
          }
          else {
            if (!empty($options) && $options[0] == 'yesno') {
              self::yesNo($formValues[$fieldName]);
            }
            if (!empty($formValues[$fieldName])) {
              $logger[$section] .= sprintf('<br/> <b>%s:</b> %s', $this->_elementNames[$fieldName], $formValues[$fieldName]);
            }
          }
        }
        $count = $abaFields['count'];
        unset($abaFields['count']);
        $entryFound = FALSE;
        for ($i = 1; $i <= $count; $i++) {
          if ($entryFound) {
            break;
          }
          foreach ($abaFields as $name) {
            if (empty($formValues[$name][$i])) {
              $entryFound = TRUE;
            }
            if (!empty($formValues[$name][$i])) {
              $logger[$section] .= sprintf('<br/> <b>%s:</b> %s', $this->_elementNames[$name][$i], $formValues[$name][$i]);
            }
          }
        }
      }
      elseif ($section == 'profile_3') {
        foreach ($fields as $fieldName => $options) {
          if (is_array($formValues[$fieldName])) {
            $result = $formValues[$fieldName] = array_filter($formValues[$fieldName], 'strlen');
            if (!empty($result)) {
              if (!empty($options)) {
                $allOptions = CRM_Core_OptionGroup::values($options[0]);
              }
              $newArray = self::replaceKeys($formValues[$fieldName], $allOptions);
              $newValue = implode(', ', array_keys($newArray));
              if ($fieldName == LANGUAGES) {
                $newValue = implode(', ', $formValues[$fieldName]);
              }
              $logger[$section] .= sprintf('<br/> <b>%s:</b> %s', $this->_elementNames[$fieldName], $newValue);
            }
          }
          else {
            if (!empty($options) && $options[0] == 'yesno') {
              self::yesNo($formValues[$fieldName]);
            }
            if (!empty($formValues[$fieldName])) {
              $logger[$section] .= sprintf('<br/> <b>%s:</b> %s', $this->_elementNames[$fieldName], $formValues[$fieldName]);
            }
          }
        }
      }
    }

    return $logger;
  }

  public static function yesNo(&$value) {
    if (!empty($value)) {
      $value = 'Yes';
    }
    else {
      $value = 'No';
    }
  }

  public static function replaceKeys($array, $replacement_keys) {
    foreach ($array as $key => $name) {
      foreach($replacement_keys as $option => $value) {
        if ($name && array_key_exists($option, $array)) {
          $array[$value] = $name;
          unset($array[$key]);
        }
      }
    }
    return $array;
  }

  public function processCustomValue(&$values) {
    foreach ($values as $key => $value) {
      if (strstr($key, 'custom_')) {
        if (!is_array($value)) {
          if (trim($value) === '') {
            unset($values[$key]);
          }
        }
        else {
          foreach ($value as $k => $v) {
            if (trim($v) === '') {
              unset($values[$key][$k]);
            }
          }
        }
      }
    }
  }

  /**
   * @param string $type
   *   Eg 'Contribution'.
   * @param string $subType
   * @param int $entityId
   */
  public function applyCustomData($type, $subType, $entityId) {
    $this->set('type', $type);
    $this->set('subType', $subType);
    $this->set('entityId', $entityId);

    CRM_Custom_Form_CustomData::preProcess($this, NULL, $subType, 1, $type, $entityId);
    CRM_Custom_Form_CustomData::buildQuickForm($this);
    CRM_Custom_Form_CustomData::setDefaultValues($this);
  }

}
