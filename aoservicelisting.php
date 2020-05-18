<?php

require_once 'aoservicelisting.civix.php';
use CRM_Aoservicelisting_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/ 
 */
function aoservicelisting_civicrm_config(&$config) {
  _aoservicelisting_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function aoservicelisting_civicrm_xmlMenu(&$files) {
  _aoservicelisting_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function aoservicelisting_civicrm_install() {
  _aoservicelisting_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function aoservicelisting_civicrm_postInstall() {
  _aoservicelisting_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function aoservicelisting_civicrm_uninstall() {
  _aoservicelisting_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function aoservicelisting_civicrm_enable() {
  _aoservicelisting_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function aoservicelisting_civicrm_disable() {
  _aoservicelisting_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function aoservicelisting_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _aoservicelisting_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function aoservicelisting_civicrm_managed(&$entities) {
  _aoservicelisting_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function aoservicelisting_civicrm_caseTypes(&$caseTypes) {
  _aoservicelisting_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function aoservicelisting_civicrm_angularModules(&$angularModules) {
  _aoservicelisting_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function aoservicelisting_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _aoservicelisting_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function aoservicelisting_civicrm_entityTypes(&$entityTypes) {
  _aoservicelisting_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function aoservicelisting_civicrm_themes(&$themes) {
  _aoservicelisting_civix_civicrm_themes($themes);
}

function aoservicelisting_civicrm_alterCustomFieldDisplayValue(&$displayValue, $value, $entityId, $fieldInfo) {
  if ('custom_' . $fieldInfo['id'] == LISTING_URL) {
    $displayValue = html_entity_decode("<a href='$value'>" . CRM_Contact_BAO_Contact::displayName($entityId) . "</a>");
  }
}

function aoservicelisting_civicrm_preProcess($formName, &$form) {
  if ($formName == "CRM_Contact_Form_Contact") {
    if (!empty($form->_contactId) && count(preg_grep('/^' . STATUS . '_[\d]*/', array_keys($form->_submitValues))) > 0) {
      $form->_oldStatus = civicrm_api3('Contact', 'getvalue', ['return' => STATUS, 'id' => $form->_contactId]);
    }
  }
  if ($formName == "CRM_Contact_Form_Inline_CustomData") {
    if (!empty($form->_submitValues['cid']) && count(preg_grep('/^' . STATUS . '_[\d]*/', array_keys($form->_submitValues))) > 0) {
      $form->_oldStatus = civicrm_api3('Contact', 'getvalue', ['return' => STATUS, 'id' => $form->_contactId]);
    }
  }
}

  /**
   * Implementation of hook_civicrm_postProcess
   *
   * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postProcess
   */
  function aoservicelisting_civicrm_postProcess($formName, &$form) {
    if ($formName == "CRM_Contact_Form_Contact") {
      if (!empty($form->_contactId) && count(preg_grep('/^' . STATUS . '_[\d]*/', array_keys($form->_submitValues))) > 0) {
        E::setStatus($form->_oldStatus, $form->_contactId, $form->_submitValues);
      }
    }
    if ($formName == "CRM_Contact_Form_Inline_CustomData") {
      if (!empty($form->_submitValues['cid']) && count(preg_grep('/^' . STATUS . '_[\d]*/', array_keys($form->_submitValues))) > 0) {
        E::setStatus($form->_oldStatus, $form->_submitValues['cid'], $form->_submitValues);
      }
    }
  }

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 *
function aoservicelisting_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 *
function aoservicelisting_civicrm_navigationMenu(&$menu) {
  _aoservicelisting_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _aoservicelisting_civix_navigationMenu($menu);
} // */
