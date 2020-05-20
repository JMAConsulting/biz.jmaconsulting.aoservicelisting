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

  public function preProcess() {
    parent::preProcess();
    CRM_Core_Resources::singleton()->addStyleFile('biz.jmaconsulting.aoservicelisting', 'css/providerformstyle.css');
    // for testing purpose consider cid value from url
    $loggedInContactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, FALSE) ?: $this->getLoggedInUserContactID();
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
    $this->_mapFields = [
      'listing_type' => [
        1 => ['website'],
        2 => ['organization_name', 'organization_email', 'website'],
      ],
      'primary_section' => [
        'primary_first_name',
        'primary_last_name',
        'custom_900',
        'email-Primary',
        'custom_901',
        'phone-Primary-6',
        'custom_902',
      ],
      'address_section' => [
        'count' => 10,
        'phone',
        'work_address',
        'city',
        'postal_code',
      ],
      'custom_893',
      'ABA_section' => [
        'custom_912',
        'custom_911',
        'aba_staff' => [
          'count' => 20,
          'aba_first_name',
          'aba_last_name',
          CERTIFICATE_NUMBER,
        ],
      ],
      'staff_section' => [
        'custom_894',
        'custom_895',
        'staff' => [
          'count' => 20,
          'staff_first_name',
          'staff_first_name',
          'staff_record_regulator',
        ],
      ],
      'profile_3' => [
        'custom_896',
        'custom_897',
        'custom_898',
        'custom_899',
        'custom_905',
      ],
      'camp_section' => [
        'count' => 20,
        'custom_890',
        'custom_891',
        'custom_892',
      ],
      'waiver_field',
    ];
    $logger = [];
    foreach ($mapFields as $section => $fields) {
      $this->_logger[$section] = '';
      if ($section == 'listing_type') {
        foreach ($fields[$formValues[$section]] as $fieldName) {
          $logger[$section] .= sprintf('<br/> %s: %s', $this->_elementNames[$fieldName], $formValues[$fieldName]);
        }
      }
      elseif ($section == 'primary_section') {
        foreach ($fields as $fieldName) {
          $logger[$section] .= sprintf('<br/> %s: %s', $this->_elementNames[$fieldName], $formValues[$fieldName]);
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
            $logger[$section] .= sprintf('<br/> %s $d: %s', $this->_elementNames["{$name}[{$i}]"], $i, $formValues[$name][$i]);
          }
        }
      }
      elseif ($section == 'ABA_section' || $section == 'staff_section') {
        $key = $section == 'ABA_section' ? 'aba_staff' : 'staff';
        $abaFields = $fields[$key];
        unset($fields[$key]);
        foreach ($fields as $fieldName) {
          if (is_array($formValues[$fieldName])) {
            $result = $formValues[$fieldName] = array_filter($formValues[$fieldName], 'strlen');
            if (!empty($result)) {
              $newValue = implode(', ', array_keys($formValues[$fieldName]));
              $logger[$section] .= sprintf('<br/> %s: %s', $this->_elementNames[$fieldName], $newValue);
            }
          }
          else {
            $logger[$section] .= sprintf('<br/> %s: %s', $this->_elementNames[$fieldName], $formValues[$fieldName]);
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
            $logger[$section] .= sprintf('<br/> %s $d: %s', $this->_elementNames["{$name}[{$i}]"], $i, $formValues[$name][$i]);
          }
        }
      }
    }

    return $logger;
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
