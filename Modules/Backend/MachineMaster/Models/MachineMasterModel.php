<?php

namespace Modules\Backend\MachineMaster\Models;

use CodeIgniter\Model;

class MachineMasterModel extends Model
{
    protected $table      = 'machineMaster';
    protected $primaryKey = 'machineId';

    protected $allowedFields = [
        'machineId',
        'tenantId',
        'machineCode',
        'machineName',
        'machineType',
        'location',
        'headCount',
        'barMaxLength',
        'barUom',
        'description',
        'isActive',
        'createdAt',
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
