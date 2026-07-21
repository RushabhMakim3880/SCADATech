<?php

namespace App\Libraries;

use Config\Services;
use App\Libraries\Auth;

class Tenant
{
    private static ?object $tenant = null; // Cached tenant data

    /**
     * Get the current tenant ID
     * - If logged in, use Auth library
     * - If public screen, detect from subdomain, domain, or query param
     * - If no match, fallback to default tenant (ID = 1)
     */
    public static function id()
    {
        return self::getTenant()->tenantId ?? null; // Default to 1 if no tenant found
    }

    /**
     * Get the full tenant details (cached for the current request)
     * @return object|null
     */
    public static function getTenant(): ?object
    {
        if (is_cli()) {
            // CLI context, no tenant
            return null;
        }


        if (self::$tenant !== null) {
            return self::$tenant;
        }

        $db = db_connect();
        $request = Services::request();

        // 1️⃣ If logged in, use Auth's tenantId
        $loggedInTenantId = Auth::user()->tenantId ?? null;
        if ($loggedInTenantId) {
            self::$tenant = $db->table('tenantMaster')->where('tenantId', $loggedInTenantId)->get()->getRow();
            return self::$tenant;
        }


        // 2️⃣ Try to get tenant by subdomain (subdomain.example.com)
        $host = $request->getServer('HTTP_HOST');
        $parts = explode('.', $host);
        if (count($parts) > 2) {
            $subdomain = $parts[0];
            $tenant = $db->table('tenantMaster')->where('subDomain', $subdomain)->get()->getRow();
            if ($tenant) {
                self::$tenant = $tenant;
                return self::$tenant;
            }
        }

        // 3️⃣ Try to get tenant by full domain (custom domain mapping)
        $tenant = $db->table('tenantMaster')->where('customDomain', $host)->get()->getRow();
        if ($tenant) {
            self::$tenant = $tenant;
            return self::$tenant;
        }

        // 4️⃣ Try to get tenant from URL parameter (?tenant=xyz)
        $tenantSlug = $request->getGet('tenant');
        if ($tenantSlug) {
            $tenant = $db->table('tenantMaster')->where('subDomain', $tenantSlug)->get()->getRow();
            if ($tenant) {
                self::$tenant = $tenant;
                return self::$tenant;
            }
        }

        // 5️⃣ Default to tenant ID = 1 (fallback)
        // self::$tenant = $db->table('tenantMaster')->where('tenantId', 1)->get()->getRow();
        return self::$tenant;
    }
}
