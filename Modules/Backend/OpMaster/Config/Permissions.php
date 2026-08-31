<?php

$permissions = [];

$permissions['OpMaster'] = [
    'scope' => 'tenant',
    'permissions' => [
        'view',
        'operate',
    ]
];

$permissions['OpHoming'] = [
    'scope' => 'tenant',
    'permissions' => [
        'machinePos',
        'proxyWear',
        'homePos',
    ]
];

$permissions['OpSettings'] = [
    'scope' => 'tenant',
    'permissions' => [
        'distance',
        'autoSpeed',
        'manualMaxSpeed',
        'safetyDistances',
        'punchSelection',
        'proxyDelayTime',
        'servoTime',
        'accumulator',
        'marking',
        'princher',
        'temperature',
        'inFeed',
        'general',
    ]
];


return $permissions;
