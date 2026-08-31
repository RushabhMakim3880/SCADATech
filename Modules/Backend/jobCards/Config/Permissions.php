<?php

$permissions = [];

$permissions['jobCards'] = [
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
