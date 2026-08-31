<?php

/**
 * @var string $mode 'open' or 'close'
 * @var array $params Additional parameters (e.g., title)
 */

if ($mode === 'open'):
    $title = $params['title'] ?? 'Default Title';
?>
    <!-- BEGIN panel -->
    <div class="card mt-3">
        <div class="card-body">
            <h2 class="card-title">
                <?= esc($title) ?>
            </h2>

        <?php
    elseif ($mode === 'close'):
        ?>

        </div>
    </div>
    <!-- END panel -->
<?php
    endif;
