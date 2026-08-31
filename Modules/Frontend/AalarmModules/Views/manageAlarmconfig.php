<?php
$config = config('AppConfig');
?>


<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => '']) ?>

<div class="row">
    <div class="col-md-12">

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => $pageTitle]]) ?>

        <div class="manageDataTable"
            data-module="AalarmModules"
            data-addtype="popup"
            data-configendpoint="api/AalarmModules/getDataTableColumns"
            data-endpoint="api/AalarmModules/getDataTableData"
            data-features='{"columnControls": false,"export": false,"pagination":"manual"}'>
        </div>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

</div>

<button type="button" class="btn btn-info apiPopup" data-title="Alarm Config Form" data-size="xl" data-endpoint="AalarmModules/addAlarmconfig"><i class="fa fa-plus-circle"></i> Add New</button>


<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>