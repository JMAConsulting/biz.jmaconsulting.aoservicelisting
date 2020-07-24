<?php
use CRM_Aoservicelisting_ExtensionUtil as E;

/**
 * Aoservicelisting.VerifyListing API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_aoservicelisting_verify_listing_spec(&$spec) {
  $spec['duration']['api.required'] = 1;
}

/**
 * Aoservicelisting.VerifyListing API
 *
 * @param array $params
 *
 * @return array
 *   API result descriptor
 *
 * @see civicrm_api3_create_success
 *
 * @throws API_Exception
 */
function civicrm_api3_aoservicelisting_verify_listing($params) {
  if (array_key_exists('duration', $params)) {
    $returnValues = E::verifyListing($params['duration']);
    return civicrm_api3_create_success($returnValues, $params, 'Aoservicelisting', 'VerifyListing');
  }
  else {
    throw new API_Exception(/*error_message*/ 'Please enter a duration from the approval date for which you would like to to send the verification notification', /*error_code*/ 'duration_incorrect');
  }
}
