<?php

namespace Modules\Backend\PlcMaster\Models;

use CodeIgniter\Model;

class PlcMasterModel extends Model
{
    protected $table      = 'plcMaster';
    protected $primaryKey = 'plcId';

    protected $allowedFields = [
        'plcId',
        'tenantId',
        'machineId',
        'plcName',
        'protocol',
        'ipAddress',
        'port',
        'modbusDeviceId',
        'description',
        'status',
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
