<?php

/**
 * Expected variables:
 * - $bgClass: Background class (e.g., 'bg-blue')
 * - $icon: Icon class (e.g., 'fa fa-desktop')
 * - $title: Widget title (e.g., 'TOTAL VISITORS')
 * - $value: The statistic value (e.g., '3,291,922')
 * - $desc: URL or JavaScript call for details (e.g., 'javascript:;')
 * - $progressBarValue: URL or JavaScript call for details (e.g., 'javascript:;')
 * - $viewPercentage: URL or JavaScript call for details (e.g., 'javascript:;')
 */
?>

<div class="widget widget-stats <?= esc($bgClass) ?>">
    <div class="stats-icon stats-icon-lg"><i class="<?= esc($icon) ?>"></i></div>
    <div class="stats-content">
        <div class="stats-title"><?= $title ?></div>
        <div class="stats-number"><?= $value ?></div>
        <div class="stats-progress progress">
            <div class="progress-bar" style="width: <?= esc($progressBarValue) ?>%;"></div>
        </div>
        <div class="stats-desc"><?= $desc ?>  <?= $viewPercentage ?></div>
    </div>
</div>
