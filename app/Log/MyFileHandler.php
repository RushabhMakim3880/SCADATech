<?php

namespace App\Log;

use CodeIgniter\Log\Handlers\FileHandler;
use App\Libraries\Tenant;

class MyFileHandler extends FileHandler
{
    public function handle($level, $message): bool
    {
        // Use tenantId instead of subdomain
        $tenantId = Tenant::id() ?? 0;

        // Optional: Include controller in log name
        $controller = service('request')->getUri()->getSegment(1) ?? 'index';

        $logDir = WRITEPATH . 'logs/' . $tenantId . '/';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $this->path = $logDir . $tenantId . '_' . $controller . '_';

        return parent::handle($level, $message);
    }
}
