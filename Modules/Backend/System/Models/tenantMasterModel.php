<?php

namespace Modules\Backend\System\Models;

use CodeIgniter\Model;

class tenantMasterModel extends Model
{
    protected $table      = 'tenantMaster';
    protected $primaryKey = 'tenantId';

    protected $allowedFields = [
        'tenantId',
        'subDomain',
        'customDomain',
        'tenantName',
        'companyName',
        'mobile',
        'email',
        'companyAddress',
        'locationId',
        'tenantType',
        'isActive',
        'createdBy',
        'createdOn',
        'updatedBy',
        'updatedOn'
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'createdOn';
    protected $updatedField  = 'updatedOn';

    protected $returnType = 'object';
    protected $useSoftDeletes = false;

    protected $validationMessages = [];
    protected $skipValidation     = false;
}
