<?php

$permissions = [];

$permissions['punchCounters'] = [
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
