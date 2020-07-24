<?php
// This file declares a managed database record of type "Job".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
return array (
  0 => 
  array (
    'name' => 'Cron:Aoservicelisting.VerifyListing',
    'entity' => 'Job',
    'params' => 
    array (
      'version' => 3,
      'name' => 'Call Aoservicelisting.VerifyListing API',
      'description' => 'Call Aoservicelisting.VerifyListing API',
      'run_frequency' => 'Daily',
      'api_entity' => 'Aoservicelisting',
      'api_action' => 'verify_listing',
      'parameters' => 'duration=6',
    ),
  ),
);
