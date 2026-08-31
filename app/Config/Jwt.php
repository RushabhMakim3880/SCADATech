<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Jwt extends BaseConfig
{
    public $secretKey;
    public $algorithm;
    public $expireTime;
    public $longExpireTime;
    public $refreshExpireTime;

    public function __construct()
    {
        parent::__construct();

        // Load JWT configurations from .env
        $this->secretKey = getenv('jwt.secretKey') ?: 'default-secret-key';
        $this->algorithm = getenv('jwt.algorithm') ?: 'HS256';
        $this->expireTime = getenv('jwt.expireTime') ?: 3600;

        //with remember me option
        $this->longExpireTime = getenv('jwt.longExpireTime') ?: 3600 * 24 * 30;

        //without remember me option
        $this->refreshExpireTime = getenv('jwt.refreshExpireTime') ?: 3600 * 24 * 7;
    }
}
