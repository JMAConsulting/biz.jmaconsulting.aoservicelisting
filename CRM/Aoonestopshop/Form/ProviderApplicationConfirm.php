<?php

use CRM_Aoonestopshop_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Aoonestopshop_Form_ProviderApplicationConfirm extends CRM_Aoonestopshop_Form_ProviderApplication {
  public function buildQuickForm() {
    $defaults = $this->get('formValues');
    unset($defaults['qfKey']);
    unset($defaults['entryUrl']);
    $serviceProviderOptions = [1 => E::ts('Individual'), 2 => E::ts('Organization')];
    $this->addRadio('provider_type', E::ts('Type of Service Provider'), $serviceProviderOptions);
    $this->add('text', 'organization_name', E::ts('Organization Name'));
    $this->add('email', 'organization_email', E::ts('Organization Email'));
    $this->add('text', 'website', E::ts('Website'));
    $this->add('text', 'primary_first_name', E::ts('First Name'));
    $this->add('text', 'primary_last_name', E::ts('Last Name'));
    $this->add('email', 'primary_email', E::ts('Email address'));
    $this->add('text', 'primary_phone_number', E::ts('Phone Number'));
    $this->add('text', 'primary_website', E::ts('Website'), ['maxlength' => 255]);
    $this->add('advcheckbox', 'display_name_public', E::ts('Display First Name and Last Name in public listing?'));
    $this->add('advcheckbox', 'display_email', E::ts('Display email address in public listing?'));
    $this->add('advcheckbox', 'display_phone', E::ts('Display phone number in public listing?'));
    $this->add('advcheckbox', 'waiver_field' , E::ts('I agree to the above waiver'));

    for ($rowNumber = 1; $rowNumber <= 11; $rowNumber++) {
      $this->add('text', "phone[$rowNumber]", E::ts('Phone Number'), ['size' => 20, 'maxlength' => 32, 'class' => 'medium']);
      $this->add('text', "work_address[$rowNumber]", E::ts('Work Address'), ['size' => 45, 'maxlength' => 96, 'class' => 'huge']);
      $this->add('text', "postal_code[$rowNumber]", E::ts('Postal code'), ['size' => 20, 'maxlength' => 64, 'class' => 'medium']);
      $this->add('text', "city[$rowNumber]", E::ts('City/Town'), ['size' => 20, 'maxlength' => 64, 'class' => 'medium']);
    }
    for ($rowNumber = 1; $rowNumber <= 22; $rowNumber++) {
      $this->add('text', "staff_first_name[$rowNumber]", E::ts('First Name'), ['size' => 20, 'maxlength' => 32, 'class' => 'medium']);
      $this->add('text', "staff_last_name[$rowNumber]", E::ts('Last Name'), ['size' => 20, 'maxlength' => 32, 'class' => 'medium']);
      $this->add('text', "staff_record_regulator[$rowNumber]", E::ts('Record on Regulator\'s site'), ['size' => 20, 'maxlength' => 255, 'class' => 'medium']);
    }
    $customFields = [861 => TRUE, 862 => TRUE, 863 => TRUE, 864 => TRUE, 865 => TRUE, 866 => FALSE, 867 => TRUE];
    foreach ($customFields as $id => $isRequired) {
      CRM_Core_BAO_CustomField::addQuickFormElement($this, "custom_{$id}", $id, $isRequired);
    }
    $this->assign('beforeStaffCustomFields', [861, 862, 863]);
    $this->assign('afterStaffCustomFields', [864, 865, 866, 867]);

    for ($row = 1; $row <= 21; $row++) {
      CRM_Core_BAO_CustomField::addQuickFormElement($this, "custom_858[$row]", 858, FALSE);
      CRM_Core_BAO_CustomField::addQuickFormElement($this, "custom_859[$row]", 859, FALSE);
      CRM_Core_BAO_CustomField::addQuickFormElement($this, "custom_860[$row]", 860, FALSE);
    }
    $this->setDefaults($defaults);
    $this->freeze();
    $this->addButtons(array(
      array(
        'type' => 'upload',
        'name' => E::ts('Continue'),
        'isDefault' => TRUE,
      ),
    ));

    $this->addButtons([
      [
        'type' => 'submit',
        'name' => E::ts('Submit'),
        'isDefault' => TRUE,
      ],
      [
        'type' => 'back',
        'name' => E::ts('Previous'),
      ],
    ]);

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    
    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    parent::postProcess();
    $this->submit($values);
  }

  public function submit($values) {
    if (empty($values['organiation_name'])) {
      $values['organization_name'] = 'Self-employed ' . $values['primary_first_name'] . ' ' . $values['primary_last_name'];
      $values['organization_email'] = $values['primary_email'];
    }
    $organization_params = [
      'organization_name' => $values['organization_name'],
      'email' => $values['organization_email'],
    ];
    $dedupeParams = CRM_Dedupe_Finder::formatParams($organization_params, 'Organization');
    $dedupeParams['check_permission'];
    $dupes = CRM_Dedupe_Finder::dupesByParams($dedupeParams, 'Organization', NULL, [], 11);
    $organization_params['contact_id'] = CRM_Utils_Array::value('0', $dupes, NULL);
    $organization_params['contact_sub_type'] = 'service_provider';
    $organization_params['contact_type'] = 'Organization';
    $organization = civicrm_api3('Contact', 'create', $organization_params);
    $address1Params = [
      'street_address' => $values['work_address'][1],
      'postal_code' => $values['postal_code'][1],
      'city' => $values['city'][1],
      'state_province_id' => 'Ontario',
      'country_id' => 'CA',
      'location_type_id' => 'Work',
      'is_primary' => 1,
      'contact_id' => $organization['id'],
    ];
    $address1 = civicrm_api3('Address', 'get', $address1Params);
    if (empty($address1['count'])) {
      $address1 = civicrm_api3('Address', 'create', $address1Params);
    }
    $websiteParams = [
      'contact_id' => $organization['id'],
      'url' => $values['website'],
      'website_type_id' => 'Work',
    ];
    civicrm_api3('Website', 'create', $websiteParams);
    if (!empty($values['primary_phone_number'])) {
      civicrm_api3('Phone', 'create', [
        'phone' => $values['primary_phone_number'],
        'location_type_id' => 'Work',
        'contact_id' => $organization['id'],
        'phone_type_id' => 'Phone',
        'is_primary' => 1,
      ]);
    }
    $addressIds = [0 => [$address1['id'], $address1Params]];
    $staffMemberIds = [];
    $customFields = ['861', '862', '863', '864', '865', '866', '867'];
    $customFieldParams = ['entity_id' => $organization['id']];
    foreach ($customFields as $customField) {
      if (in_array($customField, ['863', '865', '866'])) {
        $selectedValues = [];
        foreach ($values['custom_' . $customField] as $val => $selected) {
          if ($selected) {
            $selectedValues[] = $val;
          }
        }
        $customFieldParams['custom_' . $customField] = $selectedValues;
      }
      else {
        $customFieldParams['custom_' . $customField] = $values['custom_' . $customField];
      }
    }
    $customFieldParams['custom_868'] = empty($values['display_name_public']) ? 0 : 1;
    $customFieldParams['custom_869'] = empty($values['display_email']) ? 0 : 1;
    $customFieldParams['custom_870'] = empty($values['display_phone']) ? 0 : 1;
    $customFieldParams['custom_871'] = $values['waiver_field'];
    civicrm_api3('CustomValue', 'create', $customFieldParams);
    for ($rowNumber = 1; $rowNumber <= 20; $rowNumber++) {
      if (!empty($values['custom_858'][$rowNumber])) {
        $campCustomFieldParams = [
          'entity_id' => $organization['id'],
          'custom_858' => $values['custom_858'][$rowNumber],
          'custom_859' => $values['custom_859'][$rowNumber],    
          'custom_860' => $values['custom_860'][$rowNumber],
        ];
        civicrm_api3('CustomValue', 'create', $campCustomFieldParams);
      }
      if (!empty($values['phone'][$rowNumber])) {
        civicrm_api3('Phone', 'create', [
          'phone' => $values['phone'][$rowNumber],
          'location_type_id' => 'Work',
          'contact_id' => $organization['id'],
          'phone_type_id' => 'Phone',
        ]);
      }
      if ($rowNumber !== 1 && !empty($values['work_address'][$rowNumber])) {
        $addressParams = [
          'street_address' => $values['work_address'][$rowNumber],
          'city' => $values['city'][$rowNumber],
          'postal_code' => $values['city'][$rowNumber],
          'contact_id' => $organization['id'],
          'country_id' => 'CA',
          'state_province_id' => 'Ontario',
          'location_type_id' => 'Work',
        ];
        $address = civicrm_api3('Address', 'create', $addressParams);
        $addressIds[] = [$address['id'], $addressParams];
      }
      if (!empty($values['staff_first_name'][$rowNumber])) {
        $individualParams = [
          'first_name' => $values['staff_first_name'][$rowNumber],
          'last_name' => $values['staff_last_name'][$rowNumber],
        ];
        if ($rowNumber === 1) {
          $individualParams['email'] = $values['primary_email'];
        }
        $dedupeParams = CRM_Dedupe_Finder::formatParams($individualParams, 'Individual');
        $dedupeParams['check_permission'];
        $dupes = CRM_Dedupe_Finder::dupesByParams($dedupeParams, 'Individual', NULL, [], 9);
        $individualParams['contact_id'] = CRM_Utils_Array::value('0', $dupes, NULL);
        $individualParams['contact_type'] = 'Individual';
        if (empty($individualParams['contact_id'])) {
          $individualParams['contact_sub_type'] = 'Provider';
        }
        $staffMember = civicrm_api3('Contact', 'create', $individualParams);
        $staffMemberIds[] = $staffMember['id'];
        civicrm_api3('Website', 'create', [
          'website_type_id' => 'Work',
          'url' => $values['staff_record_regulator'][$rowNumber],
          'contact_id' => $staffMember['id'],
        ]);
        if ($rowNumber == 1) {
          if (!empty($values['primary_phone_number'])) {
            civicrm_api3('Phone', 'create', [
              'phone' => $values['primary_phone_number'],
              'location_type_id' => 'Work',
              'contact_id' => $staffMember['id'],
              'phone_type_id' => 'Phone',
              'is_primary' => 1,
            ]);
          }
        }
        $relationshipParams = [
          'contact_id_a' => $staffMember['id'],
          'contact_id_b' => $organization['id'],
          'relationship_type_id' => 5,
        ];
        $relationshipCheck = civicrm_api3('Relationship', 'get', $relationshipParams);
        if (empty($relationshipCheck['count'])) {
          try {
            civicrm_api3('Relationship', 'create', $relationshipParams);
          }
          catch (Exception $e) {
          }
        }
        if ($rowNumber === 1) {
          $relationshipParams['relationship_type_id'] = 74;
          $relationshipCheck = civicrm_api3('Relationship', 'get', $relationshipParams);
          if (empty($relationshipCheck['count'])) {
            try {
              civicrm_api3('Relationship', 'create', $relationshipParams);
            }
            catch (Exception $e) {
            }
          }
        }
      }
    }
    foreach ($staffMemberIds as $staffMemberId) {
      foreach ($addressIds as $key => $details) {
        $params = $details[1];
        $params['contact_id'] = $staffMemberId;
        $params['master_id'] = $details[0];
        $params['add_relationship'] = 0;
        civicrm_api3('Address', 'create', $params);
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
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

}
