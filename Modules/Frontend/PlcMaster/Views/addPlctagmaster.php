<?php
$config = config('AppConfig');
?>

<form method="POST" action="#" autocomplete="off"
    class="autoCrudForm"
    data-resource="api/PlcTagMaster/get/<?= isset($tagId) ? $tagId : '' ?>"
    data-record-id="<?= isset($tagId) ? $tagId : '' ?>"
    data-dropdowns='[
        {"name": "plcId" , "endpoint" : "/api/PlcMaster/getPlcList"}
        ]'>

    <div class="row">
        <div class='col-md-3 form-group mt-1'>
            <div class='form-group'>
                <label for='plcId'>Plc Name<span class='text-danger'> *</span></label>
                <select class='form-control select2' name='plcId' id='plcId' required></select>
            </div>
        </div>
        <div class='col-md-3 form-group mt-1'>
            <div class='form-group'>
                <label for='tagAddress'>Tag Address</label>
                <textarea class='form-control ' name='tagAddress' id='tagAddress' maxlength='200' required readonly=""></textarea>
            </div>
        </div>
        <div class='col-md-3 form-group mt-1'>
            <div class='form-group'>
                <label for='dataType'>Data Type</label><select class='form-control' name='dataType' id='dataType' required>
                    <option value='Boolean'>Boolean</option>
                    <option value='SByte'>Sbyte</option>
                    <option value='Byte'>Byte</option>
                    <option value='Int16'>Int16</option>
                    <option value='UInt16'>Uint16</option>
                    <option value='Int32'>Int32</option>
                    <option value='UInt32'>Uint32</option>
                    <option value='Int64'>Int64</option>
                    <option value='UInt64'>Uint64</option>
                    <option value='Float'>Float</option>
                    <option value='Double'>Double</option>
                    <option value='String'>String</option>
                    <option value='DateTime'>Date Time</option>
                </select>
            </div>
        </div>
        <div class='col-md-3 form-group mt-1'>
            <div class='form-group'>
                <label for='registerType'>Register Type</label><select class='form-control' name='registerType' id='registerType' required>
                    <option value='coil'>Coil</option>
                    <option value='holding'>Holding</option>
                    <option value='input'>Input</option>
                    <option value='discrete'>Discrete</option>
                    <option value='variable'>Variable</option>
                </select>
            </div>
        </div>
        <div class='col-md-3 form-group mt-1'>
            <div class='form-group'>
                <label for='readWrite'>Read Write</label><select class='form-control' name='readWrite' id='readWrite' required>
                    <option value='read'>Read</option>
                    <option value='write'>Write</option>
                    <option value='readwrite'>Readwrite</option>
                </select>
            </div>
        </div>
        <div class='col-md-3 form-group mt-1'>
            <div class='form-group'>
                <label for='scaleFactor'>Scale Factor</label>
                <input type='text' class='form-control numberInput' maxlength='' name='scaleFactor' id='scaleFactor' required>
            </div>
        </div>
        <div class='col-md-3 form-group mt-1'>
            <div class='form-group'>
                <label for='offset'>Offset</label>
                <input type='text' class='form-control numberInput' maxlength='' name='offset' id='offset' required>
            </div>
        </div>
        <div class='col-md-3 form-group mt-1'>
            <div class='form-group'>
                <label for='unit'>Unit</label>
                <input type='text' class='form-control' name='unit' id='unit' maxlength='20'>
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