<?php

$permissions = [];

$permissions['customStatusFields'] = [
    'scope' => 'saas',
    'permissions' => [
        'add',
        'edit',
        'view',
    ]
];

$permissions['locationMaster'] = [
    'scope' => 'saas',
    'permissions' => [
        'add',
        'edit',
        'view',
    ]
];

$permissions['statusMaster'] = [
    'scope' => 'saas',
    'permissions' => [
        'add',
        'edit',
        'view',
    ]
];

$permissions['superSaasAdmin'] = [
    'scope' => 'saas',
    'permissions' => [
        'sampleDashboard',
        'dynamicDashboard',
        'manageBranding',
        'dynamicDashboard',
        'manageMenuConfig',
        'manageAppConfig',
        'manageBranding',
        'manageLocationMaster',
        'manageStatus',
        'manageCustomStatusFields',
        'view',
        'uploadapk',
    ]
];

$permissions['tenantMaster'] = [
    'scope' => 'saas',
    'permissions' => [
        'add',
        'edit',
        'view',
        'viewAll',
    ]
];

$permissions['userMaster'] = [
    'scope' => 'tenant',
    'permissions' => [
        'add',
        'edit',
        'viewOwn',
        'viewAll',
        'managePermission',
        'manageApprovedDevices',
    ]
];

return $permissions;
