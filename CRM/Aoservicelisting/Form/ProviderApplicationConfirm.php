<?php

use CRM_Aoservicelisting_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Aoservicelisting_Form_ProviderApplicationConfirm extends CRM_Aoservicelisting_Form_ProviderApplication {

  public function buildQuickForm() {
    $defaults = $this->get('formValues');
    $serviceListingOptions = [1 => E::ts('Individual'), 2 => E::ts('Organization')];
    $this->addRadio('listing_type', E::ts('Type of Service Listing'), $serviceListingOptions);
    $this->add('text', 'organization_name', E::ts('Organization Name'));
    $this->add('email', 'organization_email', E::ts('Organization Email'));
    $this->add('text', 'website', E::ts('Website'));
    $this->add('text', 'primary_first_name', E::ts('First Name'));
    $this->add('text', 'primary_last_name', E::ts('Last Name'));
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

    $this->buildCustom(PRIMARY_PROFILE, 'profile', TRUE);
    $this->buildCustom(SERVICELISTING_PROFILE1, 'profile1', TRUE);
    $this->buildCustom(SERVICELISTING_PROFILE2, 'profile2', TRUE);

    // populating camp values
    $customFields = civicrm_api3('CustomField', 'get', ['custom_group_id' => CAMP_CG])['values'];
    $campValues = [];
    $count = 1;
    $totalCount = 21;
    while($count < $totalCount) {
      $entryFound = FALSE;
      $campValues[$count] = [];
      foreach ($customFields as $customField) {
        $key = 'custom_' . $customField['id'];
        $campValues[$count][$key] = [
          'label' => $customField['label'],
          'html' => NULL,
        ];
        if (!empty($defaults[$key . '_-' . $count])) {
          $campValues[$count][$key]['html'] = $defaults[$key . '_-' . $count];
          $entryFound = TRUE;
        }
        elseif (!empty($defaults[$key . '_' . $count])) {
          $campValues[$count][$key]['html'] = $defaults[$key . '_' . $count];
          $entryFound = TRUE;
        }
      }
      if (!$entryFound) {
        unset($campValues[$count]);
      }
      $count++;
    }

    $this->assign('campValues', $campValues);

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

    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    parent::postProcess();
    $this->submit($values);
  }

  public function submit($values) {
    $this->processCustomValue($values);
    if (empty($values['organiation_name'])) {
      $values['organization_name'] = 'Self-employed ' . $values['primary_first_name'] . ' ' . $values['primary_last_name'];
    }
    $organization_params = [
      'organization_name' => $values['organization_name'],
      'email' => $values['organization_email'],
    ];
    if (!empty($this->organizationId)) {
      $organization_params['id'] = $this->organizationId;
    }
    else {
      $dedupeParams = CRM_Dedupe_Finder::formatParams($organization_params, 'Organization');
      $dedupeParams['check_permission'] = 0;
      $dupes = CRM_Dedupe_Finder::dupesByParams($dedupeParams, 'Organization', NULL, [], 11);
      $organization_params['contact_id'] = CRM_Utils_Array::value('0', $dupes, NULL);
      $organization_params['contact_sub_type'] = 'service_provider';
      $organization_params['contact_type'] = 'Organization';
    }
    $organization = civicrm_api3('Contact', 'create', $organization_params);

    $addressParams1 = [
      'street_address' => $values['work_address'][1],
      'postal_code' => $values['postal_code'][1],
      'city' => $values['city'][1],
      'state_province_id' => 'Ontario',
      'country_id' => 'CA',
      'location_type_id' => 'Work',
      'is_primary' => 1,
      'contact_id' => $organization['id'],
    ];
    $address1 = civicrm_api3('Address' , 'create', array_merge($addressParams1,
     ['id' => civicrm_api3('Address', 'getvalue', ['contact_id' => $organization['id'], 'is_primary' => TRUE, 'return' => 'id'])]
    ));

    $id = civicrm_api3('Website', 'getvalue', [
      'contact_id' => $organization['id'],
      'url' => $values['website'],
      'return' => 'id',
      'options' => ['limit' => 1],
    ]);
    if ($id) {
      civicrm_api3('Website', 'create', [
        'contact_id' => $organization['id'],
        'url' => $values['website'],
        'website_type_id' => 'Work',
      ]);
    }

    $addressIds = [0 => [$address1['id'], $addressParams1]];
    $staffMemberIds = [];

    $customValues = CRM_Core_BAO_CustomField::postProcess($values, $organization['id'], 'Organization');
    if (!empty($customValues) && is_array($customValues)) {
      CRM_Core_BAO_CustomValueTable::store($customValues, 'civicrm_contact', $organization['id']);
    }

    civicrm_api3('CustomValue', 'create', [
      WAIVER_FIELD => $values['waiver_field'],
      'entity_id' => $organization['id'],
    ]);
    for ($rowNumber = 1; $rowNumber <= 20; $rowNumber++) {
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
          $individualParams['email'] = $values['email-Primary'];
        }
        $dedupeParams = CRM_Dedupe_Finder::formatParams($individualParams, 'Individual');
        $dedupeParams['check_permission'] = 0;
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
          if (!empty($values['phone-Primary-6'])) {
            civicrm_api3('Phone', 'create', [
              'phone' => $values['phone-Primary-6'],
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
          $relationshipParams['relationship_type_id'] = PRIMARY_CONTACT_REL;
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
    // Redirect to thank you page.
    CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/one-stop-listing-thankyou', 'reset=1'));
  }

}
