<?php
$config = config('AppConfig');
?>

<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => '']) ?>
<form method="POST" action="#" autocomplete="off"
    class="autoCrudForm"
    data-resource="api/PlcMaster/get/<?= isset($plcId) ? $plcId : '' ?>"
    data-record-id="<?= isset($plcId) ? $plcId : '' ?>"
    data-dropdowns='[
                {"name": "machineId", "endpoint": "/api/MachineMaster/getMachineList"}
            ]'>

    <div class="row">
        <div class="col-md-12">

            <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => $pageTitle]]) ?>
            <div class="row">

                <div class='col-md-3 form-group mt-1'>
                    <label for="machineId">Machine Name<span class='text-danger'>*</span></label>
                    <select class="form-control select2" id="machineId" name="machineId" required>
                        <option value="0">Select Machine</option>
                    </select>
                </div>

                <div class='col-md-3 form-group mt-1'>
                    <div class='form-group'>
                        <label for='plcName'>PLC Name<span class='text-danger'> *</span></label>
                        <input type='text' class='form-control' name='plcName' id='plcName' maxlength='50' required placeholder='Enter PLC Name'>
                    </div>
                </div>

                <div class='col-md-3 form-group mt-1'>
                    <label for="protocol">Protocol</label>
                    <select class="form-control select2" id="protocol" name="protocol">
                        <option value='modbus-tcp'>Modbus-tcp</option>
                        <option value='opc-ua'>Opc-ua</option>
                        <option value='mqtt'>Mqtt</option>
                        <option value='custom'>Custom</option>
                    </select>
                </div>

                <div class='col-md-3 form-group mt-1'>
                    <div class='form-group'>
                        <label for='ipAddress'>Ip Address</label>
                        <input type='text' class='form-control' name='ipAddress' id='ipAddress' maxlength='45'>
                    </div>
                </div>

                <div class='col-md-3 form-group mt-1'>
                    <div class='form-group'>
                        <label for='port'>Port</label>
                        <input type='text' class='form-control numberInput' maxlength='11' name='port' id='port'>
                    </div>
                </div>

                <div class='col-md-3 form-group mt-1'>
                    <div class='form-group'>
                        <label for='modbusDeviceId'>Modbus Device Id</label>
                        <input type='text' class='form-control numberInput' maxlength='11' name='modbusDeviceId' id='modbusDeviceId'>
                    </div>
                </div>

                <div class='col-md-3 form-group mt-1'>
                    <div class='form-group'>
                        <label for='description'>Description</label>
                        <textarea class='form-control' name='description' id='description' maxlength=''></textarea>
                    </div>
                </div>

                <!-- isActive -->
                <div class='col-md-3 form-group mt-1'>
                    <label for="status">Status</label>
                    <select class="form-control select2" id="status" name="status" required>
                        <option value="1">Active</option>
                        <option value="0">In Active</option>
                    </select>
                </div>

            </div>
        </div>
    </div>

    <!---  Plc Tag Details Start --->


    <!---  Plc Tag Details End --->

    <!-- submit button -->
    <button type="submit" class="btn btn-primary ">Submit</button>
    <a title="View All" href="<?php echo base_url("PlcMaster/managePlcMaster"); ?>" class="btn btn-info">
        <i class="fas fa-list"></i>&nbsp;&nbsp; View All
    </a>
    </div>

</form>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>