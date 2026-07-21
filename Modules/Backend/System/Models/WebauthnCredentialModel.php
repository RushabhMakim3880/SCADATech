<?php

namespace Modules\Backend\System\Models;

use CodeIgniter\Model;

class WebauthnCredentialModel extends Model
{
    protected $table = 'userMasterWebAuth';
    protected $primaryKey = 'webAuthId';
    protected $allowedFields = [
        'userId',
        'credentialId',
        'publicKey',
        'signCount',
        'fmt',
        'aaguid',
        'deviceName',
        'darkIcon',
        'lightIcon',
        'fingerprint',
        'lastUsedAt',
        'createdAt',
        'updatedAt'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'createdAt';
    protected $updatedField = 'updatedAt';
}
