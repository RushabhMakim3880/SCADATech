<?php
$config = config('AppConfig');
?>

<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => '']) ?>
<form method="POST" action="#" autocomplete="off"
    class="autoCrudForm"
    data-resource="api/MachineMaster/get/<?= isset($machineId) ? $machineId : '' ?>"
    data-record-id="<?= isset($machineId) ? $machineId : '' ?>"
    data-dropdowns='[]'>

    <div class="row">
        <div class="col-md-12">

            <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => $pageTitle]]) ?>



            <div class="row">
                <div class='col-md-3 form-group mt-1'>
                    <div class='form-group'>
                        <label for='machineCode'>Machine Code<span class='text-danger'> *</span></label>
                        <input type='text' class='form-control' name='machineCode' id='machineCode' maxlength='50' required placeholder='Enter Machine Code'>
                    </div>
                </div>

                <div class='col-md-3 form-group mt-1'>
                    <div class='form-group'>
                        <label for='machineName'>Machine Name<span class='text-danger'> *</span></label>
                        <input type='text' class='form-control' name='machineName' id='machineName' maxlength='50' required placeholder='Enter Machine Name'>
                    </div>
                </div>

                <div class='col-md-3 form-group mt-1'>
                    <div class='form-group'>
                        <label for='machineType'>Machine Type<span class='text-danger'> *</span></label>
                        <select class='form-control' name='machineType' id='machineType' required placeholder='Enter Machine Type'>
                            <?php
                            $machineTypes = getMachineTypes();
                            foreach ($machineTypes as $type) {
                                echo "<option value='{$type}'>{$type}</option>";
                            }
                            ?>
                        </select>
                    </div>
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



            <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
        </div>

    </div>

    <div class="row">
        <div class="col-md-12">
            <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Machine Configuration']]) ?>

            <table class="table table-bordered oneToManyWrapper" data-group="machineDetails">
                <thead>
                    <tr>
                        <th>HeadName <sup class='text-danger'>*</sup></th>
                        <th>Type</th>
                        <th>X Pos</th>
                        <th>Hold Down X</th>
                        <th>Side</th>
                        <th>Marking Cassets</th>

                    </tr>
                </thead>
                <tbody>
                    <tr class="oneToManyElement">
                        <td>
                            <input type='hidden' name='machineDetails[machineDetailId]' value=''>
                            <input type="text" class="form-control " id="headName" name='machineDetails[headName]' placeholder="Enter Head Name" required>
                            <button type='button' class='btn btn-primary btn-sm addOneToManyElement e2t_ignore' tabindex="-1" style="margin-top: 5px;"><i class="fa fa-plus-circle"></i></button>
                        </td>

                        <td>
                            <select class="form-control " id="headType" name='machineDetails[headType]' required>
                                <?php
                                $headTypes = getHeadTypes();
                                foreach ($headTypes as $type) {
                                    echo "<option value='{$type}'>{$type}</option>";
                                }
                                ?>
                            </select>
                        </td>

                        <td>
                            <input type="text" class="form-control " id="xPos" name='machineDetails[xPosition]' placeholder="Enter X Pos">
                        </td>

                        <td>
                            <input type="text" class="form-control " id="holdDownX" name='machineDetails[holdDownX]' placeholder="Enter Hold Down X">
                        </td>

                        <td>
                            <select class="form-control " id="side" name='machineDetails[side]' required>
                                <?php
                                $sides = ["N/A", "A", "B"];
                                foreach ($sides as $side) {
                                    echo "<option value='{$side}'>{$side}</option>";
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control numberInput" id="markingCassets" name='machineDetails[markingCassets]' placeholder="Nos Of Cassets For Marking">
                            <button type='button' class='btn btn-danger btn-sm removeOneToManyElement e2t_ignore' tabindex="-1"><i class="fa fa-minus-circle"></i></button>
                        </td>
                        </button>
                    </tr>
                </tbody>
            </table>

            <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
            <button type="submit" class="btn btn-primary ">Submit</button>
            <a title="View All" href="<?php echo base_url("MachineMaster/manageMachine"); ?>" class="btn btn-info">
                <i class="fas fa-list"></i>&nbsp;&nbsp; View All
            </a>
        </div>

    </div>

</form>
<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>