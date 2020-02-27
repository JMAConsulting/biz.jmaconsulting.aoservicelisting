<?php

use CRM_Aoonestopshop_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Aoonestopshop_Form_ProviderApplicationForm extends CRM_Aoonestopshop_Form_ProviderApplication {
  public function buildQuickForm() {

    $defaults = [];

    $serviceProviderOptions = [1 => E::ts('Individual'), 2 => E::ts('Organization')];
    $this->addRadio('provider_type', E::ts('Type of Service Provider'), $serviceProviderOptions);
    $defaults['provider_type'] = 1;
    $this->add('text', 'organization_name', E::ts('Organization Name'));
    $this->add('text', 'organization_email', E::ts('Organization Email'));
    
    $this->add('text', 'primary_first_name', E::ts('First Name'));
    $this->add('text', 'primary_last_name', E::ts('Last Name'));
    $this->add('text', 'primary_email', E::ts('Email address'));
    $this->add('text', 'primary_phone_number', E::ts('Phone Number'));
    $this->add('text', 'primary_website', E::ts('Website'));
    $this->add('advcheckbox', 'display_name_public', E::ts('Display First Name and Last Name in public listing?'));
    $defaults['display_name_public'] = 1;
    $this->add('advcheckbox', 'display_email', E::ts('Display email address in public listing?'));
    $defaults['display_email'] = 1;
    $this->add('advcheckbox', 'display_phone', E::ts('Display phone number in public listing?'));
    $defaults['display_phone'] = 1;
    
    for ($rowNumber = 1; $rowNumber <= 10; $rowNumber++) {
      $this->add('text', "phone[$rowNumber]", E::ts('Phone Number'), ['size' => 20, 'maxlength' => 32, 'class' => 'medium']);
      $this->add('text', "work_address[$rowNumber]", E::ts('Work Address'), ['size' => 45, 'maxlength' => 96, 'class' => 'huge']);
      $this->add('text', "postal_code[$rowNumber]", E::ts('Postal code'), ['size' => 20, 'maxlength' => 64, 'class' => 'medium']);
      $this->add('text', "city[$rowNumber]", E::ts('City/Town'), ['size' => 20, 'maxlength' => 64, 'class' => 'medium']);
      $this->add('text', "staff_first_name[$rowNumber]", E::ts('First Name'), ['size' => 20, 'maxlength' => 32, 'class' => 'medium']);
      $this->add('text', "staff_last_name[$rowNumber]", E::ts('Last Name'), ['size' => 20, 'maxlength' => 32, 'class' => 'medium']);
      $this->add('text', "staff_record_regulator[$rowNumber]", E::ts('Last Name'), ['size' => 20, 'maxlength' => 32, 'class' => 'medium']);
    }
    $customFields = [861 => TRUE, 862 => TRUE, 863 => TRUE, 864 => TRUE, 865 => TRUE, 866 => FALSE, 867 => TRUE];
    foreach ($customFields as $id => $isRequired) {
      CRM_Core_BAO_CustomField::addQuickFormElement($this, "custom_{$id}", $id, $isRequired);
    }
    $this->assign('beforeStaffCustomFields', [861, 862, 863]);
    $this->assign('afterStaffCustomFields', [864, 865, 866, 867]);
    $defaults['custom_866'] = [1 => 1, 2 => 1, 3 => 1, 4 => 1];

    for ($row = 1; $row <= 20; $row++) {
      CRM_Core_BAO_CustomField::addQuickFormElement($this, "custom_858[$row]", 858, FALSE);
      CRM_Core_BAO_CustomField::addQuickFormElement($this, "custom_859[$row]", 859, FALSE);
      CRM_Core_BAO_CustomField::addQuickFormElement($this, "custom_860[$row]", 860, FALSE);
    }

    $this->setDefaults($defaults);
    $this->_elements;
    $this->addButtons(array(
      array(
        'type' => 'upload',
        'name' => E::ts('Continue'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    $this->addFormRule(['CRM_Aoonestopshop_Form_ProviderApplicationForm', 'providerFormRule']);
    parent::buildQuickForm();
  }

  public function providerFormRule($values) {
    $errors = [];
    if ($values['provider_type'] == 1 && empty($values['display_name_public'])) {
      $errors['display_name_public'] = E::ts('Provider first name and last name must be displayed in public listing');
    }
    if ($values['provider_type'] == 1 && empty($values['display_email']) && empty($values['display_phone'])) {
      $errors['display_email'] = E::ts('At least one of email or phone must be provided and made public');
    }
    if ($values['provider_type'] == 1 && empty($values['primary_phone_number']) && empty($values['primary_email'])) {
      $errors['primary_phone_number'] = E::ts('At least one of email or phone must be provided and made public');
    }
    $addressFieldLables = ['phone' => E::ts('Work Phone Number'), 'work_address' => E::ts('Work Address'), 'postal_code' => E::ts('Postal code'), 'city' =>  E::ts('City/Town'), 'postal_code' =>  E::ts('Postal code')];
    foreach (['phone', 'work_address', 'postal_code', 'city', 'postal_code'] as $addressField) {
      if (empty($values[$addressField][1])) {
        $errors[$addressField . '[1]'] = E::ts('Need to supply %1', [1 => $addressFieldLables[$addressField]]);
      }
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

}
