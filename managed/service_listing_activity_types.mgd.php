<?php
return [
  0 => [
    'name' => 'ServiceListingCreatedActivityType',
    'entity' => 'OptionValue',
    'update' => 'never',
    'params' => [
      'option_group_id' => 'activity_type',
      'label' => 'Service listing created',
      'name' => 'service_listing_created',
      'is_reserved' => 1,
    ],
  ],
  1 => [
    'name' => 'ServiceListingEditedActivityType',
    'entity' => 'OptionValue',
    'update' => 'never',
    'params' => [
      'option_group_id' => 'activity_type',
      'label' => 'Service listing edited',
      'name' => 'service_listing_edited',
      'is_reserved' => 1,
    ],
  ],
  2 => [
    'name' => 'ServiceListingEditedActivityType',
    'entity' => 'OptionValue',
    'update' => 'never',
    'params' => [
      'option_group_id' => 'activity_type',
      'label' => 'Service listing status changed',
      'name' => 'provider_status_changed',
      'is_reserved' => 1,
    ],
  ],
  3 => [
    'name' => 'ServiceListingSubmissionActivityType',
    'entity' => 'OptionValue',
    'update' => 'never',
    'params' => [
      'option_group_id' => 'activity_type',
      'label' => 'Service listing submission',
      'name' => 'service_listing_submission',
      'is_reserved' => 1,
    ],
  ],
];
