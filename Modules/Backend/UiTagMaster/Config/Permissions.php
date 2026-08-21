<?php

$permissions = [];

$permissions['UiTagMaster'] = [
    'scope' => 'tenant',
    'permissions' => [
        'add',
        'edit',
        'view',
        'manage',
        'writeTag',
    ]
];

return $permissions;
