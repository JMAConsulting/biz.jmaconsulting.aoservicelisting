<?php
return [
  0 => [
    'name' => 'AuthorizedContactType',
    'entity' => 'ContactType',
    'update' => 'never',
    'params' => [
      'version' => 3,
      'label' => 'Authorized Contact',
      'name' => 'authorized_contact',
      'parent_id' => 'Individual',
      'is_active' => 1,
      'is_reserved' => 1,
    ],
  ],
];
