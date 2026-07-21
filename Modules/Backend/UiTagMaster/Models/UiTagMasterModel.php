<?php

namespace Modules\Backend\UiTagMaster\Models;

use CodeIgniter\Model;

class UiTagMasterModel extends Model
{
    protected $table      = 'uiTagMaster';
    protected $primaryKey = 'uiTagId';

    protected $allowedFields = [
        'uiTagId',
        'tenantId',
        'tagId',
        'tagGroupId',
        'tagName',
        'minValue',
        'maxValue',
        'isActive',
        'updatedAt',
        'updatedBy',
        'createdAt',
        'createdBy',

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
