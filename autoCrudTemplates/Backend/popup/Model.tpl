<?php

namespace Modules\Backend\{{MODULE_NAME}}\Models;

use CodeIgniter\Model;

class {{MODULE_NAME}}Model extends Model
{
    protected $table      = '{{TABLE_NAME}}';
    protected $primaryKey = '{{PRIMARY_FIELD}}';

    protected $allowedFields = [
        {{MODEL_ALLOWED_FIELDS}}
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
