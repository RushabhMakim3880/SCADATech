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
<div class="card card-sm">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-auto">
                <span class="bg-primary text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                    <i class="<?php echo $icon ?? ""; ?>"></i>
                </span>
            </div>
            <div class="col">
                <div class="font-weight-medium">
                    <?php echo $value ?? ""; ?>
                    <?php echo $title ?? ""; ?>
                </div>
                <div class="text-secondary">
                    <?php echo $subtitle ?? ""; ?>
                </div>
            </div>
        </div>
    </div>
</div>