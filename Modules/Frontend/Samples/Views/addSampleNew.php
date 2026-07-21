<style>
    .priority-label {
        cursor: pointer;
        margin-right: 10px;
        text-align: center;
    }

    .priority-label input {
        display: none;
        /* Hide the default radio button */
    }

    .priority-img {
        width: 40px;
        height: 40px;
        border-radius: 5px;
        transition: all 0.3s ease-in-out;
        filter: grayscale(100%);
        /* Default: All images in grayscale */
    }

    /* When a radio button is selected, its corresponding image is shown in full color */
    .priority-label input:checked+.priority-img {
        filter: grayscale(0);
    }
</style>


<?php
$config = config('AppConfig');
?>

<a class='btn btn-primary float-end m-3' href="<?= base_url('samples/manageSampleNew') ?>"><i class="fa fa-list"></i> View All</a>
<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => $pageTitle]) ?>
<div class="clearfix"></div>
<form method="POST" autoCache action="#" autocomplete="off"
    class="autoCrudForm"
    data-resource="api/newSample/get/<?= isset($newSampleId) ? $newSampleId : '' ?>"
    data-record-id="<?= isset($newSampleId) ? $newSampleId : '' ?>"
    data-dropdowns='[
            {"name": "simpleDropdown", "endpoint": "/api/newSample/loadSimpleDropdown"}
                ]'>

    <div class="row">
        <div class="col-md-12">

            <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Add New Sample']]) ?>

            <div class="row">
                <div class="col-md-6 mb-3 col-lg-4 col-xl-3">
                    <label class="form-label">Sample Image</label>
                    <div class="imageUploader">
                        <div class="user_pic_container_box">
                            <img data-type="circle" data-width="500" class='user_pic_container' src='<?php echo $profile_pic; ?>' />
                        </div>
                        <a class="imgremovebtn" href="#"><i class="fa fa-times"></i></a>
                        <input readonly style="display: none" id='file_browse' type="file" accept="image/*">
                        <input type="hidden" name="profile_pic" class="value_container" value="nochange" />
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-3 form-group mt-3">
                    <label for="sampleDate">Date</label>
                    <input id="sampleDate" type="text" name="sampleDate" class="form-control datePicker" />
                </div>



                <div class="col-md-3 form-group mt-3">
                    <label for="newSampleName">New Sample Name<sup class='text-danger'>*</sup></label>
                    <input type="text" class="form-control" id="newSampleName" name="newSampleName" placeholder="Enter New Sample Name" required>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="price">Price</label>
                    <input id="price" type="text" name="price" class="form-control numberInput" placeholder="Enter Price " />
                </div>

                <!-- <div class="col-md-3 form-group mt-3">
                    <label for="locationId">Location</label>
                    <select class="form-control select2" id="locationId" name="locationId">
                        <option value="0">Select Location</option>
                    </select>
                </div> -->

                <div class="col-md-3 form-group mt-3">
                    <label for="locationId">Location</label>
                    <select class="form-control select2" data-selecttype="ajax" id="locationId" name="locationId" data-endpoint="/api/locationMaster/getLocations">
                    </select>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="simpleDropdown">Simple Dropdown</label>
                    <select class="form-control select2" id="simpleDropdown" name="simpleDropdown" data-dropdown="simpleDropdown">
                        <option value="0">Select Simple Dropdown</option>
                    </select>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="simpleDropdownMultiple">Simple Dropdown Multi</label>
                    <select class="form-control select2" id="simpleDropdownMultiple" name="simpleDropdownMultiple" multiple data-endpoint="/api/locationMaster/getLocations" data-selecttype="ajax">

                    </select>
                </div>


                <div class="col-md-3 form-group mt-3">
                    <label for="colorCode">Color Code<sup class='text-danger'>*</sup></label>
                    <input type="text" class="form-control colorPicker" id="colorCode" name="colorCode" placeholder="Enter Color Code" required>
                </div>


                <div class="col-md-3 form-group mt-3">

                    <label for="iconCode">Icon Code<sup class='text-danger'>*</sup></label>
                    <input type="text" class="form-control iconPicker" id="iconCode" name="iconCode" placeholder="Enter Icon Code" required>
                </div>


                <div class="col-md-3 form-group mt-3">
                    <label for="isActive">Active</label>
                    <select class="form-control select2" id="isActive" name="isActive" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="category">Category</label>
                    <select class="form-control select2" id="category" name="category">
                        <option value="one">One</option>
                        <option value="two">Two</option>
                        <option value="three">Three</option>
                    </select>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="priority">Priority</label>
                    <div class="d-flex">
                        <label class="priority-label">
                            <input type="radio" name="priority" value="hot">
                            <img src="/Modules/Samples/img/hotMeter.png" alt="Hot" class="priority-img">
                        </label>

                        <label class="priority-label">
                            <input type="radio" name="priority" value="warm" checked> <!-- Default selected -->
                            <img src="/Modules/Samples/img/warmMeter.png" alt="Warm" class="priority-img">
                        </label>

                        <label class="priority-label">
                            <input type="radio" name="priority" value="cold">
                            <img src="/Modules/Samples/img/coldMeter.png" alt="Cold" class="priority-img">
                        </label>
                    </div>
                </div>

                <!-- <timepicker> -->
                <div class="col-md-3 form-group mt-3">
                    <label for="timepicker">Time</label>
                    <input id="timepicker" type="text" name="timepicker" class="form-control timePicker" />
                </div>

                <!-- datetimepicker -->
                <!-- <div class="col-md-3 form-group mt-3"> -->



                <div class="col-md-3 form-group mt-3">
                    <label for="checkBoxes">CheckBoxes</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkbox1" name="checkboxes" value="1" />
                        <label class="form-check-label" for="checkbox1">Checkbox</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkbox2" name="checkboxes" value="2" />
                        <label class="form-check-label" for="checkbox2">Checkbox</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkbox3" name="checkboxes" value="3" />
                        <label class="form-check-label" for="checkbox3">Checkbox</label>
                    </div>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="checkBoxes">Radios</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="radio1" name="radios" value="1" />
                        <label class="form-check-label" for="radio1">radio 1</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="radio2" name="radios" value="2" />
                        <label class="form-check-label" for="radio2">radio 2</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="radio3" name="radios" value="3" />
                        <label class="form-check-label" for="radio3">radio 3</label>
                    </div>
                </div>

                <div class='col-md-3' data-dependent-on="radios" data-dependent-value="3,2">

                    <label for="dateTime">Date Time</label>
                    <input id="dateTime" type="text" name="dateTime" class="form-control dateTimePicker" />
                </div>
                <!-- <textarea -->

                <!-- <div class="col-md-3 form-group mt-3"> -->
                <div class='col-md-3' data-dependent-on="checkboxes" data-dependent-value="3">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" placeholder="Enter Description"></textarea>
                </div>


            </div>


            <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
        </div>

    </div>

    <div class="row">
        <div class="col-md-12">

            <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'One To Many Example']]) ?>

            <table class="table table-bordered oneToManyTable">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Description</th>
                        <th>City</th>
                        <th>District</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="oneToManyRow">
                        <td>
                            <input type='hidden' name='manyRows[sampleNewDetailId]' value=''>
                            <select class='form-control select2 d-inline-block' name='manyRows[itemId]' data-selecttype="ajax" data-endpoint="/api/newSample/getAjaxItem">
                            </select>
                            <button type='button' class='btn btn-primary btn-sm addOneToManyRow e2t_ignore' tabindex="-1"><i class="fa fa-plus-circle"></i></button>

                        </td>
                        <td>
                            <input type='text' name='manyRows[discription]' class='form-control' placeholder='Enter Description'>
                        </td>
                        <td>
                            <select class='form-control select2' name='manyRows[cityId]' data-dropdown='simpleDropdown'>
                            </select>
                        </td>
                        <td>
                            <div class="d-inline-block">
                                <select class='form-control select2 d-inline-block' name='manyRows[districtId]' data-selecttype="ajax" data-endpoint="/api/locationMaster/getLocations/District">
                                </select>
                            </div>
                            <button type='button' class='btn btn-danger btn-sm removeOneToManyRow e2t_ignore' tabindex="-1"><i class="fa fa-minus-circle"></i></button>

                        </td>
                    </tr>
                    <tr class="oneToManyTotalRow">
                        <td class='text-end'>Total:</td>
                        <td>
                            <input type='text' name='total' class='form-control' readonly>
                        </td>
                    </tr>
                    <tr class="oneToManyTotalRow">
                        <td class='text-end'>Total:</td>
                        <td class='text-end'>Total:</td>
                        <td>
                            <input type='text' name='total' class='form-control' readonly>
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
        </div>



    </div>

    <!-- submit button -->
    <button type="submit" class="btn btn-primary">Submit</button>

    <a href="<?= base_url('samples/manageSampleNew') ?>" class="btn btn-primary" data-title="View All Sample">View All</a>

</form>
<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>