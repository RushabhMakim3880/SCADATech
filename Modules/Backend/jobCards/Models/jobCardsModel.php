<?php

namespace Modules\Backend\jobCards\Models;

use CodeIgniter\Model;

class jobCardsModel extends Model
{
    protected $table      = 'productionJobCards';
    protected $primaryKey = 'jobId';

    protected $allowedFields = [
        'jobId',
        'tenantId',
        'itemRecipeId',
        'requiredQuantity',
        'completedQuantity',
        'status',
        'startedAt',
        'completedAt',
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
