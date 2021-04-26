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
    // Get profile defaults set.
    $fields = CRM_Core_BAO_UFGroup::getFields(PRIMARY_PROFILE, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->organizationId, $fields, $defaults, TRUE);
    $fields = CRM_Core_BAO_UFGroup::getFields(SERVICELISTING_PROFILE1, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->organizationId, $fields, $defaults, TRUE);
    $fields = CRM_Core_BAO_UFGroup::getFields(SERVICELISTING_PROFILE2, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->organizationId, $fields, $defaults, TRUE);
    $fields = CRM_Core_BAO_UFGroup::getFields(SERVICELISTING_PROFILE3, FALSE);
    CRM_Core_BAO_UFGroup::setProfileDefaults($this->organizationId, $fields, $defaults, TRUE);

    if (empty($this->organizationId)) {
      $defaults['listing_type'] = 1;
    }

    if (!empty($this->_loggedInContactID)) {
      if (!empty($this->organizationId)) {
        // If we have an org id and we are logged in prefill based on already submitted data.
        $staffMemberIds = [$this->_loggedInContactID];
        $organization = civicrm_api3('Contact', 'getsingle', [
          'id' => $this->organizationId,
          'return' => ['organization_name', 'email'],
        ]);
        $primaryContact = civicrm_api3('Contact', 'getsingle', [
          'id' => $this->_loggedInContactID,
          'return' => ['id', 'first_name', 'last_name', CERTIFICATE_NUMBER, REGULATED_URL]
        ]);
        $primaryContactPhone = civicrm_api3('Phone', 'getsingle', ['contact_id' => $this->_loggedInContactID, 'is_primary' => 1]);
        $regulatorUrlPresent = (!empty($primaryContact[REGULATED_URL]));
        $staffRowCount = $abaStaffCount = $addressRowCount = 1;
        //If the primary contact has a regulated url variable set also set the first regulated staff details to match that of the primary contact.
        if ($regulatorUrlPresent) {
          $defaults['staff_first_name['. $staffRowCount . ']'] = $defaults['primary_first_name'] = $primaryContact['first_name'];
          $defaults['staff_last_name['. $staffRowCount . ']'] = $defaults['primary_last_name'] = $primaryContact['last_name'];
          $defaults['staff_contact_id['. $staffRowCount . ']'] = $this->_loggedInContactID;
          $defaults['staff_record_regulator[' . $staffRowCount . ']'] = $primaryContact[REGULATED_URL];
          $staffRowCount++;
        }
        // if the primary contact has a ABA certificate field set then prefill the first ABA staff member details with teh primary contact details. 
        if (!empty($primaryContact[CERTIFICATE_NUMBER])) {
          $defaults['aba_first_name[' . $abaStaffCount . ']'] = $defaults['primary_first_name'] = $primaryContact['first_name'];
          $defaults['aba_last_name[' . $abaStaffCount . ']'] = $defaults['primary_last_name'] = $primaryContact['last_name'];
          $defaults['aba_contact_id[' . $abaStaffCount . ']'] = $this->_loggedInContactID;
          $defaults[CERTIFICATE_NUMBER . '[' . $abaStaffCount . ']'] = $primaryContact[CERTIFICATE_NUMBER];
          $abaStaffCount++;
        }
        // If the primary contact does not have a regulated URL or an ABA certificate then only fill in the primary contact field information
        if (!$regulatorUrlPresent && empty($primaryContact[CERTIFICATE_NUMBER])) {
          $defaults['primary_first_name'] = $primaryContact['first_name'];
          $defaults['primary_last_name'] = $primaryContact['last_name'];
        }
        // Set organization field defaults and listing type default.
        foreach (['organization_name',  'email'] as $field) {
          if ($field === 'organization_name') {
            if (stristr($organization[$field], 'self-employed') === FALSE) {
              $defaults['listing_type'] = 2;
              $this->listingType = 2;
            }
            else {
              $defaults['listing_type'] = 1;
              $this->listingType = 1;
            }
          }
          if ($field === 'email') {
            $defaults['organization_email'] = $organization[$field];
          }

          $defaults[$field] = $organization[$field];
        }
        $phoneIds = $addressRowsMissing = [];
        // Fill in the address fields starting with the primary address for the first row
        $organizationAddreses = civicrm_api3('Address', 'get', ['contact_id' => $this->organizationId, 'options' => ['sort' => "is_primary DESC"], 'return' => ['street_address', 'postal_code', 'city', PHONE_ID_CUSTOM_FIELD]])['values'];
        if (!empty($organizationAddreses)) {
          foreach ($organizationAddreses as $orgAddress) {
            $defaults['work_address[' . $addressRowCount . ']'] = $orgAddress['street_address'];
            $defaults['postal_code[' . $addressRowCount . ']'] = $orgAddress['postal_code'];
            $defaults['city[' . $addressRowCount . ']'] = $orgAddress['city'];
            // If the address is linked to a phone use that phone id to fill in the phone field in this row.
            if (!empty($orgAddress[PHONE_ID_CUSTOM_FIELD])) {
              $phone = civicrm_api3('Phone', 'get', ['id' => $orgAddress[PHONE_ID_CUSTOM_FIELD], 'sequential' => 1])['values'];
              if (!empty($phone)) {
                $defaults['phone[' . $addressRowCount . ']'] = $phone[0]['phone'];
                $phoneIds[] = $orgAddress[PHONE_ID_CUSTOM_FIELD];
              }
            }
            else {
              $addressRowsMissing[] = $addressRowCount;
            }
            $addressRowCount++;
          }
        }
        // If we have address field rows missing phones lets just start by filling in with any phones from the service listing contact
        if (!empty($addressRowsMissing)) {
          $phoneParams = [
            'contact_id' => $this->organizationId,
            'options' => ['sort' => "is_primary DESC"],
          ];
          // If we have already used some ids to populate the form exclude those.
          if (!empty($phoneIds)) {
            $phoneParams['id'] = ['NOT IN' => $phoneIds];
          }
          $addressRowKey = 0;
          $phones = civicrm_api3('Phone', 'get', $phoneParams)['values'];
          if (!empty($phones)) {
            foreach ($phones as $phone) {
              if (!empty($addressRowsMissing[$addressRowKey])) {
                $defaults['phone[' . $addressRowsMissing[$addressRowKey] . ']'] = $phone['phone'];
                $addressRowKey++;
              }
            }
          }
        }
        $primaryWebsite = civicrm_api3('Website', 'get', ['contact_id' => $this->organizationId, 'url' => ['IS NOT NULL' => 1], 'sequential' => 1]);
        if (!empty($primaryWebsite['values'][0]['url'])) {
          $defaults['website'] = $primaryWebsite['values'][0]['url'];
        }
        // Get details of the other staff members other than the primary contact. 
        $staffMembers = civicrm_api3('Relationship', 'get', [
          'contact_id_b' => $this->organizationId,
          'contact_id_a' => ['!=' => $this->_loggedInContactID],
          'is_active'  => 1,
          'sequential' => 1,
          'relationship_type_id' => EMPLOYER_CONTACT_REL,
          'return' => ['contact_id_a'],
        ]);
        if (!empty($staffMembers['count'])) {
          foreach ($staffMembers['values'] as $staffMember) {
            $staffMemberContactId = $staffMember['contact_id_a'];
            $staffDetails = civicrm_api3('Contact', 'getsingle', ['id' => $staffMemberContactId, 'return' => [REGULATED_URL, CERTIFICATE_NUMBER, 'first_name', 'last_name']]);
            $regulatorUrlPresent = (!empty($staffDetails[REGULATED_URL]));
            $certificateNumberPresent = (!empty($staffDetails[CERTIFICATE_NUMBER]));
            // If the staff member has a regulated url field then fill in the details of the next avaliable regulated staff member key
            if ($regulatorUrlPresent) {
              $staffMemberIds[] = $staffMemberContactId;
              $defaults['staff_contact_id[' . $staffRowCount . ']'] = $staffMember['contact_id_a'];
              $defaults['staff_first_name[' . $staffRowCount . ']'] = $staffDetails['first_name'];
              $defaults['staff_last_name[' . $staffRowCount . ']'] = $staffDetails['last_name'];
              $defaults['staff_record_regulator[' . $staffRowCount . ']'] = $staffDetails[REGULATED_URL];
              $staffRowCount++;
            }
            // If the staff member has a aba certificate field valid then fill in the details of the next avaliable regulated aba member key
            if ($certificateNumberPresent) {
              $defaults['aba_contact_id[' . $abaStaffCount . ']'] = $staffMember['contact_id_a'];
              $defaults['aba_first_name[' . $abaStaffCount . ']'] = $staffDetails['first_name'];
              $defaults['aba_last_name[' . $abaStaffCount . ']'] = $staffDetails['last_name'];
              $defaults[CERTIFICATE_NUMBER . '[' . $abaStaffCount . ']'] = $staffDetails[CERTIFICATE_NUMBER];
              $abaStaffCount++;
            }
          }
        }
      }
    }
    return $defaults;
  }

  public function buildQuickForm() {
    if (\Drupal::languageManager()->getCurrentLanguage()->getId() == 'fr') {
      CRM_Utils_System::setTitle('Demande d\'inscription au CommunityConnect, d\'Autisme Ontario');
      if (!empty($this->_loggedInContactID)) {
        $this->assign('preFormText', Civi::settings()->get('aoservicelisting_form_pre_help_fr'));
      }
    }
    else {
      CRM_Utils_System::setTitle('Autism Ontario CommunityConnect Application Form');
      if (!empty($this->_loggedInContactID)) {
        $this->assign('preFormText', Civi::settings()->get('aoservicelisting_form_pre_help'));
      }
    }

    // Prevent setting defaults for URLs on edit mode.
    if (empty($this->_loggedInContactID)) {
      $this->assign('isCreate', TRUE);
    }
    else {
      $this->assign('isCreate', FALSE);
    }

    $attr = empty($this->organizationId) ? [] : ['readonly' => TRUE];
    $serviceListingOptions = [1 => E::ts('Individual'), 2 => E::ts('Organization')];
    $listingTypeField = $this->addRadio('listing_type', E::ts('Type of Service Listing'), $serviceListingOptions, $attr);
    $organizationNameField = $this->add('text', 'organization_name', E::ts('Organization Name'), $attr);
    $this->add('email', 'organization_email', E::ts('Organization Email'));
    $this->add('text', 'website', E::ts('Website'), NULL);
    $this->addRule('website', E::ts('Enter a valid web address beginning with \'http://\' or \'https://\'.'), 'url');
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
      $this->add('text', "staff_record_regulator[$rowNumber]", E::ts('Record on regulator\'s site'), ['size' => 20, 'maxlength' => 255, 'class' => 'medium']);
      $this->addRule("staff_record_regulator[$rowNumber]", E::ts('Enter a valid web address beginning with \'http://\' or \'https://\'.'), 'url');
      $this->add('hidden', "aba_contact_id[$rowNumber]", NULL);
      $this->add('text', "aba_first_name[$rowNumber]", E::ts('First Name'), ['size' => 20, 'maxlength' => 32, 'class' => 'medium']);
      $this->add('text', "aba_last_name[$rowNumber]", E::ts('Last Name'), ['size' => 20, 'maxlength' => 32, 'class' => 'medium']);
      CRM_Core_BAO_CustomField::addQuickFormElement($this, CERTIFICATE_NUMBER . "[$rowNumber]", str_replace('custom_', '', CERTIFICATE_NUMBER), FALSE);
    }

    $this->buildCustom(PRIMARY_PROFILE, 'profile');
    $this->buildCustom(SERVICELISTING_PROFILE1, 'profile1');
    $this->buildCustom(SERVICELISTING_PROFILE2, 'profile2');
    $this->buildCustom(SERVICELISTING_PROFILE3, 'profile3');
    $this->assign('REGULATED_SERVICE_CF', REGULATED_SERVICE_CF);
    $this->assign('IS_REGULATED_SERVICE', IS_REGULATED_SERVICE);
    $this->assign('OTHER_LANGUAGE', OTHER_LANGUAGE);
    $this->assign('LANGUAGES', LANGUAGES);
    // Commented out since we are not pre populating URLs
    // $this->assign('regulator_services', json_encode(CRM_Core_OptionGroup::values('regulator_url_mapping')));
    $this->assign('ABA_SERVICES', ABA_SERVICES);
    $this->assign('ABA_CREDENTIALS', ABA_CREDENTIALS);
    $this->assign('CERTIFICATE_NUMBER', CERTIFICATE_NUMBER);

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
    CRM_Core_Error::debug_var('formvaluesbeforesubmit', $values);
    $errors = $setValues = [];
    $regulatorRecordKeys = $verifiedURLCounter = [];
    $staffMemberCount = $abaStaffMemberCount = 0;

    // Check email for primary contact to see if existing in the database.
    if (!empty($values['email-Primary'])) {
      $isProvider = CRM_Core_DAO::singleValueQuery('SELECT e.email FROM civicrm_email e INNER JOIN civicrm_contact c ON c.id = e.contact_id WHERE e.email LIKE %1 AND c.is_deleted <> 1 AND c.contact_sub_type LIKE \'%authorized_contact%\'', [1 => [$values['email-Primary'], 'String']]);
      if (!empty($isProvider)) {
        $errors['email-Primary'] = E::ts("A person with this email address has already submitted a CommunityConnect Application. Please contact servicelisting@autismontario.com for more information.");
      }
    }
    $regulatorUrlMapping = CRM_Core_OptionGroup::values('regulator_url_mapping');

    // Check primary contact first and last name.
    if (empty($values['primary_first_name'])) {
      $errors['primary_first_name'] = E::ts('First name of the primary contact is a required field');
    }
    if (empty($values['primary_last_name'])) {
      $errors['primary_last_name'] = E::ts('Last name of the primary contact is a required field');
    }

    // ABA Services
    if (!empty($values[ABA_SERVICES])) {
      foreach ($values[ABA_CREDENTIALS] as $value => $checked) {
        if ($checked && $value !== 'None') {
          $setAbaValues[] = $value;
        }
      }
      // Check if no aba credentials are checked.
      if (empty($setAbaValues) && empty($values[ABA_CREDENTIALS]['None'])) {
        $errors[ABA_CREDENTIALS] = E::ts('Credentials held is a required field');
      }
    }

    if (!empty($values[IS_REGULATED_SERVICE])) {
      foreach ($values[REGULATED_SERVICE_CF] as $value => $checked) {
        if ($checked) {
          $setValues[] = $value;
        }
      }
      // Check if no services are checked.
      if (empty($setValues)) {
        $errors[IS_REGULATED_SERVICE] = E::ts('Regulated services provided is a required field when you say you provide regulated services');
      }
    }
    $urls = [];
    foreach ($setValues as $serviceValue) {
      if (!empty($regulatorUrlMapping[$serviceValue])) {
        if (array_key_exists($serviceValue, $verifiedURLCounter) === FALSE) {
          $verifiedURLCounter[$serviceValue] = 0;
        }
        $urls[] = $regulatorUrlMapping[$serviceValue];
      }
    }
    foreach ($values['staff_record_regulator'] as $key => $value) {
      if (!empty($value)) {
        $regulatorRecordKeys[$key] = 1;
        $staffMemberCount++;
        if (stristr($value, 'ontariocampsassociation.ca') === FALSE) {
          if (empty($values['staff_first_name'][$key]) || empty($values['staff_last_name'][$key])) {
            if (empty($values['staff_first_name'][$key])) {
              $errors['staff_first_name' . '[' . $key . ']'] = E::ts('First name of the regulated staff member is required');
            }
            if (empty($values['staff_last_name'][$key])) {
              $errors['staff_last_name' . '[' . $key . ']'] = E::ts('Last name of the regulated staff member is required');
            }
          }
        }
        $regulatedUrlValidated = FALSE;
        if (!empty($urls)) {
          foreach ($urls as $url) {
            $parts = (array) explode(',', $url);
            $entryFound = FALSE;
            foreach ($parts as $url) {
              if (stristr($value, $url) !== FALSE) {
                $entryFound = TRUE;
                break;
              }
            }
            if (!$regulatedUrlValidated && $entryFound) {
              foreach ($regulatorUrlMapping as $k => $val) {
                if (stristr($val, $url) !== FALSE) {
                  $verifiedURLCounter[$k] = $verifiedURLCounter[$k] + 1;
                  $regulatedUrlValidated = TRUE;
                  unset($regulatorRecordKeys[$key]);
                }
              }
            }
          }
        }
        else {
          unset($regulatorRecordKeys[$key]);
        }
        // If any urls have not matched show an error.
        if (!empty($regulatorRecordKeys)) {
          foreach ($regulatorRecordKeys as $rowKey => $val) {
            $errors['staff_record_regulator[' . $rowKey . ']'] = E::ts('The link you provided for “record on regulator’s site” does not match any listed regulated professions on file.');
          }
        }
      }
    }
    $options = self::_getServiceOptions();
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

    $addressFieldLables = ['phone' => E::ts('phone number'), 'work_address' => E::ts('address'), 'postal_code' => E::ts('postal code'), 'city' =>  E::ts('city/town')];
    foreach (['phone', 'work_address', 'postal_code', 'city', 'postal_code'] as $addressField) {
      if (empty($values[$addressField][1])) {
        $errors[$addressField . '[1]'] = E::ts('Primary work location %1 is a required field.', [1 => $addressFieldLables[$addressField]]);
      }
    }
    $primaryAddressGeocodeParams = [
       'country' => 'CAN',
       'street_address' => $values['work_address'][1],
       'city' => $values['city'][1],
       'postal_code' => $values['postal_code'][1],
       'state_province' => 'Ontario',
    ];
    try {
      $geocodeProvider = CRM_Utils_GeocodeProvider::getConfiguredProvider();
      $geocodeProvider->format($primaryAddressGeocodeParams);
      if (($primaryAddressGeocodeParams['geo_code_1'] == 'null' && $primaryAddressGeocodeParams['geo_code_2'] == 'null') || !empty($primaryAddressGeocodeParams['geo_code_error'])) {
        $errors['work_address[1]'] = E::ts('Unable to find this location on Google Maps. Please revise the address');
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
          'country' => 'CAN',
          'street_address' => $values['work_address'][$workRecordId],
          'city' => $values['city'][$workRecordId],
          'postal_code' => $values['postal_code'][$workRecordId],
          'state_province' => 'Ontario',
        ];
        try {
          $geocodeProvider = CRM_Utils_GeocodeProvider::getConfiguredProvider();
          $geocodeProvider->format($supplementalAddressGeocodeParams);
          if (($primaryAddressGeocodeParams['geo_code_1'] == 'null' && $primaryAddressGeocodeParams['geo_code_2'] == 'null') || !empty($supplementalAddressGeocodeParams['geo_code_error'])) {
            $errors['work_address[' . $workRecordId . ']'] = E::ts('Unable to find this location on Google Maps. Please revise the address');
          }
        }
        catch (Exception $e) {
        }
      }
    }
    $flag = FALSE;
    if ($values['listing_type'] == 1 && count($setValues) > 1 ) {
      $errors[REGULATED_SERVICE_CF] = E::ts('You have selected more than one registered service');
      $flag = TRUE;
    }
    if ($values['listing_type'] == 2 && count($setValues) > $staffMemberCount) {
      $errors[REGULATED_SERVICE_CF] = E::ts('Ensure you have entered all the staff members that match the registered services');
      $flag = TRUE;
    }
    if (!empty($missingRegulators) && !$flag) {
      $errors[REGULATED_SERVICE_CF] = E::ts('Either no staff members have been entered or no URLs have been entered as a record for regulated profession for %1 regulated services', [1 => implode(', ', $missingRegulators)]);
    }

    // Check ABA credentials
    foreach ($values[CERTIFICATE_NUMBER] as $key => $value) {
      if (!empty($value)) {
        $abaStaffMemberCount++;
        if (empty($values['aba_first_name'][$key])) {
          $errors['aba_first_name' . '[' . $key . ']'] = E::ts('First Name of ABA staff member is a required field');
        }
        if (empty($values['aba_last_name'][$key])) {
          $errors['aba_last_name' . '[' . $key . ']'] = E::ts('Last Name of ABA staff member is a required field');
        }
        if (empty($values[CERTIFICATE_NUMBER][$key])) {
          $errors[CERTIFICATE_NUMBER . '[' . $key . ']'] = E::ts('Certificate number is a required field');
        }
      }
    }
    if ($values['listing_type'] == 1 && count($setAbaValues) > 1 ) {
      $errors[ABA_CREDENTIALS] = E::ts('You have selected more than one ABA credential');
    }
    if ($values['listing_type'] == 2 && count($setAbaValues) > $abaStaffMemberCount) {
      $errors[ABA_CREDENTIALS] = E::ts('Please ensure that you have entered all the staff members that match the ABA credentials');
    }
    $credentials = [];
    if (!empty($values[ABA_CREDENTIALS])) {
      foreach ($values[ABA_CREDENTIALS] as $value => $set) {
        if (!empty($set)) {
          if ($value !== 'None') {
            $credentials[] = $value;
          }
          elseif (!empty($credentials)) {
            $errors[ABA_CREDENTIALS] = E::ts('\'None of the above\' must be the only option selected');
          }
        }
      }
    }
    if ($values['listing_type'] == 2 && empty($values['organization_name'])) {
      $errors['organization_name'] = E::ts('Organization name is a required field');
    }
    if ($values['listing_type'] == 2 && empty($values['organization_email'])) {
      $errors['organization_email'] = E::ts('Organization email is a required field');
    }
    if (empty($values['waiver_field'])) {
      $errors['waiver_field'] = E::ts('You must agree to the waivers in order to submit the application.');
    }
    return empty($errors) ? TRUE : $errors;
  }

  public function postProcess() {
    // We recet the page here so that the confirm page always gets the latest data from the submission of the form.
    $this->controller->resetPage('ProviderApplicationConfirm');
    $formValues = array_merge($this->controller->exportValues($this->_name), $this->_submitValues);
    CRM_Core_Error::debug_var('formvaluesaftersubmit', $formValues);
    $this->set('formValues', $formValues);
    parent::postProcess();
  }

  public static function _getServiceOptions() {
    $options = [];
    $customFieldAPI = civicrm_api3('Custom Field', 'getsingle', ['name' => 'Regulated_Services_Provided']);
    $dbOptions = civicrm_api3('OptionValue', 'get', ['option_group_id' => $customFieldAPI['option_group_id']]);
    foreach ($dbOptions['values'] as $optionValue) {
      $options[$optionValue['value']] = $optionValue['label'];
    }
    return $options;
  }

}
