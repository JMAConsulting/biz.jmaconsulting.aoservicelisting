<?php
use CRM_Aoservicelisting_ExtensionUtil as E;

class CRM_Aoservicelisting_Page_ProviderApplicationThankYou extends CRM_Core_Page {

  public function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(E::ts('Thank you for submitting your application'));

    parent::run();
  }

}
