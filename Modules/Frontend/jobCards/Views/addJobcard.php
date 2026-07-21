<?php
$config = config('AppConfig');
?>

<form method="POST" action="#" autocomplete="off"
    class="autoCrudForm"
    data-resource="api/jobCards/get/<?= isset($jobId) ? $jobId : '' ?>"
    data-record-id="<?= isset($jobId) ? $jobId : '' ?>"
    data-dropdowns='[]'>

    <div class="row">
        <div class='col-md-12 form-group mt-1'>
            <div class='form-group'>
                <label for='itemRecipeId'>Item Recipe<span class='text-danger'> *</span></label>
                <select class='form-control select2' data-selecttype="ajax" name='itemRecipeId' id='itemRecipeId' required data-endpoint="api/ItemRecipeMaster/getItemRecipeList">
                </select>
            </div>
        </div>
    </div>
    <div>
        <div class='col-md-12 form-group mt-1'>
            <div class='form-group'>
                <label for='requiredQuantity'>Required Quantity<span class='text-danger'> *</span></label>
                <input type='text' class='form-control numberInput' maxlength='' name='requiredQuantity' id='requiredQuantity' required>
            </div>
        </div>
    </div>
</form>