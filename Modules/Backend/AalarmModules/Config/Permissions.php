<?php

$permissions = [];

$permissions['AalarmModules'] = [
    'scope' => 'tenant',
    'permissions' => [
        'add',
        'edit',
        'view',
        'delete',
        'manage',
    ]

];

$permissions['AlarmLog'] = [
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
