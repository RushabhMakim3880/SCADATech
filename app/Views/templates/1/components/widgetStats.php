<?php

/**
 * Expected variables:
 * - $bgClass: Background class (e.g., 'bg-blue')
 * - $icon: Icon class (e.g., 'fa fa-desktop')
 * - $title: Widget title (e.g., 'TOTAL VISITORS')
 * - $value: The statistic value (e.g., '3,291,922')
 * - $link: URL or JavaScript call for details (e.g., 'javascript:;')
 */
?>

<div class="widget widget-stats <?= esc($bgClass) ?>">
    <div class="stats-icon"><i class="<?= esc($icon) ?>"></i></div>
    <div class="stats-info">
        <h4><?= $title ?></h4>
        <p><?= $value ?></p>
    </div>
    <?php if (!empty($link)) : ?>
        <div class="stats-link">
            <a href="<?= $link ?>">View Detail <i class="fa fa-arrow-alt-circle-right"></i></a>
        </div>
    <?php endif; ?>

</div>