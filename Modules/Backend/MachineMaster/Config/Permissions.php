<?php

$permissions = [];

$permissions['MachineMaster'] = [
    'scope' => 'tenant',
    'permissions' => [
        'add',
        'edit',
        'view',
        'delete',
        'manage',
    ]
];

$permissions['MachineOperationConfig'] = [
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
