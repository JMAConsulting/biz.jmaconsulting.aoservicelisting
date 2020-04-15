<?php
return [
  0 => [
    'name' => 'ServiceProviderCustomData',
    'entity' => 'CustomGroup',
    'update' => 'never',
    'params' => [
      'name' => 'service_provider_details',
      'title' => 'Service Listing Details',
      'extends' => 'Organization',
      'extends_entity_column_value' => 'service_provider',
      'style' => 'Inline',
      'is_active' => 1,
      'is_public' => 1,
      'collapse_adv_display' => 1,
      'is_reserved' => 0,
    ],
  ],
  1 => [
    'name' => 'ServiceProviderEvents',
    'entity' => 'CustomGroup',
    'update' => 'never',
    'params' => [
      'name' => 'service_provider_events',
      'title' => 'Service Listing Events',
      'extends' => 'Organization',
      'extends_entity_column_value' => 'service_provider',
      'style' => 'Tab',
      'is_active' => 1,
      'is_public' => 1,
      'collapse_adv_display' => 1,
      'is_reserved' => 0,
      'is_multiple' => 1,
    ],
  ],
];
