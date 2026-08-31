<?php
$menu = config('Menu');
$uri = service('uri');
$segments = $uri->getSegments();
//keep first two items only in array if more.
if (count($segments) > 3) {
    $segments = array_slice($segments, 0, 3);
}
$currentUri = implode('/', $segments);

foreach ($menu->items as $mi) {

    //skip item if user dont have permissions
    if (isset($mi->permissions) and isset($mi->module)) {
        if (!\App\Libraries\UserPermissionLib::userCanDo($mi->module, $mi->permissions))
            continue;
    }

?>
    <div class="menu-item <?= !empty($mi->children) ? 'has-sub' : ''; ?> <?= isActiveMenu($mi, $currentUri) ? 'active' : ''; ?>">
        <?php
        if (isset($mi->isPopup) && $mi->isPopup) {
            echo '<a href="javascript:;" class="menu-link apiPopup ' . ($mi->class ?? '') . '" ' . ($mi->attributes ?? '') . ' data-endpoint="' . $mi->url . '">';
        } else {
            echo '<a href="' . $mi->url . '" class="menu-link ' . ($mi->class ?? '') . '" ' . ($mi->attributes ?? '') . '>';
        }
        ?>

        <div class="menu-icon">
            <i class="<?= $mi->icon; ?>"></i>
        </div>
        <div class="menu-text"><?= $mi->title; ?></div>
        <?php
        if (!empty($mi->children)) {
        ?>
            <div class="menu-caret"></div>
        <?php
        }
        ?>
        </a>
        <?php
        if (!empty($mi->children)) {
        ?>
            <div class="menu-submenu">
                <?php
                foreach ($mi->children as $child) {

                    //skip item if user dont have permissions
                    if (isset($child->permissions) and isset($child->module)) {
                        if (!\App\Libraries\UserPermissionLib::userCanDo($child->module, $child->permissions))
                            continue;
                    }

                ?>
                    <div class="menu-item <?= !empty($child->children) ? 'has-sub' : ''; ?> <?= isActiveMenu($child, $currentUri) ? 'active' : ''; ?>">

                        <?php
                        if (isset($child->isPopup) && $child->isPopup) {
                            echo '<a href="javascript:;" class="menu-link apiPopup ' . ($child->class ?? '') . '" ' . ($child->attributes ?? '') . ' data-endpoint="' . $child->url . '">';
                        } else {
                            echo '<a href="' . $child->url . '" class="menu-link ' . ($child->class ?? '') . '" ' . ($child->attributes ?? '') . '>';
                        }
                        ?>

                        <div class="menu-icon">
                            <i class="<?= $child->icon; ?>"></i>
                        </div>
                        <div class="menu-text"><?= $child->title; ?></div>
                        <?php
                        if (!empty($child->children)) {
                        ?>
                            <div class="menu-caret"></div>
                        <?php
                        }
                        ?>
                        </a>
                        <?php
                        if (!empty($child->children)) {
                        ?>
                            <div class="menu-submenu">
                                <?php
                                foreach ($child->children as $subchild) {

                                    //skip item if user dont have permissions
                                    if (isset($subchild->permissions) and isset($subchild->module)) {
                                        if (!\App\Libraries\UserPermissionLib::userCanDo($subchild->module, $subchild->permissions))
                                            continue;
                                    }

                                ?>
                                    <div class="menu-item <?= !empty($subchild->children) ? 'has-sub' : ''; ?> <?= isActiveMenu($subchild, $currentUri) ? 'active' : ''; ?>">

                                        <?php
                                        if (isset($subchild->isPopup) && $subchild->isPopup) {
                                            echo '<a href="javascript:;" class="menu-link apiPopup ' . ($subchild->class ?? '') . '" ' . ($subchild->attributes ?? '') . ' data-endpoint="' . $subchild->url . '">';
                                        } else {
                                            echo '<a href="' . $subchild->url . '" class="menu-link ' . ($subchild->class ?? '') . '" ' . ($subchild->attributes ?? '') . '>';
                                        }
                                        ?>
                                        <div class="menu-icon">
                                            <i class="<?= $subchild->icon; ?>"></i>
                                        </div>
                                        <div class="menu-text"><?= $subchild->title; ?></div>
                                        <?php
                                        if (!empty($subchild->children)) {
                                        ?>
                                            <div class="menu-caret"></div>
                                        <?php
                                        }
                                        ?>
                                        </a>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                <?php
                }
                ?>
            </div>
        <?php
        }
        ?>
    </div>
<?php
}
?>