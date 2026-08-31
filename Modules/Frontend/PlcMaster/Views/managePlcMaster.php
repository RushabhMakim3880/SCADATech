<?php
$config = config('AppConfig');
?>


<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => '']) ?>

<div class="row">
    <div class="col-md-12">

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => $pageTitle]]) ?>

        <div class="manageDataTable table-responsive"
            data-module="PlcMaster"
            data-addtype="popup"
            data-configendpoint="api/PlcMaster/getDataTableColumns"
            data-endpoint="api/PlcMaster/getDataTableData"
            data-features='{"columnControls": false,"export": false,"pagination":"manual"}'>
        </div>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

</div>


<a title="Add New" href="<?php echo base_url("PlcMaster/addPlcMaster"); ?>" class="btn btn-info">
    <i class="fas fa-plus-circle"></i>&nbsp;&nbsp; Add New
</a>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>