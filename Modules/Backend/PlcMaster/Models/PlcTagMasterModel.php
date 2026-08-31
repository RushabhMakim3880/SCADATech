<?php

namespace Modules\Backend\PlcMaster\Models;

use CodeIgniter\Model;

class PlcTagMasterModel extends Model
{
    protected $table      = 'plcTagMaster';
    protected $primaryKey = 'tagId';

    protected $allowedFields = [
        'tagId',
        'tenantId',
        'plcId',
        'tagName',
        'tagAddress',
        'dataType',
        'registerType',
        'readWrite',
        'scaleFactor',
        'offset',
        'unit',
        'description',
        'isActive',
        'createdBy',
        'createdAt',
        'updatedBy',
        'updatedAt',

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
