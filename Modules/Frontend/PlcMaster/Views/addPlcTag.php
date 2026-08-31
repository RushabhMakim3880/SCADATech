<?php
$config = config('AppConfig');
?>

<form method="POST" action="#" autocomplete="off"
    class="autoCrudForm"
    data-resource="api/PlcTagGroupMaster/get/<?= isset($tagGroupId) ? $tagGroupId : '' ?>"
    data-record-id="<?= isset($tagGroupId) ? $tagGroupId : '' ?>"
    data-dropdowns='[
                {"name": "plcId", "endpoint": "/api/PlcMaster/getPlcList"}
            ]'>

    <div class="row">

        <div class='col-md-3 form-group mt-1'>
            <label for="plcId">Plc Name<span class='text-danger'> *</span></label>
            <select class="form-control select2" id="plcId" name="plcId">
                <option value="0">Select Plc</option>
            </select>
        </div>

        <div class='col-md-3 form-group mt-1'>
            <div class='form-group'>
                <label for='groupName'>Group Name<span class='text-danger'> *</span></label>
                <input type='text' class='form-control' name='groupName' id='groupName'   placeholder='Enter Group Name'>
            </div>
        </div>

        <div class='col-md-3 form-group mt-1'>
            <div class='form-group'>
                <label for='description'>Description</label>
                <textarea class='form-control' name='description' id='description' maxlength=''></textarea>
            </div>
        </div>
    </div>

</form>