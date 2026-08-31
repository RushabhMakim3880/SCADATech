<?php
$config = config('AppConfig');
?>

<form method="POST" action="#" autocomplete="off"
    class="autoCrudForm"
    data-resource="api/MachineOperationConfig/get/<?= isset($operationConfigId) ? $operationConfigId : '' ?>"
    data-record-id="<?= isset($operationConfigId) ? $operationConfigId : '' ?>"
    data-dropdowns='[
                {"name": "machineId", "endpoint": "/api/MachineMaster/getMachineList"},
                {"name": "plcTriggerTag", "endpoint": "/api/PlcMaster/getPlcTagList"},
                {"name": "plcAckTag", "endpoint": "/api/PlcMaster/getPlcTagList"}

            ]'>

    <div class="row">

        <div class='col-md-3 form-group mt-1'>
            <label for="machineId">Machine Name<span class='text-danger'> *</span></label>
            <select class="form-control select2" id="machineId" name="machineId">
                <option value="0">Select Machine</option>
            </select>
        </div>

        <div class='col-md-3 form-group mt-1'>
            <div class='form-group'>
                <label for='operationCode'>Operation Code<span class='text-danger'> *</span></label>
                <input type='text' class='form-control' name='operationCode' id='operationCode' maxlength='50' required placeholder='Enter Operation Code'>
            </div>
        </div>

        <div class='col-md-3 form-group mt-1'>
            <div class='form-group'>
                <label for='operationType'>Operation Type<span class='text-danger'> *</span></label>
                <input type='text' class='form-control' name='operationType' id='operationType' maxlength='50' required placeholder='Enter Operation Type'>
            </div>
        </div>

        <div class='col-md-3 form-group mt-1'>
            <div class='form-group'>
                <label for='operationLabel'>Operation Label<span class='text-danger'> *</span></label>
                <input type='text' class='form-control' name='operationLabel' id='operationLabel' maxlength='50' required placeholder='Enter Operation Type'>
            </div>
        </div>

        <div class='col-md-3 form-group mt-1'>
            <div class='form-group'>
                <label for='positionX'>Position X</label>
                <input type='text' class='form-control numberInput' maxlength='' name='positionX' id='positionX'>
            </div>
        </div>

        <div class='col-md-3 form-group mt-1'>
            <div class='form-group'>
                <label for='positionY'>Position Y</label>
                <input type='text' class='form-control numberInput' maxlength='' name='positionY' id='positionY'>
            </div>
        </div>


        <div class='col-md-3 form-group mt-1'>
            <label for="plcTriggerTag">PLC Tag<span class='text-danger'> *</span></label>
            <select class="form-control select2" id="plcTriggerTag" name="plcTriggerTag">
                <option value="0">Select Tag</option>
            </select>
        </div>

        <div class='col-md-3 form-group mt-1'>
            <label for="plcAckTag">Plc Ack Tag<span class='text-danger'> *</span></label>
            <select class="form-control select2" id="plcAckTag" name="plcAckTag">
                <option value="0">Select Tag</option>
            </select>
        </div>


        <div class='col-md-3 form-group mt-1'>
            <div class='form-group'>
                <label for='description'>Description</label>
                <textarea class='form-control' name='description' id='description' maxlength=''></textarea>
            </div>
        </div>

    </div>

</form>