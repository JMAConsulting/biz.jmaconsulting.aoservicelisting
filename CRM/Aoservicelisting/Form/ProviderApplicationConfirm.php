<?php

use CRM_Aoservicelisting_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Aoservicelisting_Form_ProviderApplicationConfirm extends CRM_Aoservicelisting_Form_ProviderApplication {

  public function buildQuickForm() {
    if (\Drupal::languageManager()->getCurrentLanguage()->getId() == 'fr') {
      CRM_Utils_System::setTitle('Demande d\'inscription au Répertoire des services d\'Autisme Ontario');
    }
    else {
      CRM_Utils_System::setTitle('Autism Ontario Service Listing Application');
    }
    CRM_Core_Resources::singleton()->addStyleFile('biz.jmaconsulting.aoservicelisting', 'css/providerconfirmstyle.css');
    $defaults = $this->get('formValues');
    $this->assign('REGULATED_SERVICES', REGULATED_SERVICE_CF);
    $this->assign('ABA_SERVICES', ABA_SERVICES);
    $this->assign('ABA_CREDENTIALS', ABA_CREDENTIALS);
    if (!empty($defaults[ABA_SERVICES])) {
      $this->assign('SHOW_ABA_SERVICES', 1);
    }
    else {
      $this->assign('SHOW_ABA_SERVICES', 0);
    }
    if (!empty($defaults[IS_REGULATED_SERVICE])) {
      $this->assign('SHOW_REGULATED_SERVICES', 1);
    }
    else {
      $this->assign('SHOW_REGULATED_SERVICES', 0);
    }
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
      $this->add('hidden', "staff_contact_id[$rowNumber]", NULL);
      $this->add('text', "staff_first_name[$rowNumber]", E::ts('First Name'), ['size' => 20, 'maxlength' => 32, 'class' => 'medium']);
      $this->add('text', "staff_last_name[$rowNumber]", E::ts('Last Name'), ['size' => 20, 'maxlength' => 32, 'class' => 'medium']);
      $this->add('text', "staff_record_regulator[$rowNumber]", E::ts('Record on Regulator\'s site'), ['size' => 20, 'maxlength' => 255, 'class' => 'medium']);
      $this->add('text', "aba_first_name[$rowNumber]", E::ts('First Name'), ['size' => 20, 'maxlength' => 32, 'class' => 'medium']);
      $this->add('text', "aba_last_name[$rowNumber]", E::ts('Last Name'), ['size' => 20, 'maxlength' => 32, 'class' => 'medium']);
      CRM_Core_BAO_CustomField::addQuickFormElement($this, CERTIFICATE_NUMBER . "[$rowNumber]", str_replace('custom_', '', CERTIFICATE_NUMBER), FALSE);
    }

    $this->buildCustom(PRIMARY_PROFILE, 'profile', TRUE);
    $this->buildCustom(SERVICELISTING_PROFILE1, 'profile1', TRUE);
    $this->buildCustom(SERVICELISTING_PROFILE2, 'profile2', TRUE);
    $this->buildCustom(SERVICELISTING_PROFILE3, 'profile3', TRUE);
    $this->assign('CERTIFICATE_NUMBER', CERTIFICATE_NUMBER);
    $this->assign('OTHER_LANGUAGE', OTHER_LANGUAGE);
    $this->assign('LANGUAGES', LANGUAGES);

    // populating camp values
    $customFields = civicrm_api3('CustomField', 'get', ['custom_group_id' => CAMP_CG])['values'];
    $campValues = [];
    $count = 1;
    $entryCount = 0;
    $totalCount = 21;
    while($count < $totalCount) {
      $entryFound = FALSE;
      foreach ($customFields as $customField) {
        $key = 'custom_' . $customField['id'];
        if (!empty($defaults[$key . '_-' . $count])) {
          $entryFound = TRUE;
        }
      }
      if ($entryFound) {
        $entryCount++;
      }
      $count++;
    }

    // this part is to render camp fields
    $campFields = [];
    for ($i = 1; $i <= $entryCount; $i++) {
      $campFields[$i] = [];
      foreach ($customFields as $customField) {
        // when we insert new value for multi-valued custom field the key is suppose to be in custom_xx_-1 otherwise custom_xx_1 where xx is the custom field id
        $key = 'custom_' . $customField['id'] . '_-' . $i;
        $campFields[$i][] = $key;
        CRM_Core_BAO_CustomField::addQuickFormElement($this, $key, $customField['id'], FALSE);
      }
    }
    $this->assign('campFields', $campFields);

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

  public function submit($values)
  {
    $this->processCustomValue($values);

    if (!empty($this->_loggedInContactID)) {
      // This is an edit, record before and after values here TODO.
      // For now, just adding an edit activity to the organization contact.
      $relationship = civicrm_api3('Relationship', 'get', [
        'contact_id_a' => $this->_loggedInContactID,
        'relationship_type_id' => PRIMARY_CONTACT_REL,
        'return' => 'contact_id_b',
      ]);
      if ($relationship['count'] > 0 && !empty($relationship['values'][$relationship['id']]['contact_id_b'])) {
        E::editActivity($relationship['values'][$relationship['id']]['contact_id_b']);
      }
    }

    $organization_params = [
      'organization_name' => empty($values['organization_name']) ? 'Self-employed ' . $values['primary_first_name'] . ' ' . $values['primary_last_name'] : $values['organization_name'],
      'email' => $values['organization_email'] ?: $values['email-Primary'],
    ];

    if (!empty($this->organizationId)) {
      $organization_params['id'] = $this->organizationId;
      // delete all camp session custom values if present
      $tableName = civicrm_api3('CustomGroup', 'getvalue', ['id' => CAMP_CG, 'return' => "table_name"]);
      CRM_Core_DAO::executeQuery("DELETE FROM $tableName WHERE entity_id = " . $this->organizationId);
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

    // Set status to submitted for first time this is submitted, else awaiting staff verification if edited.
    if (empty($this->organizationId) && empty($this->_loggedInContactID) && !empty($organization['id'])) {
      // Set status to submitted
      civicrm_api3('Contact', 'create', [
        'id' => $organization['id'],
        STATUS => "Submitted",
      ]);
    } elseif (!empty($this->organizationId) && !empty($this->_loggedInContactID)) {
      // Set status to offline verification.
      civicrm_api3('Contact', 'create', [
        'id' => $this->organizationId,
        STATUS => "Pending Approval",
      ]);
    }

    $addId = civicrm_api3('Address', 'get', [
      'contact_id' => $organization['id'],
      'is_primary' => 1,
      'return' => 'id',
    ]);

    list($add1Id, $addressParams1) = E::createAddress($values, 1, $organization['id'], CRM_Utils_Array::value('id', $addId));

    $addressIds = [0 => [$add1Id, $addressParams1]];

    E::createWebsite($organization['id'], $values['website']);

    $customValues = CRM_Core_BAO_CustomField::postProcess($values, $organization['id'], 'Organization');
    if (!empty($customValues) && is_array($customValues)) {
      CRM_Core_BAO_CustomValueTable::store($customValues, 'civicrm_contact', $organization['id']);
    }
    civicrm_api3('CustomValue', 'create', [
      WAIVER_FIELD => $values['waiver_field'],
      'entity_id' => $organization['id'],
    ]);


    $staffMemberIds = $abaStaffDone = [];
    $primaryContactFound = FALSE;
    $primaryContactId = 0;
    for ($rowNumber = 1; $rowNumber <= 20; $rowNumber++) {

      E::createPhone($organization['id'], $values['phone'][$rowNumber]);

      if ($rowNumber !== 1 && !empty($values['work_address'][$rowNumber])) {
        list($addressId, $addressParams) = E::createAddress($values, $rowNumber, $organization['id']);

        $addressIds[] = [$addressId, $addressParams];
      }
      E::endRelationship($values, $rowNumber, $organization['id']);

      if (!empty($values['staff_first_name'][$rowNumber])) {
        $individualParams = [
          'first_name' => $values['staff_first_name'][$rowNumber],
          'last_name' => $values['staff_last_name'][$rowNumber],
        ];
        if ($values['primary_first_name'] == $values['staff_first_name'][$rowNumber] &&
          $values['primary_last_name'] == $values['staff_last_name'][$rowNumber]) {
          $individualParams['email'] = $values['email-Primary'];
        }


        E::findDupes($values['staff_contact_id'][$rowNumber], $organization['id'], $individualParams);
        if (!empty($individualParams['email'])) {
          // Check for dupes for primary contact.
          if (empty($individualParams['contact_id'])) {
            $dedupeParams = CRM_Dedupe_Finder::formatParams($individualParams, 'Individual');
            $dedupeParams['check_permission'] = 0;
            $dupes = CRM_Dedupe_Finder::dupesByParams($dedupeParams, 'Individual', NULL, [], 12);
            $individualParams['contact_id'] = CRM_Utils_Array::value('0', $dupes, NULL);
          }
        }
        $individualParams['contact_type'] = 'Individual';
        $abaStaffMemberFound = FALSE;
        if (array_search($values['staff_first_name'][$rowNumber], $values['aba_first_name']) !== FALSE && array_search($values['staff_last_name'][$rowNumber], $values['aba_last_name']) !== FALSE) {
          // Check that we have found the same combination of first and last names
          if (array_search($values['staff_first_name'][$rowNumber], $values['aba_first_name']) == array_search($values['staff_last_name'][$rowNumber], $values['aba_last_name'])) {
            $arrayKey = array_search($values['staff_first_name'][$rowNumber], $values['aba_first_name']);
            $individualParams[CERTIFICATE_NUMBER] = $values[CERTIFICATE_NUMBER][$arrayKey];
            $abaStaffDone[] = $arrayKey;
            $abaStaffMemberFound = TRUE;
          }
        }
        $staffMember = civicrm_api3('Contact', 'create', $individualParams);
        $staffMemberIds[] = $staffMember['id'];

        E::createWebsite($staffMember['id'], $values['staff_record_regulator'][$rowNumber]);

        if ($rowNumber == 1) {
          // Create activity
          if (empty($this->_loggedInContactID)) {
            E::createActivity($organization['id']);
            // Send email on confirmation.
            E::sendMessage($staffMember['id'], RECEIVED_MESSAGE);
          }
          else {
            // We need to handle cases of email edit.
          }

          E::createPhone($staffMember['id'], CRM_Utils_Array::value('phone-Primary-6', $values));
        }

        E::createRelationship($staffMember['id'], $organization['id'], EMPLOYER_CONTACT_REL);

        if (!$primaryContactFound) {
          // Check if primary contact is the same as staff member 1
          if ($values['primary_first_name'] == $values['staff_first_name'][$rowNumber] &&
            $values['primary_last_name'] == $values['staff_last_name'][$rowNumber]
          ) {
            E::createRelationship($staffMember['id'], $organization['id'], PRIMARY_CONTACT_REL);
            E::createPhone($staffMember['id'], CRM_Utils_Array::value('phone-Primary-6', $values));
            $primaryContactFound = TRUE;
            $primaryContactId = $staffMember['id'];
          }
        }
      }
    }
    foreach ($staffMemberIds as $staffMemberId) {
      foreach ($addressIds as $key => $details) {
        $params = $details[1];
        unset($params['id']);
        $params['contact_id'] = $staffMemberId;
        $params['master_id'] = $details[0];
        $params['add_relationship'] = 0;
        $params['update_current_employer'] = 0;
        civicrm_api3('Address', 'create', $params);
      }
    }
    foreach ($values[CERTIFICATE_NUMBER] as $key => $certificateNumber) {
      if (!empty($certificateNumber) && !in_array($key, $abaStaffDone)) {
        $individualParams = [
          'first_name' => $values['aba_first_name'][$key],
          'last_name' => $values['aba_last_name'][$key],
          CERTIFICATE_NUMBER => $values[CERTIFICATE_NUMBER][$key],
          'contact_type' => 'Individual',
        ];
        if ($values['primary_first_name'] == $values['aba_first_name'][$key] &&
          $values['primary_last_name'] == $values['aba_last_name'][$key]) {
          $individualParams['email'] = $values['email-Primary'];
          $primaryContactFound = TRUE;
        }

        E::findDupes($values['aba_contact_id'][$key], $organization['id'], $individualParams);
        $abaMember = civicrm_api3('Contact', 'create', $individualParams);
        if ($primaryContactFound) {
          $primaryContactId = $abaMember['id'];
        }

        E::createRelationship($abaMember['id'], $organization['id'], EMPLOYER_CONTACT_REL);

        // create address
        $addressKey = $key - 1;
        if (!empty($addressIds[$addressKey])) {
          $params = $addressIds[$addressKey][1];
          unset($params['id']);
          $params['contact_id'] = $abaMember['id'];
          $params['master_id'] = $addressIds[$addressKey][0];
          $params['add_relationship'] = 0;
          $params['update_current_employer'] = 0;
          civicrm_api3('Address', 'create', $params);
        }

        // TODO :create website, Do we need to inherit website from the Staff N to ABA Staff N?
      }
    }
    if (!$primaryContactFound) {
      // Create the primary contact
      $primaryParams = [
         'first_name' => $values['primary_first_name'],
         'last_name' => $values['primary_last_name'],
         'contact_type' => 'Individual',
         'contact_sub_type' => PRIMARY_CONTACT_SUBTYPE,
         'email' => $values['email-Primary'],
      ];
      if (!empty($this->_loggedInContactID)) {
        $primaryParams['contact_id'] = $this->_loggedInContactID;
        $primaryContact = civicrm_api3('Contact', 'getsingle', [
          'id' => $this->_loggedInContactID,
          'contact_type' => 'Individual',
          'contact_sub_type' => PRIMARY_CONTACT_SUBTYPE,
          'return' => ['first_name', 'last_name', 'email'],
        ]);
        if ($primaryContact['first_name'] != $primaryParams['first_name'] &&
          $primaryContact['last_name'] != $primaryParams['last_name'] &&
          $primaryContact['email'] != $primaryParams['email']
        ) {
          civicrm_api3('Relationship', 'get', [
            'contact_id_a' => $this->_loggedInContactID,
            'contact_id_b' => $organization['id'],
            'relationship_type_id' => PRIMARY_CONTACT_REL,
            'api.relationship.delete' => '$value.id',
          ]);
        }
      }
      else {
        E::findDupes(NULL, $organization['id'], $primaryParams, PRIMARY_CONTACT_REL, TRUE);
        // Check for dupes for primary contact.
        if (empty($primaryParams['contact_id'])) {
          $dedupeParams = CRM_Dedupe_Finder::formatParams($primaryParams, 'Individual');
          $dedupeParams['check_permission'] = 0;
          $dupes = CRM_Dedupe_Finder::dupesByParams($dedupeParams, 'Individual', NULL, [], 12);
          $primaryParams['contact_id'] = CRM_Utils_Array::value('0', $dupes, NULL);
        }
      }
      $primId = civicrm_api3('Contact', 'create', $primaryParams)['id'];
      E::createRelationship($primId, $organization['id'], PRIMARY_CONTACT_REL);
      E::createPhone($primId, CRM_Utils_Array::value('phone-Primary-6', $values));
    }
    else {
      $primId = $primaryContactId;
    }
    if ($primId) {
      foreach ($addressIds as $key => $details) {
        $aparams = $details[1];
        unset($aparams['id']);
        $aparams['contact_id'] = $primId;
        $aparams['master_id'] = $details[0];
        $aparams['add_relationship'] = 0;
        $aparams['update_current_employer'] = 0;
        civicrm_api3('Address', 'create', $aparams);
      }
    }
    // Redirect to thank you page.
    if (\Drupal::languageManager()->getCurrentLanguage()->getId() == 'fr') {
      CRM_Utils_System::redirect(CRM_Utils_System::url('fr/civicrm/service-listing-thankyou', 'reset=1'));
    } else {
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/service-listing-thankyou', 'reset=1'));
    }
  }

}
