<?php

$permissions = [];

$permissions['productionMaster'] = [
    'scope' => 'tenant',
    'permissions' => [
        'view',
        'operate',
    ]
];

return $permissions;
