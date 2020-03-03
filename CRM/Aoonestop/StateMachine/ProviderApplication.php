<?php
/*
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC. All rights reserved.                        |
 |                                                                    |
 | This work is published under the GNU AGPLv3 license with some      |
 | permitted exceptions and without any warranty. For full license    |
 | and copyright information, see https://civicrm.org/licensing       |
 +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 */

/**
 * State machine for managing different states of the Import process.
 *
 */
class CRM_Aoonestop_StateMachine_ProviderApplication extends CRM_Core_StateMachine {

  /**
   * Class constructor.
   *
   * @param CRM_Core_Controller $controller
   * @param \const|int $action
   *
   * @return CRM_Contribute_StateMachine_Contribution
   */
  public function __construct($controller, $action = CRM_Core_Action::NONE) {
    parent::__construct($controller, $action);

    $this->_pages = [
      'CRM_Aoonestop_Form_ProviderApplicationForm' => NULL,
      'CRM_Aoonestop_Form_ProviderApplicationConfirm' => NULL,
    ];

    $this->addSequentialPages($this->_pages, $action);
  }

}
