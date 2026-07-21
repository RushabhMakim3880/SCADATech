<?php
$config = config('AppConfig');
?>


<form method="POST" action="#" autocomplete="off"
    class="autoCrudForm"
    data-resource="api/ItemRecipeMaster/get/<?= isset($itemRecipeId) ? $itemRecipeId : '' ?>"
    data-record-id="<?= isset($itemRecipeId) ? $itemRecipeId : '' ?>"
    data-dropdowns='[]'>

    <div class="row">
        <div class="col-md-12">

            <div class="row">

                <div class='col-md-3 form-group mt-1'>
                    <div class='form-group'>
                        <label for='itemCode'>Item Code<span class='text-danger'> *</span></label>
                        <input type='text' class='form-control' name='itemCode' id='itemCode' maxlength='100' required placeholder='Enter Item Code'>
                    </div>
                </div>
                <!-- <div class='col-md-3 form-group mt-1'>
                    <div class='form-group'>
                        <label for='itemName'>Item Name<span class='text-danger'> *</span></label>
                        <input type='text' class='form-control' name='itemName' id='itemName' maxlength='50' required placeholder='Enter Machine Name'>
                    </div>
                </div> -->
                <!-- <div class='col-md-3 form-group mt-1'>
                    <div class='form-group'>
                        <label for='description'>Description</label>
                        <textarea class='form-control' name='description' id='description' maxlength=''></textarea>
                    </div>
                </div> -->



                <div class='col-md-3 form-group mt-1'>
                    <div class='form-group'>
                        <label for='sideAWidth'>Side A Width<span class='text-danger'> *</span></label>
                        <input type='text' class='form-control numberInput' name='sideAWidth' id='sideAWidth' maxlength='50' required placeholder='Enter Side A Width' required>
                    </div>
                </div>

                <div class='col-md-3 form-group mt-1'>
                    <div class='form-group'>
                        <label for='sideBWidth'>Side B Width<span class='text-danger'> *</span></label>
                        <input type='text' class='form-control numberInput' name='sideBWidth' id='sideBWidth' maxlength='50' placeholder='Enter Side B Width' required>
                    </div>
                </div>

                <div class='col-md-3 form-group mt-1'>
                    <div class='form-group'>
                        <label for='sideAThickness'>Thickness<span class='text-danger'> *</span></label>
                        <input type='text' class='form-control numberInput' name='sideAThickness' id='sideAThickness' maxlength='50' placeholder='Enter Side A Thickness' required>
                    </div>
                </div>
                <!-- <div class='col-md-3 form-group mt-1'>
                    <div class='form-group'>
                        <label for='sideBThickness'>Side B Thickness</label>
                        <input type='text' class='form-control numberInput' name='sideBThickness' id='sideBThickness' maxlength='50' placeholder='Enter Side B Thickness'>
                    </div>
                </div> -->
                <div class='col-md-3 form-group mt-3'>
                    <div class='form-group'>
                        <label for='material'> Material</label>
                        <input type='text' class='form-control' name='material' id='material' maxlength='100' placeholder='Enter Material'>
                    </div>
                </div>
                <div class='col-md-3 form-group mt-3'>
                    <div class='form-group'>
                        <label for='programLength'>Program Length<sup class='text-danger'>*</sup></label>
                        <input type='text' class='form-control numberInput' name='programLength' id='programLength' maxlength='50' placeholder='Enter Program Length' required>
                    </div>
                </div>

                <!-- <div class='col-md-3 form-group mt-3'>
                    <div class='form-group'>
                        <label for='cutRadius'>Cutting Punch Radius<span class='text-danger'> *</span></label>
                        <input type='text' class='form-control numberInput' name='cutRadius' id='cutRadius' maxlength='50' required placeholder='Enter Cutting Punch Radius' required>
                    </div>
                </div> -->

                <div class="col-md-3 form-group mt-3">
                    <label for="isActive">Status</label>
                    <select class="form-control " id="isActive" name="isActive">
                        <option value="1">Active</option>
                        <option value="0">In Active</option>
                    </select>
                </div>
            </div>

        </div>
    </div>

    <!---  itemRecipeSteps  Details --->
    <hr />
    <div class="row mt-3">
        <div class="col-md-12">


            <?php
            $db = db_connect();
            $heads =  $db->table('machineDetails')
                ->where('tenantId', 1)
                ->where('machineId', 1)
                ->get()
                ->getResultArray();

            // debug($heads);
            echo "<div class='preSelector'>";
            foreach ($heads as $head) {

                // skip cutting
                if ($head['headType'] == "Cutting") {
                    continue;
                }

                // Assuming each head has a 'headName' field
                if ($head['headType'] == "Marking") {
                    for ($i = 1; $i <= $head['markingCassets']; $i++) {

                        echo '<div class="d-inline-block form-group ms-2">';
                        echo "<label class='form-label'>" . $head['headName'] . " " . $i . "</label><br>";
                        echo "<input class='form-control' data-details='" . json_encode($head) . "' type='text'/>";
                        echo '</div>';
                    }
                } else {
                    echo '<div class="d-inline-block form-group ms-2">';
                    echo "<label class='form-label'>" . $head['headName'] . "</label><br>";
                    echo "<input class='form-control' data-details='" . json_encode($head) . "' type='text'/>";
                    echo '</div>';
                }
            }

            echo "<button type='button' class='btn btn-primary btn-sm mx-3 nextBtn'><i class='fa fa-arrow-alt-circle-right'></i> Next </button>";
            echo "<button type='button' class='btn btn-primary btn-sm mx-3 resetBtn' style='display:none;'><i class='fa fa-refresh'></i> Reset </button>";
            echo "</div>";

            ?>


            <table class="table-sm table table-bordered oneToManyWrapper mt-3 w-auto" data-group="itemRecipeSteps" style="display:none;">
                <thead>
                    <tr>
                        <th>Operation <sup class='text-danger'>*</sup></th>
                        <th>X Pos</th>
                        <th>Y Pos</th>
                        <th>Measurement Type</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="oneToManyElement">
                        <td>
                            <input type='hidden' name='itemRecipeSteps[itemRecipeStepId]' value=''>
                            <select class='form-control d-inline-block headSideDropdown' required>
                            </select>

                            <input type="hidden" class="form-control" id="opType" name='itemRecipeSteps[opType]' placeholder="Enter Value">
                            <input type="hidden" class="form-control" id="opValue" name='itemRecipeSteps[opValue]' placeholder="Enter Value">
                            <input type="hidden" class="form-control" id="side" name='itemRecipeSteps[side]' placeholder="Enter Value">

                            <button type='button' class='btn btn-primary btn-sm addOneToManyElement e2t_ignore' tabindex="-1" style="margin-top: 5px;"><i class="fa fa-plus-circle"></i></button>
                        </td>
                        <td>
                            <input type="text" class="form-control-sm " id="xPos" name='itemRecipeSteps[xPos]' placeholder="Enter X Pos">
                        </td>

                        <td>
                            <input type="text" class="form-control-sm d-inline-block" id="yPos" name='itemRecipeSteps[yPos]' placeholder="Enter Y Pos">
                            <!-- <button type='button' class='btn btn-danger btn-sm removeOneToManyElement e2t_ignore' tabindex="-1"><i class="fa fa-minus-circle"></i></button> -->
                            <!-- <button type='button' class='btn btn-danger btn-sm removeOneToManyElement e2t_ignore' tabindex="-1"><i class="fa fa-minus-circle"></i></button> -->
                        </td>

                        <td>
                            <div class="d-inline-block">
                                <select class='form-control' name='itemRecipeSteps[measurementType]' id='measurementType'>
                                    <?php
                                    $list = ["Absolute", "Incremental"];
                                    foreach ($list as $value) {
                                        echo "<option value='$value'>$value</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type='button' class='btn btn-warning btn-sm insertOneToManyElement e2t_ignore' tabindex="-1"><i class="fa fa-reply fa-flip-vertical"></i></button>
                            <button type='button' class='btn btn-danger btn-sm removeOneToManyElement e2t_ignore' tabindex="-1"><i class="fa fa-minus-circle"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>


            <!-- <button type="submit" class="btn btn-primary ">Submit</button>
            <a title="View All" href="<?php echo base_url("ItemRecipeMaster/manageItemrecipemaster"); ?>" class="btn btn-info">
                <i class="fas fa-list"></i>&nbsp;&nbsp; View All
            </a> -->
        </div>

    </div>

    </div>


    <!-- submit button -->
</form>