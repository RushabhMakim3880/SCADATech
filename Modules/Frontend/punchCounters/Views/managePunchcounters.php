<?php
$config = config('AppConfig');
?>


<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => '']) ?>

<div class="row">
    <div class="col-md-12">

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => $pageTitle]]) ?>

<div class="manageDataTable"
               data-module="punchCounters"
               data-configendpoint="api/punchCounters/getDataTableColumns"
               data-endpoint="api/punchCounters/getDataTableData"
               data-features='{"columnControls": true,"export": true,"pagination":"manual"}'>
        </div>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>

    </div>

</div>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>