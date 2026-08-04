<?php

namespace Modules\Backend\Setting\Models;

use CodeIgniter\Model;

class CompanyMasterSettingsModel extends Model
{
    protected $table      = 'companyMasterSettings';
    protected $primaryKey = 'companySettingsId';

    protected $allowedFields = [
        'companySettingsId',
        'tenantId',
        'serialNo',
        'companyId',
        'key',
        'value',
    ];

    protected $returnType     = 'object';
    protected $useSoftDeletes = false;
}
