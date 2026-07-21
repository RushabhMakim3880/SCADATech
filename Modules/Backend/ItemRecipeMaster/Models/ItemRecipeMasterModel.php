<?php

namespace Modules\Backend\ItemRecipeMaster\Models;

use CodeIgniter\Model;

class ItemRecipeMasterModel extends Model
{
    protected $table      = 'itemRecipeMaster';
    protected $primaryKey = 'itemRecipeId';

    protected $allowedFields = [
        'itemRecipeId',
        'tenantId',
        'itemCode',
        'description',
        'sideAWidth',
        'sideBWidth',
        'sideAThickness',
        'sideBThickness',
        'material', 
        'programLength', 
        'cutRadius',
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
