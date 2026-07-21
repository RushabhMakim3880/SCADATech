<?php
$config = config('AppConfig');
?>

<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => '']) ?>

<div class="row">
    <div class="col-md-12">

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Manage Trusted Devices']]) ?>

        <div class="manageDataTable"
            data-module="manageTrustedDevice"
            data-addendpoint=""
            data-addtype=""
            data-configendpoint="api/trustedDevice/getDataTableColumns"
            data-endpoint="api/trustedDevice/getDataTableData"
            data-features='{"columnControls": true,"export": true}'>
        </div>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

</div>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>