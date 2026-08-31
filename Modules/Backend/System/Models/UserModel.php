<?php

namespace Modules\Backend\System\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'userMaster';
    protected $primaryKey = 'userId';

    protected $allowedFields = [
        'tenantId',
        'serialNo',
        'username',
        'password',
        'firstName',
        'lastName',
        'email',
        'mobile',
        'groupId',
        'singleSignonToken',
        '2FaToken',
        'resetPasswordToken',
        'failedAttempts',
        'lockoutUntil',
        'passwordExpiryTime',
        'isActive',
        'lastLoginTime',
        'lastActiveTime',
        'updatedBy',
        'createdBy',
        'updatedAt',
        'createdAt'
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';

    protected $returnType = 'object';
    protected $useSoftDeletes = false;

    // Optionally, define validation rules
    // protected $validationRules    = [
    //     'username' => 'required|alpha_numeric|min_length[3]|is_unique[userMaster.username]',
    //     'email'    => 'required|valid_email|is_unique[userMaster.email]',
    //     'password' => 'required|min_length[6]',
    // ];

    protected $validationMessages = [];
    protected $skipValidation     = false;
}
