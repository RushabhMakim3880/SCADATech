<?php

namespace Modules\Backend\punchCounters\Models;

use CodeIgniter\Model;

class punchCountersModel extends Model
{
    protected $table      = 'punchCounters';
    protected $primaryKey = 'punchId';

    protected $allowedFields = [
        'punchId',
        'tenantId',
        'programId',
        'itemRecipeId',
        'punchCount',
        'startHour',
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
