<?php
$config = config('AppConfig');
?>


<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => '']) ?>

<div class="row">
    <div class="col-md-12">


        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => $pageTitle]]) ?>

        <input type="hidden" class="dataTableCustomFilter" data-target="PlcTagMaster" name="plcId" value="<?= ($plcId) ?>" />


        <div class="manageDataTable table-responsive"
            data-module="PlcTagMaster"
            data-addendpoint="PlcTagMaster/addPlctagmaster"
            data-addtype="popup"
            data-configendpoint="api/PlcTagMaster/getDataTableColumns"
            data-endpoint="api/PlcTagMaster/getDataTableData"
            data-features='{"columnControls": true,"export": false,"pagination":"manual"}'>
        </div>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

</div>



<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>