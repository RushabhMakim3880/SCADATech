<?php

namespace Modules\Backend\productionMasters\Models;

use CodeIgniter\Model;

class productionMastersModel extends Model
{
    protected $table      = 'productionMaster';
    protected $primaryKey = 'productionId';

    protected $allowedFields = [
        'productionId',
        'tenantId',
        'programId',
        'jobId',
        'quantityProduced',
        'startedAt',
        'completedAt',
        'userId',
        
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
