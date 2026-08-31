<?php
$config = config('AppConfig');
?>

<div class="row">
    <div class="col-md-12">
        <div class="manageDataTable table-responsive"
            data-module="ItemRecipeMaster"
            data-configendpoint="api/ItemRecipeMaster/getDataTableColumns"
            data-addtype="normal"
            data-endpoint="api/ItemRecipeMaster/getDataTableData"
            data-features='{"columnControls": false,"export": false,"pagination":"manual"}'>
        </div>
    </div>
</div>

<div class="mt-3">
    <a class="btn btn-info apiPopup" href="javascript:;" data-endpoint="<?= base_url("ItemRecipeMaster/addItemrecipemaster"); ?>" data-size="xxl" data-title="Add Program" data-stricttype="strict"><i class="fa fa-plus-circle"></i> Add New</a>
    <a class="btn btn-warning float-end apiPopup" href="javascript:;" data-endpoint="<?= base_url("ItemRecipeMaster/importFile"); ?>" data-size="xl" data-title="Import Program" data-stricttype="strict"><i class="fa fa-upload"></i> Import File</a>
</div>