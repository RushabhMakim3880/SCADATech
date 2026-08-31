<?php

namespace Modules\Backend\Samples\Models;

use CodeIgniter\Model;

class sampleModel extends Model
{
    protected $table      = 'newSampleTable';
    protected $primaryKey = 'newSampleId';

    protected $allowedFields = [
        'newSampleId',
        'tenantId',
        'sampleDate',
        'newSampleName',
        'priority',
        'price',
        'locationId',
        'colorCode',
        'iconCode',
        'isActive',
        'category',
        'timepicker',
        'dateTime',
        'checkboxes',
        'radios',
        'simpleDropdown',
        'simpleDropdownMultiple',
        'description',
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

    // Optionally, define validation rules
    // protected $validationRules    = [
    //     'username' => 'required|alpha_numeric|min_length[3]|is_unique[userMaster.username]',
    //     'email'    => 'required|valid_email|is_unique[userMaster.email]',
    //     'password' => 'required|min_length[6]',
    // ];

    protected $validationMessages = [];
    protected $skipValidation     = false;
}
