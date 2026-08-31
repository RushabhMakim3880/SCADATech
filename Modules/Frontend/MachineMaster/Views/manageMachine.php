<?php
$config = config('AppConfig');
?>

<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => '']) ?>

<div class="row">
    <div class="col-md-12">

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => $pageTitle]]) ?>

        <div class="manageDataTable table-responsive"
            data-module="MachineMaster"
            data-addtype="normal"
            data-configendpoint="api/MachineMaster/getDataTableColumns"
            data-endpoint="api/MachineMaster/getDataTableData"
            data-features='{"columnControls": false,"export": false,"pagination":"manual"}'>
        </div>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

</div>

<a class="btn btn-info" href="<?= base_url("MachineMaster/addMachine"); ?>"><i class="fa fa-plus-circle"></i> Add New</a>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>