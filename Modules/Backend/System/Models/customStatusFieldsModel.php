<?php

namespace Modules\Backend\System\Models;

use CodeIgniter\Model;

class customStatusFieldsModel extends Model
{
    protected $table      = 'customStatusFields';
    protected $primaryKey = 'fieldId';

    protected $allowedFields = [
        'tenantId',
        'statusId',
        'fieldName',
        'fieldType',
        'isDefaultEntry',
        'fieldOptions',
        'isRequired',
        'isActive',
        'isDeleted',
        'updatedAt',
        'updatedBy',
        'createdAt',
        'createdBy'
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';

    protected $returnType = 'object';
    protected $useSoftDeletes = false;

    protected $validationMessages = [];
    protected $skipValidation     = false;
}
