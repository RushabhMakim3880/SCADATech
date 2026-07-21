<?php
$config = config('AppConfig');
?>

<form method="POST" action="#" autocomplete="off"
    class="autoCrudForm"
    data-resource="api/UiTagMaster/get/<?= isset($uiTagId) ? $uiTagId : '' ?>"
    data-record-id="<?= isset($uiTagId) ? $uiTagId : '' ?>"
    data-dropdowns='[
                {"name": "tagId", "endpoint": "/api/PlcMaster/getPlcTagList"},
                {"name": "tagGroupId", "endpoint": "/api/PlcTagGroupMaster/getTagGroupList"}

            ]'>

    <div class="row">

        <div class='col-md-3 form-group mt-1'>
            <div class='form-group'>
                <label for='tagName'>Scada Tag Name<span class='text-danger'> *</span></label>
                <input type='text' class='form-control' name='tagName' id='tagName' required placeholder='Enter Scada Tag Name'>
            </div>
        </div>

        <div class='col-md-3 form-group mt-1'>
            <label for="tagGroupId">Tag Group Name<span class='text-danger'>*</span></label>
            <select class="form-control select2" id="tagGroupId" name="tagGroupId" required>
            </select>
        </div>

        <div class='col-md-3 form-group mt-1'>
            <label for="tagId">PLC Tag<span class='text-danger'>*</span></label>
            <select class="form-control select2" id="tagId" name="tagId" required>
            </select>
        </div>

        <div class='col-md-3 form-group mt-1'>
            <label for="minValue">Min Value</label>
            <input type='text' class='form-control' name='minValue' id='minValue' placeholder='Enter Min Value'>
        </div>

        <div class='col-md-3 form-group mt-1'>
            <label for="maxValue">Max Value</label>
            <input type='text' class='form-control' name='maxValue' id='maxValue' placeholder='Enter Max Value'>
        </div>

        <!-- isActive -->
        <div class='col-md-3 form-group mt-1'>
            <label for="isActive">Status</label>
            <select class="form-control select2" id="isActive" name="isActive" required>
                <option value="1">Active</option>
                <option value="0">In Active</option>
            </select>
        </div>


    </div>

</form>