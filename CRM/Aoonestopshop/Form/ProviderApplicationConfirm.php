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
    $this->add('text', 'organization_email', E::ts('Organization Email'));
    $this->add('text', 'website', E::ts('Website'));
    $this->add('text', 'primary_first_name', E::ts('First Name'));
    $this->add('text', 'primary_last_name', E::ts('Last Name'));
    $this->add('text', 'primary_email', E::ts('Email address'));
    $this->add('text', 'primary_phone_number', E::ts('Phone Number'));
    $this->add('text', 'primary_website', E::ts('Website'));
    $this->add('advcheckbox', 'display_name_public', E::ts('Display First Name and Last Name in public listing?'));
    $this->add('advcheckbox', 'display_email', E::ts('Display email address in public listing?'));
    $this->add('advcheckbox', 'display_phone', E::ts('Display phone number in public listing?'));

    for ($rowNumber = 1; $rowNumber <= 10; $rowNumber++) {
      $this->add('text', "phone[$rowNumber]", E::ts('Phone Number'), ['size' => 20, 'maxlength' => 32, 'class' => 'medium']);
      $this->add('text', "work_address[$rowNumber]", E::ts('Work Address'), ['size' => 45, 'maxlength' => 96, 'class' => 'huge']);
      $this->add('text', "postal_code[$rowNumber]", E::ts('Postal code'), ['size' => 20, 'maxlength' => 64, 'class' => 'medium']);
      $this->add('text', "city[$rowNumber]", E::ts('City/Town'), ['size' => 20, 'maxlength' => 64, 'class' => 'medium']);
      $this->add('text', "staff_first_name[$rowNumber]", E::ts('First Name'), ['size' => 20, 'maxlength' => 32, 'class' => 'medium']);
      $this->add('text', "staff_last_name[$rowNumber]", E::ts('Last Name'), ['size' => 20, 'maxlength' => 32, 'class' => 'medium']);
      $this->add('text', "staff_record_regulator[$rowNumber]", E::ts('Last Name'), ['size' => 20, 'maxlength' => 32, 'class' => 'medium']);
    }
    [861 => TRUE, 862 => TRUE, 863 => TRUE, 864 => TRUE, 865 => TRUE, 866 => FALSE, 867 => TRUE];
    foreach ($customFields as $id => $isRequired) {
      CRM_Core_BAO_CustomField::addQuickFormElement($this, "custom_{$id}", $id, $isRequired);
    }
    $this->assign('beforeStaffCustomFields', [861, 862, 863]);
    $this->assign('afterStaffCustomFields', [864, 865, 866, 867]);

    for ($row = 1; $row <= 20; $row++) {
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
    $options = $this->getColorOptions();
    CRM_Core_Session::setStatus(E::ts('You picked color "%1"', array(
      1 => $options[$values['favorite_color']],
    )));
    parent::postProcess();
  }

  public function getColorOptions() {
    $options = array(
      '' => E::ts('- select -'),
      '#f00' => E::ts('Red'),
      '#0f0' => E::ts('Green'),
      '#00f' => E::ts('Blue'),
      '#f0f' => E::ts('Purple'),
    );
    foreach (array('1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e') as $f) {
      $options["#{$f}{$f}{$f}"] = E::ts('Grey (%1)', array(1 => $f));
    }
    return $options;
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
