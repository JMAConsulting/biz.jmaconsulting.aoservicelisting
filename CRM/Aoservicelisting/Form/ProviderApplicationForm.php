<?php

use CRM_Aoservicelisting_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Aoservicelisting_Form_ProviderApplicationForm extends CRM_Aoservicelisting_Form_ProviderApplication {
  public function buildQuickForm() {

    CRM_Core_Resources::singleton()->addStyleFile('biz.jmaconsulting.aoservicelisting', 'css/providerformstyle.css');

    $defaults = [];

    $loggedInContactId = $this->getLoggedInUserContactID();
    if (!empty($loggedInContactId)) {
      $relationship = civicrm_api3('Relationship', 'get', [
        'contact_id_a' => $loggedInContactId,
        'relationship_type_id' => 74,
      ]);
      if (!empty($relationship['count'])) {
        $this->organizationId = $relationship['values'][$relationship['id']]['contact_id_b'];
        $this->set('organizationId', $relationship['values'][$relationship['id']]['contact_id_b']);
        $organization = civicrm_api3('Contact', 'getsingle', [
          'id' => $this->organizationId,
          'return' => ['organization_name', 'custom_861', 'custom_862', 'custom_863', 'custom_864', 'custom_865', 'custom_866', 'custom_867', 'custom_868', 'custom_869', 'custom_870', 'email'],
        ]);
        $primrayContact = civicrm_api3('Contact', 'getsingle', [
          'id' => $loggedInContactId,
        ]);
        $primaryContactPhone = civicrm_api3('Phone', 'getsingle', ['contact_id' => $loggedInContactId, 'is_primary' => 1]);
        $defaults['primary_first_name'] = $primrayContact['first_name'];
        $defaults['primary_last_name'] = $primrayContact['last_name'];
        $defaults['staff_first_name[1]'] = $primrayContact['first_name'];
        $defaults['staff_last_name[1]'] = $primrayContact['last_name'];
        $defaults['phone[1]'] = $primaryContactPhone['phone'];
        $primaryStaffWebsite = civicrm_api3('Website', 'get', ['contact_id' => $primrayContact['id'], 'is_active' => 1, 'url' => ['IS NOT NULL' => 1]]);
        if (!empty($primaryStaffWebsite['count'])) {
          $defaults['staff_record_regulator[1]'] = $primaryStaffWebsite['values'][$primaryStaffWebsite['id']]['url'];
        }
        foreach (['organization_name', 'custom_861', 'custom_862', 'custom_863', 'custom_864', 'custom_865', 'custom_866', 'custom_867', 'custom_868', 'custom_869', 'custom_870', 'email'] as $field) {
          if ($field === 'organization_name' && stristr($organization[$field], 'self-employed') === FALSE) {
            $defaults['listing_type'] = 2;
          }
          else {
            $defaults['listing_type'] = 1;
          }
          if ($field === 'email') {
            $defaults['organization_email'] = $organization[$field];
          }
          if ($field === 'custom_865' || $field == 'custom_866' || $field === 'custom_863') {
            $selctedOptions = [];
            foreach ($organization[$field] as $option) {
              $selctedOptions[$option] = 1;
            }
            $defaults[$field] = $selctedOptions;
          }
          elseif ($field === 'custom_868') {
            $defaults['display_name_public'] = $organization[$field];
          }
          elseif ($field === 'custom_869') {
            $defaults['display_email'] = $organization[$field];
          }
          elseif ($field === 'custom_870') {
            $defaults['display_phone'] = $organization[$field];
          }
          else {
            $defaults[$field] = $organization[$field];
          }
        }
        $primrayWorkAddress = civicrm_api3('Address', 'getsingle', ['contact_id' => $this->organizationId, 'is_primary' => 1]);
        $defaults['work_address[1]'] = $primrayWorkAddress['street_address'];
        $defaults['postal_code[1]'] = $primrayWorkAddress['postal_code'];
        $defaults['city[1]'] = $primrayWorkAddress['city'];
        $priamryEmailAddress = civicrm_api3('Email', 'getsingle', ['contact_id' => $this->organizationId, 'is_primary' => 1]);
        $defaults['primary_email'] = $priamryEmailAddress['email'];
        $primaryWebsite = civicrm_api3('Website', 'get', ['contact_id' => $this->organizationId, 'url' => ['IS NOT NULL' => 1], 'sequential' => 1]);
        $defaults['website'] = $primaryWebsite['values'][0]['url'];
        $primaryWorkPhone = civicrm_api3('Phone', 'getsingle', ['contact_id' => $this->organizationId, 'is_primary' => 1]);
        $defaults['primary_phone_number'] = $primaryWorkPhone['phone'];
        // Get details of the other staff members
        $staffMembers = civicrm_api3('Relationship', 'get', [
          'contact_id_b' => $this->organizationId,
          'contact_id_a' => ['!=' => $loggedInContactId],
          'sequential' => 1,
        ]);
        $staffRowCount = $campRowCount = 1;
        if (!empty($staffMembers['count'])) {
          foreach ($staffMembers['values'] as $staffMember) {
            $staffMemberContactId = $staffMember['contact_id_a'];
            $staffDetails = civicrm_api3('Contact', 'getsingle', ['id' => $staffMemberContactId]);
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
    $serviceListingOptions = [1 => E::ts('Individual'), 2 => E::ts('Organization')];
    $listingTypeField = $this->addRadio('listing_type', E::ts('Type of Service Listing'), $serviceListingOptions);
    $organizationNameField = $this->add('text', 'organization_name', E::ts('Organization Name'));
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

    $this->buildCustom(SERVICELISTING_PROFILE1, 'profile1');
    $this->buildCustom(SERVICELISTING_PROFILE2, 'profile2');
    $this->assign('customDataType', 'Organization');
    $this->assign('customDataSubType', 'Service Listing');
    $this->assign('entityID', $this->organizationId);
    $this->assign('groupID', CAMP_CG);
    /**
    $customFields = [861 => TRUE, 862 => TRUE, 863 => FALSE, 864 => TRUE, 865 => TRUE, 866 => FALSE, 867 => TRUE];
    foreach ($customFields as $id => $isRequired) {
      CRM_Core_BAO_CustomField::addQuickFormElement($this, "custom_{$id}", $id, $isRequired);
    }
    $this->assign('beforeStaffCustomFields', [861, 862, 863]);
    $this->assign('afterStaffCustomFields', [864, 865, 866, 867]);


    if (empty($this->organizationId)) {
      $defaults['display_phone'] = 1;
      $defaults['display_email'] = 1;
      $defaults['display_name_public'] = 1;
      $defaults['listing_type'] = 1;
      $defaults['custom_866'] = [1 => 1, 2 => 1, 3 => 1, 4 => 1];
    }
    else {
      $listingTypeField->freeze();
      $organizationNameField->freeze();
    }

    for ($row = 1; $row <= 21; $row++) {
      CRM_Core_BAO_CustomField::addQuickFormElement($this, "custom_858[$row]", 858, FALSE);
      CRM_Core_BAO_CustomField::addQuickFormElement($this, "custom_859[$row]", 859, FALSE);
      CRM_Core_BAO_CustomField::addQuickFormElement($this, "custom_860[$row]", 860, FALSE);
    }
*/
    $this->setDefaults($defaults);
    $this->addButtons(array(
      array(
        'type' => 'upload',
        'name' => E::ts('Continue'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    //$this->assign('elementNames', $this->getRenderableElementNames());
    $this->addFormRule(['CRM_Aoservicelisting_Form_ProviderApplicationForm', 'providerFormRule']);
    parent::buildQuickForm();
  }

  public function providerFormRule($values) {
    $errors = $setValues = [];
    $regulatorRecordKeys = $verifiedURLCounter = [];
    $staffMemberCount = 0;
    $regulatorUrlMapping = [
      1 => 'caslpo.com',
      2 => 'cco.on.ca',
      3 => 'ontariocampsassociation.ca',
      4 => 'oacyc.org',
      5 => 'cdho.org',
      6 => 'rcdso.org',
      7 => 'members.dietitians.ca',
      7 => 'collegeofdietitians.org',
      8 => 'college-ece.ca',
      9 => 'collegeoftrades.ca',
      10 => 'coko.ca',
      11 => 'cmto.com',
      12 => 'coto.org',
      13 => 'collegeoptom.on.ca',
      14 => 'coptont.org',
      15 => 'cpso.on.ca',
      16 => 'collegept.org',
      17 => 'ccpa-accp.ca',
      17 => 'psych.on.ca',
      17 => 'cpo.on.ca',
      18 => 'eopa.ca',
      19 => 'findasocialworker.ca',
      19 => 'ocwssw.org',
      20 => 'osla.on.ca',
      20 => 'caslpo.com',
      21 => 'oct.ca',
    ];
    foreach ($values['custom_863'] as $value => $checked) {
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
            $verifiedURLCounter[$serviceValue] = 0;
            $urls[] = $regulatorUrlMapping[$serviceValue];
          }
        }
        if (!empty($urls)) {
          foreach ($urls as $url) {
            if (!$regulatedUrlValidated && stristr($value, $url) !== FALSE) {
              $serviceValueFound = array_search($url, $regulatorUrlMapping);
              $verifiedURLCounter[$serviceValue] = $verifiedURLCounter[$serviceValue] + 1;
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
        foreach ($verifiedURLCounter as $value => $counter) {
          if (empty($counter)) {
            $options = self::_getServieOptions();
            $missingRegulators[] = $options[$value];
          }
        }
      }
    }
    if ($values['listing_type'] == 1 && empty($values['display_name_public'])) {
      $errors['display_name_public'] = E::ts('first name and last name of listing must be publicly displayed');
    }
    if ($values['listing_type'] == 1 && empty($values['display_email']) && empty($values['display_phone'])) {
      $errors['display_email'] = E::ts('At least one of email or phone must be provided and made public');
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
      $errors['custom_863'] = E::ts('You have selected more than one registered service');
    }
    if ($values['listing_type'] == 2 && count($setValues) > $staffMemberCount) {
      $errors['custom_863'] = E::ts('Ensure you have entered all the staff members that match the registered services');
    }
    if (!empty($missingRegulators)) {
      $errors['custom_863'] = E::ts('No Staff members have been entered for %1 regulated services', [1 => implode(', ', $missingRegulators)]);
    }
    if ($values['listing_type'] == 2 && empty($values['organization_name'])) {
      $errors['organization_name'] = E::ts('Need to supply the organization name');
    }
    if ($values['listing_type'] == 2 && empty($values['organization_email'])) {
      $errors['organization_email'] = E::ts('Need to supply the organization email');
    }
    if (!empty($values['custom_862']) && empty($setValues)) {
      $errors['custom_863'] = E::ts('You must select at least one registered service');
    }
    if (empty($values['primary_first_name'])) {
      $errors['primary_first_name'] = E::ts('First name of the primary contact is a required field.');
    }
    if (empty($values['primary_last_name'])) {
      $errors['primary_last_name'] = E::ts('Last name of the primary contact is a required field.');
    }
    if (empty($values['waiver_field'])) {
      $errors['waiver_field'] = E::ts('You must agree to the waivers in order to submit the application.');
    }
    if (empty($values['website'])) {
      $errors['website'] = E::ts('Website is a required field.');
    }
    return empty($errors) ? TRUE : $errors;
  }

  public function postProcess() {
    $formValues = $this->controller->exportValues($this->_name);
    $this->set('formValues', $formValues);
    parent::postProcess();
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

  public static function _getServieOptions() {
    $options = [];
    $customFieldAPI = civicrm_api3('Custom Field', 'getsingle', ['name' => 'Regulated_Services_Provided']);
    $dbOptions = civicrm_api3('OptionValue', 'get', ['option_group_id' => $customFieldAPI['option_group_id']]);
    foreach ($dbOptions['values'] as $optionValue) {
      $options['value'] = $options['label'];
    }
    return $options;
  }

}
