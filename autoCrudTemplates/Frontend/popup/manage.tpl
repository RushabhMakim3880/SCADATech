<?php
$config = config('AppConfig');
?>


<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => '']) ?>

<div class="row">
    <div class="col-md-12">

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => $pageTitle]]) ?>

        <div class="manageDataTable"
            data-module="{{MODULE_NAME}}"
            data-addendpoint="{{MODULE_NAME}}/add{{ITEM_NAME}}"
            data-addtype="{{CRUD_TYPE}}"
            data-configendpoint="api/{{MODULE_NAME}}/getDataTableColumns"
            data-endpoint="api/{{MODULE_NAME}}/getDataTableData"
            data-features='{"columnControls": true,"export": true,"pagination":"manual"}'>
        </div>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

</div>

<button type="button" class="btn btn-info apiPopup" data-title="{{ITEM_NAME}} Form" data-size="xl" data-endpoint="{{MODULE_NAME}}/add{{ITEM_NAME}}"><i class="fa fa-plus-circle"></i> Add New</button>


<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>