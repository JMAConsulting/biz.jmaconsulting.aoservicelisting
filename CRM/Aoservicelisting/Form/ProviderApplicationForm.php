<?php

use CRM_Aoservicelisting_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Aoservicelisting_Form_ProviderApplicationForm extends CRM_Aoservicelisting_Form_ProviderApplication {

  public $listingType = 1;

  public function setDefaultValues() {
    $defaults = [];
    $fields = CRM_Core_BAO_UFGroup::getFields(PRIMARY_PROFILE, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->organizationId, $fields, $defaults, TRUE);
    $fields = CRM_Core_BAO_UFGroup::getFields(SERVICELISTING_PROFILE1, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->organizationId, $fields, $defaults, TRUE);
    $fields = CRM_Core_BAO_UFGroup::getFields(SERVICELISTING_PROFILE2, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->organizationId, $fields, $defaults, TRUE);
    if (empty($this->organizationId)) {
      $defaults['listing_type'] = 1;
    }

    if (!empty($this->_loggedInContactID)) {
      if (!empty($this->organizationId)) {
        $organization = civicrm_api3('Contact', 'getsingle', [
          'id' => $this->organizationId,
          'return' => ['organization_name'],
        ]);
        $primraryContact = civicrm_api3('Contact', 'getsingle', [
          'id' => $this->_loggedInContactID,
        ]);
        $primaryContactPhone = civicrm_api3('Phone', 'getsingle', ['contact_id' => $this->_loggedInContactID, 'is_primary' => 1]);
        $defaults['staff_first_name[1]'] = $defaults['primary_first_name'] = $primraryContact['first_name'];
        $defaults['staff_last_name[1]'] = $defaults['primary_last_name'] = $primraryContact['last_name'];
        $defaults['staff_contact_id[1]'] = $this->_loggedInContactID;
        $defaults['phone[1]'] = $primaryContactPhone['phone'];
        $primaryStaffWebsite = civicrm_api3('Website', 'get', ['contact_id' => $primraryContact['id'], 'is_active' => 1, 'url' => ['IS NOT NULL' => 1]]);
        if (!empty($primaryStaffWebsite['count'])) {
          $defaults['staff_record_regulator[1]'] = $primaryStaffWebsite['values'][$primaryStaffWebsite['id']]['url'];
        }
        foreach (['organization_name',  'email'] as $field) {
          if ($field === 'organization_name' && stristr($organization[$field], 'self-employed') === FALSE) {
            $defaults['listing_type'] = 2;
            $this->listingType = 2;
          }
          else {
            $defaults['listing_type'] = 1;
            $this->listingType = 1;
          }
          if ($field === 'email') {
            $defaults['organization_email'] = $organization[$field];
          }

          $defaults[$field] = $organization[$field];
        }
        $primrayWorkAddress = civicrm_api3('Address', 'getsingle', ['contact_id' => $this->organizationId, 'is_primary' => 1]);
        $defaults['work_address[1]'] = $primrayWorkAddress['street_address'];
        $defaults['postal_code[1]'] = $primrayWorkAddress['postal_code'];
        $defaults['city[1]'] = $primrayWorkAddress['city'];
        $primaryWebsite = civicrm_api3('Website', 'get', ['contact_id' => $this->organizationId, 'url' => ['IS NOT NULL' => 1], 'sequential' => 1]);
        $defaults['website'] = $primaryWebsite['values'][0]['url'];
        // Get details of the other staff members
        $staffMembers = civicrm_api3('Relationship', 'get', [
          'contact_id_b' => $this->organizationId,
          'contact_id_a' => ['!=' => $this->_loggedInContactID],
          'is_active'  => 1,
          'sequential' => 1,
        ]);
        $staffRowCount = 2;
        if (!empty($staffMembers['count'])) {
          foreach ($staffMembers['values'] as $staffMember) {
            $staffMemberContactId = $staffMember['contact_id_a'];
            $staffDetails = civicrm_api3('Contact', 'getsingle', ['id' => $staffMemberContactId]);
            $defaults['staff_contact_id[' . $staffRowCount . ']'] = $staffMember['contact_id_a'];
            $defaults['staff_first_name[' . $staffRowCount . ']'] = $staffDetails['first_name'];
            $defaults['staff_last_name[' . $staffRowCount . ']'] = $staffDetails['last_name'];
            $website = civicrm_api3('Website', 'get', ['contact_id' => $staffMemberContactId, 'url' => ['IS NOT NULL' => 1], 'sequential' => 1]);
            if (!empty($website['count'])) {
              $defaults['staff_record_regulator[' . $staffRowCount . ']'] = $website['values'][0]['url'];
            }
            $staffRowCount++;
          }
        }
      }
    }

    return $defaults;
  }

  public function buildQuickForm() {
    $attr = empty($this->organizationId) ? [] : ['readonly' => TRUE];
    $serviceListingOptions = [1 => E::ts('Individual'), 2 => E::ts('Organization')];
    $listingTypeField = $this->addRadio('listing_type', E::ts('Type of Service Listing'), $serviceListingOptions, $attr);
    $organizationNameField = $this->add('text', 'organization_name', E::ts('Organization Name'), $attr);
    $this->add('email', 'organization_email', E::ts('Organization Email'));
    $this->add('text', 'website', E::ts('Website'), TRUE);
    $nameAttr = (!empty($this->organizationId) && $this->listingType = 1) ? ['readonly' => TRUE] : [];
    $this->add('text', 'primary_first_name', E::ts('First Name'), $nameAttr);
    $this->add('text', 'primary_last_name', E::ts('Last Name'), $nameAttr);
    $this->add('advcheckbox', 'waiver_field' , E::ts('I agree to the above waiver'));
    for ($rowNumber = 1; $rowNumber <= 11; $rowNumber++) {
      $this->add('text', "phone[$rowNumber]", E::ts('Phone Number'), ['size' => 20, 'maxlength' => 32, 'class' => 'medium']);
      $this->add('text', "work_address[$rowNumber]", E::ts('Work Address'), ['size' => 45, 'maxlength' => 96, 'class' => 'huge']);
      $this->add('text', "postal_code[$rowNumber]", E::ts('Postal code'), ['size' => 20, 'maxlength' => 64, 'class' => 'medium']);
      $this->add('text', "city[$rowNumber]", E::ts('City/Town'), ['size' => 20, 'maxlength' => 64, 'class' => 'medium']);
    }
    for ($rowNumber = 1; $rowNumber <= 22; $rowNumber++) {
      $this->add('hidden', "staff_contact_id[$rowNumber]", NULL);
      $this->add('text', "staff_first_name[$rowNumber]", E::ts('First Name'), ['size' => 20, 'maxlength' => 32, 'class' => 'medium']);
      $this->add('text', "staff_last_name[$rowNumber]", E::ts('Last Name'), ['size' => 20, 'maxlength' => 32, 'class' => 'medium']);
      $this->add('text', "staff_record_regulator[$rowNumber]", E::ts('Record on Regulator\'s site'), ['size' => 20, 'maxlength' => 255, 'class' => 'medium']);
    }

    $this->buildCustom(PRIMARY_PROFILE, 'profile');
    $this->buildCustom(SERVICELISTING_PROFILE1, 'profile1');
    $this->buildCustom(SERVICELISTING_PROFILE2, 'profile2');

    // this part is to render camp fields
    $customFields = civicrm_api3('CustomField', 'get', ['custom_group_id' => CAMP_CG])['values'];
    $campFields = $campDefaultValues = $columnNames = [];
    for ($i = 1; $i <= 21; $i++) {
      $campFields[$i] = [];
      foreach ($customFields as $customField) {
        // when we insert new value for multi-valued custom field the key is suppose to be in custom_xx_-1 otherwise custom_xx_1 where xx is the custom field id
        $key = 'custom_' . $customField['id'] . '_-' . $i;
        if ($this->organizationId) {
          $campDefaultValues[$i][$customField['column_name']] = $key;
          if ($i == 1) {
            $columnNames[] = $customField['column_name'];
          }
        }
        $campFields[$i][] = $key;
        CRM_Core_BAO_CustomField::addQuickFormElement($this, $key, $customField['id'], FALSE);
      }
    }
    $this->assign('campFields', $campFields);

    if (!empty($this->organizationId)) {
      // this part is to set default values of camp fields on basis of stored value
      $defaults = [];
      $tableName = civicrm_api3('CustomGroup', 'getvalue', ['id' => CAMP_CG, 'return' => "table_name"]);
      $results = CRM_Core_DAO::executeQuery("SELECT * FROM $tableName WHERE entity_id = " . $this->organizationId)->fetchAll();
      foreach ($results as $key => $values) {
        $count = $key + 1;
        foreach ($values as $columnName => $value) {
          if (in_array($columnName, $columnNames)) {
            $defaults[$campDefaultValues[$count][$columnName]] = $value;
          }
        }
      }
      if (!empty($defaults)) {
        $this->setDefaults($defaults);
      }
    }

    $this->addButtons(array(
      array(
        'type' => 'upload',
        'name' => E::ts('Continue'),
        'isDefault' => TRUE,
      ),
    ));


    $this->addFormRule(['CRM_Aoservicelisting_Form_ProviderApplicationForm', 'providerFormRule']);
    parent::buildQuickForm();
  }

  public function providerFormRule($values) {
    $errors = $setValues = [];
    $regulatorRecordKeys = $verifiedURLCounter = [];
    $staffMemberCount = 0;
    $regulatorUrlMapping = CRM_Core_OptionGroup::values('regulator_url_mapping');

    foreach ($values[REGULATED_SERVICE_CF] as $value => $checked) {
      if ($checked) {
        $setValues[] = $value;
      }
    }
    foreach ($values['staff_record_regulator'] as $key => $value) {
      if (!empty($value)) {
        $regulatorRecordKeys[$key] = 1;
        $staffMemberCount++;
        if (stristr($value, 'ontariocampassociation.ca') === FALSE) {
          if (empty($values['staff_first_name'][$key])) {
            $errors['staff_first_name' . '[' . $key . ']'] = E::ts('Need to provide the first name of the regulated staff member');
          }
          if (empty($values['staff_last_name'][$key])) {
            $errors['staff_last_name' . '[' . $key . ']'] = E::ts('Need to provide the last name of the regulated staff member');
          }
        }
        // Check if the content of the record on regulator site matches a given url.
        $regulatedUrlValidated = FALSE;
        $urls = [];
        foreach ($setValues as $serviceValue) {
          if (!empty($regulatorUrlMapping[$serviceValue])) {
            if (array_key_exists($serviceValue, $verifiedURLCounter) === FALSE) {
              $verifiedURLCounter[$serviceValue] = 0;
            }
            $urls[] = $regulatorUrlMapping[$serviceValue];
          }
        }
        if (!empty($urls)) {
          foreach ($urls as $url) {
            if (!$regulatedUrlValidated && stristr($value, $url) !== FALSE) {
              $serviceValueFound = array_search($url, $regulatorUrlMapping);
              $verifiedURLCounter[$serviceValueFound] = $verifiedURLCounter[$serviceValueFound] + 1;
              $regulatedUrlValidated = TRUE;
              unset($regulatorRecordKeys[$key]);
            }
          }
        }
        else {
          unset($regulatorRecordKeys[$key]);
        }
        // If any urls have not matched show an error.
        if (!empty($regulatorRecordKeys)) {
          foreach ($regulatorRecordKeys as $rowKey => $val) {
            $errors['staff_record_regulator[' . $rowKey . ']'] = E::ts('Please ensure that your Record on Regulator’s site matches the regulator’s domain for one of the regulated professions that you selected.');
          }
        }
      }
    }
    $options = self::_getServieOptions();
    foreach ($verifiedURLCounter as $value => $counter) {
      if (empty($counter) && array_key_exists($value, $options) !== FALSE) {
        $missingRegulators[] = $options[$value];
      }
    }

    if ($values['listing_type'] == 1 && empty($values[DISPLAY_NAME])) {
      $errors[DISPLAY_NAME] = E::ts('first name and last name of listing must be publicly displayed');
    }
    if ($values['listing_type'] == 1 && empty($values[DISPLAY_EMAIL]) && empty($values[DISPLAY_PHONE])) {
      $errors[DISPLAY_EMAIL] = E::ts('At least one of email or phone must be provided and made public');
    }

    $addressFieldLables = ['phone' => E::ts('Phone Number'), 'work_address' => E::ts('Address'), 'postal_code' => E::ts('Postal code'), 'city' =>  E::ts('City/Town')];
    foreach (['phone', 'work_address', 'postal_code', 'city', 'postal_code'] as $addressField) {
      if (empty($values[$addressField][1])) {
        $errors[$addressField . '[1]'] = E::ts('Primary Work Location %1 is a required field.', [1 => $addressFieldLables[$addressField]]);
      }
    }
    $primaryAddressGeocodeParams = [
       'country' => 'CA',
       'street_address' => $values['work_address'][1],
       'city' => $values['city'][1],
       'postal_code' => $values['postal_code'][1],
       'state_province' => 'Ontario',
    ];
    try {
      $geocodeProvider = CRM_Utils_GeocodeProvider::getConfiguredProvider();
      $geocodeProvider->format($primaryAddressGeocodeParams);
      if (!empty($primaryAddressGeocodeParams['geo_code_error'])) {
        // Disabled for now until the geocoding api is fixed.
        // $errors['work_address[1]'] = E::ts('Unable to find this location on Google Maps. Please revise the address so that Google Maps understands it.');
      }
    }
    catch (Exception $e) {
    }

    $workLocationIds = [2, 3, 4, 5, 6, 7, 8, 9, 10, 11];
    foreach ($workLocationIds as $workRecordId) {
      if (!empty($values['phone'][$workRecordId]) || !empty($values['work_address'][$workRecordId]) || !empty($values['postal_code'][$workRecordId]) || !empty($values['city'][$workRecordId])) {
        foreach (['phone', 'work_address', 'postal_code', 'city'] as $field) {
          if (empty($values[$field][$workRecordId])) {
            $errors[$field . '[' . $workRecordId . ']'] = E::ts('Supplemental work location %1 %2 is a required field', [1 => ($workRecordId - 1), 2 => $addressFieldLables[$field]]);
          }
        }
        $supplementalAddressGeocodeParams = [
          'country' => 'CA',
          'street_address' => $values['work_address'][$workRecordId],
          'city' => $values['city'][$workRecordId],
          'postal_code' => $values['postal_code'][$workRecordId],
          'state_province' => 'Ontario',
        ];
        try {
          $geocodeProvider = CRM_Utils_GeocodeProvider::getConfiguredProvider();
          $geocodeProvider->format($supplementalAddressGeocodeParams);
          if (!empty($supplementalAddressGeocodeParams['geo_code_error'])) {
            // Disabled for now until the geocoding api is fixed.
            // $errors['work_address[' . $workRecordId . ']'] = E::ts('Unable to find this location on Google Maps. Please revise the address so that Google Maps understands it.');
          }
        }
        catch (Exception $e) {
        }
      }
    }
    if ($values['listing_type'] == 1 && count($setValues) > 1 ) {
      $errors[REGULATED_SERVICE_CF] = E::ts('You have selected more than one registered service');
    }
    if ($values['listing_type'] == 2 && count($setValues) > $staffMemberCount) {
      $errors[REGULATED_SERVICE_CF] = E::ts('Ensure you have entered all the staff members that match the registered services');
    }
    if (!empty($missingRegulators)) {
      $errors[REGULATED_SERVICE_CF] = E::ts('No Staff members have been entered for %1 regulated services', [1 => implode(', ', $missingRegulators)]);
    }

    if ($values['listing_type'] == 2 && empty($values['organization_name'])) {
      $errors['organization_name'] = E::ts('Need to supply the organization name');
    }
    if ($values['listing_type'] == 2 && empty($values['organization_email'])) {
      $errors['organization_email'] = E::ts('Need to supply the organization email');
    }
    if (empty($values['waiver_field'])) {
      $errors['waiver_field'] = E::ts('You must agree to the waivers in order to submit the application.');
    }
    return empty($errors) ? TRUE : $errors;
  }

  public function postProcess() {
    $formValues = array_merge($this->controller->exportValues($this->_name), $this->_submitValues);
    $this->set('formValues', $formValues);
    parent::postProcess();
  }

  public static function _getServieOptions() {
    $options = [];
    $customFieldAPI = civicrm_api3('Custom Field', 'getsingle', ['name' => 'Regulated_Services_Provided']);
    $dbOptions = civicrm_api3('OptionValue', 'get', ['option_group_id' => $customFieldAPI['option_group_id']]);
    foreach ($dbOptions['values'] as $optionValue) {
      $options[$optionValue['value']] = $optionValue['label'];
    }
    return $options;
  }

}
