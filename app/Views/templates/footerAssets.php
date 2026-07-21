<?php

use App\Libraries\AssetManager;
use App\Libraries\SettingManager;
use App\Libraries\Tenant;
use PHPUnit\Event\Runtime\PHP;

AssetManager::loadJsAssets();

$tenantId = Tenant::id();

$config = SettingManager::getAllResolvedSettings($tenantId);

if ($config['customJs']) {
    echo "<script>";
    echo $config['customJs'];
    echo "</script>";
}


if (@$_COOKIE['clientType'] == 'mobile') {
    $menu = config('BottomMenu');
    echo '<div class="pwa-bottom-nav">' . PHP_EOL;
    foreach ($menu->items as $mi) {
        //skip item if user dont have permissions
        if (isset($mi->permissions) and isset($mi->module)) {
            if (!\App\Libraries\UserPermissionLib::userCanDo($mi->module, $mi->permissions))
                continue;
        }
        if (empty($mi->children)) {
            if (isset($mi->isPopup) and $mi->isPopup) {
                echo "<a href='javascript:;' data-endpoint=\"{$mi->url}\" class=\"apiPopup {$mi->class}\"><i class=\"{$mi->icon}\"></i><span>{$mi->title}</span></a>" . PHP_EOL;
            } else {
                echo "<a href=\"{$mi->url}\" class=\"{$mi->class}\"><i class=\"{$mi->icon}\"></i><span>{$mi->title}</span></a>" . PHP_EOL;
            }
        } else {
            echo '<div class="nav-item-with-submenu">' . PHP_EOL;

            if (isset($mi->isPopup) and $mi->isPopup) {
                echo  "<a href='javascript:;' data-endpoint=\"{$mi->url}\" class=\"apiPopup submenu-trigger\"><i class=\"{$mi->icon}\"></i><span>{$mi->title}</span></a>" . PHP_EOL;
            } else {
                echo  "<a href=\"{$mi->url}\" class=\"submenu-trigger\"><i class=\"{$mi->icon}\"></i><span>{$mi->title}</span></a>" . PHP_EOL;
            }

            echo  '<div class="submenu d-none">' . PHP_EOL;
            foreach ($mi->children as $child) {
                //skip item if user dont have permissions
                if (isset($child->permissions) and isset($child->module)) {
                    if (!\App\Libraries\UserPermissionLib::userCanDo($child->module, $child->permissions))
                        continue;
                }

                if (isset($child->isPopup) and $child->isPopup) {
                    echo  "<a href='javascript:;' data-endpoint=\"{$child->url}\" class=\"apiPopup {$child->class}\"><i class=\"{$child->icon}\"></i> {$child->title}</a>" . PHP_EOL;
                } else {
                    echo  "<a href=\"{$child->url}\" class=\"{$child->class}\"><i class=\"{$child->icon}\"></i> {$child->title}</a>" . PHP_EOL;
                }
            }
            echo  '</div>' . PHP_EOL;
            echo  '</div>' . PHP_EOL;
        }
    }
    echo '</div>' . PHP_EOL;
    echo '<div class="pwa-global-dropup d-none"></div>' . PHP_EOL;
}
