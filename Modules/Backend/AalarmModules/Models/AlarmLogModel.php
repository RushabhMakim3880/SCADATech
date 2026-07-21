<?php

namespace Modules\Backend\AalarmModules\Models;

use CodeIgniter\Model;

class AlarmLogModel extends Model
{
    protected $table      = 'AlarmLog';
    protected $primaryKey = 'logId';

    protected $allowedFields = [
        'logId',
        'tenantId',
        'alarmId',
        'uiTagId',
        'alarmType',
        'triggerValue',
        'triggerTime',
        'resolveTime',
        'isActive',

    ];

    protected $useTimestamps = false;
    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';

    protected $returnType = 'object';
    protected $useSoftDeletes = false;


    protected $validationMessages = [];
    protected $skipValidation     = false;
}
