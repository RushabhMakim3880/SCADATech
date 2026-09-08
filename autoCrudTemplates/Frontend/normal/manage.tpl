<?php
$config = config('AppConfig');
?>

<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => '']) ?>

<div class="row">
    <div class="col-md-12">

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => $pageTitle]]) ?>

        <div class="manageDataTable"
            data-module="{{MODULE_NAME}}"
            data-configendpoint="api/{{MODULE_NAME}}/getDataTableColumns"
            data-addendpoint="{{MODULE_NAME}}/add{{ITEM_NAME}}"
            data-addtype="{{CRUD_TYPE}}"
            data-endpoint="api/{{MODULE_NAME}}/getDataTableData"
            data-features='{"columnControls": true,"export": true,"pagination":"manual"}'>
        </div>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

</div>

<a class="btn btn-info" href="<?= base_url("{{MODULE_NAME}}/add{{ITEM_NAME}}"); ?>"><i class="fa fa-plus-circle"></i> Add New</a>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>