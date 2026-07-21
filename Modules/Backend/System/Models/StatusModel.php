<?php

namespace Modules\Backend\System\Models;

use CodeIgniter\Model;

class StatusModel extends Model
{
    protected $table      = 'statusMaster';
    protected $primaryKey = 'statusId';

    protected $allowedFields = [
        'tenantId',
        'moduleId',
        'statusName',
        'module',
        'statusType',
        'isDefaultEntry',
        'isEditable',
        'isAction',
        'isSystemManaged',
        'icon',
        'textColor',
        'bgColor',
        'Sequence',
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
