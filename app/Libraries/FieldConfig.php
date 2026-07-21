<?php

namespace App\Libraries;

use Config\Services;
use Config\Database;
use App\Libraries\Auth;

class FieldConfig
{
    protected $tenantId;
    protected $groupId;
    protected $db;
    protected static $cache = [];

    public function __construct()
    {
        $this->db = Database::connect();
        $user = Auth::user();

        $this->tenantId = $user->tenantId ?? 0; // Default to 0 if tenantId is not set
        $this->groupId  = $user->groupId ?? 0; // Default to 0 if groupId is not set
    }

    public function getField(string $moduleName, string $fieldKey): object
    {
        $cacheKey = $this->tenantId . '__' . $moduleName . '__' . $fieldKey;
        if (isset(self::$cache[$cacheKey])) {
            return self::$cache[$cacheKey];
        }

        $config = (object)[
            'visible' => 1,
            'required' => 0,
            'masked' => 0,
        ];

        $control = $this->db->table('fieldControls')
            ->where('tenantId', $this->tenantId)
            ->where('moduleName', $moduleName)
            ->where('fieldKey', $fieldKey)
            ->get()
            ->getRow();

        if ($control) {
            if ($control->isVisible !== null) {
                $config->visible = $control->isVisible;
            }
            if ($control->isRequired !== null) {
                $config->required = $control->isRequired;
            }
            // if ($control->isMasked !== null) {
            //     $config->masked = $control->isMasked;
            // }

            if (!empty($control->maskedFor) && $control->maskedFor != '0') {
                $maskedGroups = explode(',', $control->maskedFor);
                if (in_array($this->groupId, $maskedGroups)) {
                    $config->masked = 1;
                }
            }
        }

        self::$cache[$cacheKey] = $config;
        return $config;
    }
}
