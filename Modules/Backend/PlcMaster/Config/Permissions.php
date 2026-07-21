<?php

$permissions = [];

$permissions['PlcMaster'] = [
    'scope' => 'tenant',
    'permissions' => [
        'add',
        'edit',
        'view',
        'delete',
        'manage',
    ]
];

$permissions['PlcTagGroupMaster'] = [
    'scope' => 'tenant',
    'permissions' => [
        'add',
        'edit',
        'view',
        'delete',
        'manage',
    ]
];

$permissions['PlcTagMaster'] = [
    'scope' => 'tenant',
    'permissions' => [
        'add',
        'edit',
        'view',
        'delete',
        'manage',
    ]
];

return $permissions;
