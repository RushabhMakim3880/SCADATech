<?php

namespace Modules\Backend\System\Models;

use CodeIgniter\Model;

class MenuConfigModel extends Model
{
    protected $table      = 'menuConfig';
    protected $primaryKey = 'menuConfigId';

    protected $allowedFields = [
        'tenantId',
        'orderId',
        'title',
        'icon',
        'class',
        'module',
        'attributes',
        'permissions',
        'url',
        'parentId',
        'menuLocation',
        'isPopup',
        'isActive',
        'isDeleted',
        'updatedBy',
        'updatedAt',
        'createdBy',
        'createdAt',
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
