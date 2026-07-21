<?php
$config = config('AppConfig');
?>


<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => '']) ?>

<div class="row">
    <div class="col-md-12">

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => $pageTitle]]) ?>

        <div class="manageDataTable table-responsive"
            data-module="PlcTagGroupMaster"
            data-addtype="popup"
            data-configendpoint="api/PlcTagGroupMaster/getDataTableColumns"
            data-endpoint="api/PlcTagGroupMaster/getDataTableData"
            data-features='{"columnControls": false,"export": false,"pagination":"manual"}'>
        </div>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

</div>

<button type="button" class="btn btn-info apiPopup" data-title="Plc Tag Form" data-size="xl" data-endpoint="PlcTagGroupMaster/addPlcTag"><i class="fa fa-plus-circle"></i> Add New</button>


<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>