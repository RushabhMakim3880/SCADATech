<?php

namespace Modules\Backend\Setting\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table      = 'tenantCompanySettings';
    protected $primaryKey = 'tenantSettingId';

    protected $allowedFields = [
        'tenantSettingId',
        'tenantId',
        'key',
        'value',
    ];

    protected $useTimestamps = false;
    protected $createdField  = '';
    protected $updatedField  = '';

    protected $returnType = 'object';
    protected $useSoftDeletes = false;

    protected $validationMessages = [];
    protected $skipValidation     = false;
}
