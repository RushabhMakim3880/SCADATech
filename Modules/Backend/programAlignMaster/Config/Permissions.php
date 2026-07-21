<?php

$permissions = [];

$permissions['programAlignMaster'] = [
    'scope' => 'tenant',
    'permissions' => [
        'view',
        'manage',
    ]
];

return $permissions;