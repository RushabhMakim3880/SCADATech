<?php

/**
 * @var string $mode 'open' or 'close'
 * @var array $params Additional parameters (e.g., title)
 */

if ($mode === 'open'):
    $title = $params['title'] ?? 'Default Title';
?>
    <!-- BEGIN panel -->
    <div class="panel panel-inverse" data-sortable="false">
        <div class="panel-heading">
            <h4 class="panel-title"><?= esc($title) ?></h4>
            <div class="panel-heading-btn">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-default" data-toggle="panel-expand"><i class="fa fa-expand"></i></a>
                <!-- <a href="javascript:;" class="btn btn-xs btn-icon btn-success" data-toggle="panel-reload"><i class="fa fa-redo"></i></a> -->
                <a href="javascript:;" class="btn btn-xs btn-icon btn-warning" data-toggle="panel-collapse"><i class="fa fa-minus"></i></a>
                <!-- <a href="javascript:;" class="btn btn-xs btn-icon btn-danger" data-toggle="panel-remove"><i class="fa fa-times"></i></a> -->
            </div>
        </div>
        <div class="panel-body">
        <?php
    elseif ($mode === 'close'):
        ?>
        </div> <!-- end .panel-body -->
    </div>
    <!-- END panel -->
<?php
    endif;
