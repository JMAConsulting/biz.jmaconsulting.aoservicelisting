<?php

use CRM_Aoonestopshop_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Aoonestopshop_Form_ProviderApplicationForm extends CRM_Core_Form {
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
    $this->add('advCheckBox', 'display_name_public', E::ts('Display First Name and Last Name in public listing?'));
    $defaults['display_name_public'] = 1;
    $this->add('advCheckBox', 'display_email', E::ts('Display email address in public listing?'));
    $defaults['display_email'] = 1;
    $this->add('advCheckBox', 'display_phone', E::ts('Display phone number in public listing?'));
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
    
    $this->setDefaults($defaults);
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => E::ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
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
