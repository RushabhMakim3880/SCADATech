<?php

namespace App\Models;

use CodeIgniter\Model;

class TempLinkModel extends Model
{
    protected $table = 'tempLinks';
    protected $primaryKey = 'id';
    protected $allowedFields = ['token', 'originalUrl', 'payload', 'payloadHash', 'expiresAt', 'createdAt'];
}
