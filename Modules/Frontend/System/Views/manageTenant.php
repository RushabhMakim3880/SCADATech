<?php
$config = config('AppConfig');
?>
<!-- <a title="Add New" href="<?php echo base_url("tenantMaster/addTenant"); ?>" class="btn btn-info float-end m-3 mt-3">
    <i class="fas fa-plus-circle"></i>&nbsp;&nbsp; Add New
</a> -->
<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => '']) ?>

<div class="row">
    <div class="col-md-12">

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => $pageTitle]]) ?>

        <div class="manageDataTable"
            data-module="manageTenant"
            data-configendpoint="api/tenantMaster/getDataTableColumns"
            data-endpoint="api/tenantMaster/getDataTableData"
            data-features='{"columnControls": true,"export": true}'>
        </div>


        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

</div>
<a class='btn btn-success' title="Add New " href="<?php echo base_url("tenantMaster/addTenant"); ?>"><i class="fa fa-plus-circle"></i>&nbsp;&nbsp;Add</a>


<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>