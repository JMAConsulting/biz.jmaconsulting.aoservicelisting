<?php

use CRM_Aoservicelisting_ExtensionUtil as E;

class CRM_Aoservicelisting_Form_Settings extends CRM_Admin_Form_Generic {

  public function postProcess() {
    $params = $this->controller->exportValues($this->_name);
    Civi::settings()->set('aoservicelisting_form_pre_help', $params['aoservicelisting_form_pre_help']);
    Civi::settings()->set('aoservicelisting_form_pre_help_fr', $params['aoservicelisting_form_pre_help_fr']);
    CRM_Core_Session::setStatus(E::ts('Service Listing form settings saved'), E::ts('Settings Saved'), 'success');
  }

}
