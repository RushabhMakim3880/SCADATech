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
        'homingSpeed',
        'proxyWear',
        'wear',
        'homePosition',
    ]
];

$permissions['OpSettings'] = [
    'scope' => 'tenant',
    'permissions' => [
        'distance',
        'punchSelection',
        'barDetails',
        'scrapDetails',
        'princherSpeed',
        'servoAccSettings',
        'oilTemperature',
        'lubSettings',
        'masterSettings',
        'accumulatorPressureSettings',
        'markingPressureSettings',
        'princherPressureSettings',
        'inFeedPressureSettings',
    ]
];

$permissions['OpAuto'] = [
    'scope' => 'tenant',
    'permissions' => [
        'autoSpeed',
    ]
];

return $permissions;
