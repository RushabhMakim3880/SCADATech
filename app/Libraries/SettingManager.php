<?php

namespace App\Libraries;

use Config\Database;
use Config\Services;

class SettingManager
{
    private static array $cachedSettings = [];
    private static array $cachedJsSettings = [];

    public static function getAllResolvedSettings(?int $tenantId = null): array
    {
        self::loadResolved($tenantId);
        return self::$cachedSettings[$tenantId] ?? [];
    }

    public static function get(string $featureKey, ?int $tenantId = null): mixed
    {
        $all = self::getAllResolvedSettings($tenantId);
        return $all[$featureKey] ?? null;
    }

    public static function getJsSideSettings(?int $tenantId = null): array
    {
        self::loadResolved($tenantId);
        return self::$cachedJsSettings[$tenantId] ?? [];
    }

    public static function invalidateCache(?int $tenantId): void
    {
        $cache = Services::cache();
        $cacheKey = $tenantId . '_resolvedSettings';
        $cache->delete($cacheKey);
        $cache->delete($cacheKey . '_js');
        unset(self::$cachedSettings[$tenantId], self::$cachedJsSettings[$tenantId]);
    }

    private static function loadResolved(?int $tenantId): void
    {
        if (isset(self::$cachedSettings[$tenantId])) {
            return;
        }

        $cache = Services::cache();
        $cacheKey = $tenantId . '_resolvedSettings';

        if (($cached = $cache->get($cacheKey)) !== null) {
            self::$cachedSettings[$tenantId] = $cached;
            self::$cachedJsSettings[$tenantId] = $cache->get($cacheKey . '_js') ?? [];
            return;
        }

        $db = Database::connect();

        $features = $db->table('featureRegistry')->get()->getResult();
        $tenantSettings = self::map($db->table('tenantSettings')->where('tenantId', $tenantId)->get()->getResult());
        $topups = self::map($db->table('tenantTopups')->where('tenantId', $tenantId)->get()->getResult());

        $planRow = $db->table('tenantPlans')->select('planId')->where('tenantId', $tenantId)->get()->getRow();
        $planFeatures = $planRow
            ? self::map($db->table('planFeatures')->where('planId', $planRow->planId)->get()->getResult())
            : [];

        $saasSettings = self::map($db->table('saasSettings')->get()->getResult());

        $resolved = [];
        $jsOnly = [];

        foreach ($features as $feature) {
            $key = $feature->featureKey;

            if (isset($topups[$key])) {
                $resolved[$key] = $topups[$key];
            } elseif (isset($planFeatures[$key])) {
                $resolved[$key] = $planFeatures[$key];
            } elseif (isset($tenantSettings[$key])) {
                $resolved[$key] = $tenantSettings[$key];
            } elseif (isset($saasSettings[$key])) {
                $resolved[$key] = $saasSettings[$key];
            } else {
                $resolved[$key] = $feature->defaultValue;
            }

            //.env based overrides
            foreach ($_ENV as $k => $v) {
                if (str_starts_with($k, "feature.")) {
                    $k = str_replace("feature.", "", $k);
                    $resolved[$k] = $v;
                }
            }

            if (!empty($feature->isJsSide)) {
                $jsOnly[$key] = $resolved[$key];
            }
        }

        self::$cachedSettings[$tenantId] = $resolved;
        self::$cachedJsSettings[$tenantId] = $jsOnly;

        // $cache->save($cacheKey, $resolved, 3600);
        // $cache->save($cacheKey . '_js', $jsOnly, 3600);
    }

    private static function map(array $rows): array
    {
        $out = [];
        foreach ($rows as $row) {
            $out[$row->featureKey] = $row->value;
        }
        return $out;
    }
}
