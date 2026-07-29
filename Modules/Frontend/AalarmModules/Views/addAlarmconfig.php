<?php
$config = config('AppConfig');
?>

<form method="POST" action="#" autocomplete="off"
    class="autoCrudForm"
    data-resource="api/AalarmModules/get/<?= isset($alarmId) ? $alarmId : '' ?>"
    data-record-id="<?= isset($alarmId) ? $alarmId : '' ?>"
    data-dropdowns='[
        {"name": "uiTagId" , "endpoint" : "/api/UiTagMaster/getUiTag"}

        ]'>

    <div class="row">
        <div class='col-md-3 form-group mt-1'>
            <label for="uiTagId">Scada Tag<span class='text-danger'>*</span></label>
            <select class="form-control select2" id="uiTagId" name="uiTagId" required>
                <option value="0">Select Scada Tag</option>
            </select>

        </div>
        <div class='col-md-3 form-group mt-1'>
            <div class='form-group'>
                <label for='loloTheresold'>Lolo Theresold</label>
                <input type='text' class='form-control numberInput' maxlength='10' name='loloTheresold' id='loloTheresold'>
            </div>
        </div>
        <div class='col-md-3 form-group mt-1'>
            <div class='form-group'>
                <label for='loTheresold'>Lo Theresold</label>
                <input type='text' class='form-control numberInput' maxlength='10' name='loTheresold' id='loTheresold'>
            </div>
        </div>
        <div class='col-md-3 form-group mt-1'>
            <div class='form-group'>
                <label for='hiTheresold'>Hi Theresold</label>
                <input type='text' class='form-control numberInput' maxlength='10' name='hiTheresold' id='hiTheresold'>
            </div>
        </div>
        <div class='col-md-3 form-group mt-1'>
            <div class='form-group'>
                <label for='hihiTheresold'>Hihi Theresold</label>
                <input type='text' class='form-control numberInput' maxlength='10' name='hihiTheresold' id='hihiTheresold'>
            </div>
        </div>
        <div class='col-md-3 form-group mt-1'>
            <div class='form-group'>
                <label for='message'>Message</label>
                <input type='text' class='form-control' maxlength='255' name='message' id='message'>
            </div>
        </div>
        <div class='col-md-3 form-group mt-1'>
            <label for="isActive">Status</label>
            <select class="form-control select2" id="isActive" name="isActive" required>
                <option value="1">Active</option>
                <option value="0">In Active</option>
            </select>
        </div>
        <div class='col-md-6 form-group mt-1'>
            <div class='form-group'>
                <label for='solution'>Solution</label>
                <textarea class='form-control' name='solution' id='solution' rows='3'></textarea>
            </div>
        </div>
    </div>

</form>