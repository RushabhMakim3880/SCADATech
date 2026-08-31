<?php
$config = config('AppConfig');
?>

<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => '']) ?>

<div class="row">
    <div class="col-md-12">

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => $pageTitle]]) ?>

        <div class="manageDataTable"
            data-module="manageLocationMaster"
            data-configendpoint="api/locationMaster/getDataTableColumns"
            data-endpoint="api/locationMaster/getDataTableData"
            data-features='{"columnControls": true,"export": true}'>
        </div>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

</div>


<a class='btn btn-success' title="Add New Location" href="<?php echo base_url("locationMaster/addLocationMaster"); ?>"><i class="fa fa-plus-circle"></i>&nbsp;&nbsp;Add</a>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>