<?php
$config = config('AppConfig');
?>


<form method="POST" action="#" enctype="multipart/form-data" autocomplete="off"
    class="autoCrudForm"
    data-resource="api/importFile/get/<?= isset($itemRecipeId) ? $itemRecipeId : '' ?>"
    data-record-id=""
    data-dropdowns='[]'>

    <div class="row">
        <div class="col-md-12">

            <div class="row">

                <!-- hidden variable -->
                <input type="hidden" name="itemRecipeId" value="">
                <input type="hidden" name="itemRecipeName" value="">

                <div class='col-md-3 form-group mt-1'>
                    <div class='form-group'>
                        <label for='importFile'>Item File<span class='text-danger'> *</span></label>
                        <input type='file' class='form-control' name='importFile[]' id='importFile' required placeholder='Select File' multiple>
                    </div>
                </div>

            </div>

            <!-- <button type="submit" class="btn btn-primary ">Submit</button>
            <a title="View All" href="<?php echo base_url("ItemRecipeMaster/manageItemrecipemaster"); ?>" class="btn btn-info">
                <i class="fas fa-list"></i>&nbsp;&nbsp; View All
            </a> -->

        </div>
    </div>
</form>