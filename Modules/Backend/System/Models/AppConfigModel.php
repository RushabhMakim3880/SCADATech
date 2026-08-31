<?php

namespace Modules\Backend\System\Models;

use CodeIgniter\Model;

class AppConfigModel extends Model
{
    protected $table      = 'appConfig';
    protected $primaryKey = 'appconfigId';

    protected $allowedFields = [
        'fieldId',
        'fieldValue',
    ];

    protected $useTimestamps = false;
    protected $createdField  = '';
    protected $updatedField  = '';

    protected $returnType = 'object';
    protected $useSoftDeletes = false;

    protected $validationMessages = [];
    protected $skipValidation     = false;
}
