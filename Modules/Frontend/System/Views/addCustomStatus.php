<?php
$config = config('AppConfig');
?>
<!-- <a title="View All Entries" href="<?php echo base_url("system/manageCustomStatus"); ?>" class="btn btn-info float-end m-3 mt-3">
    <i class="fas fa-list"></i>&nbsp;&nbsp; View All
</a> -->
<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' =>'']) ?>

<div class="row">
    <div class="col-md-12">

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' =>  $pageTitle]]) ?>

        <form method="POST" action="#" autocomplete="off"
            class="autoCrudForm"
            data-resource="api/customStatusMaster/get/<?= isset($fieldId) ? $fieldId : '' ?>"
            data-record-id="<?= isset($fieldId) ? $fieldId : '' ?>"

            data-dropdowns='[{"name": "statusId", "endpoint": "/api/customStatusMaster/getStatusData"}]'>


            <div class="row">

                <div class="col-md-3 form-group mt-3">
                    <label for="fieldName">Field Name<sup class='text-danger'>*</sup></label>
                    <input type="text" class="form-control" id="fieldName" name="fieldName" placeholder="Enter Field Name" required>
                </div>




                <div class="col-md-3 form-group mt-3">
                    <label for="statusId">Status<sup class="text-danger">*</sup></label>
                    <select class="form-control select2" id="statusId" name="statusId" required>
                        <option value="0">Select Status Name</option>
                    </select>
                </div>


                <div class=" col-md-3 form-group mt-3">
                    <label for="fieldType">Field Type<sup class='text-danger'>*</sup></label>
                    <select class="form-control select2" id="fieldType" name="fieldType" required>
                        <option value="">Select Field Type</option>
                        <option value="text">Text</option>
                        <option value="dropdown">Dropdown</option>
                        <option value="number">Number</option>
                        <option value="date">Date</option>

                    </select>
                </div>


                <!-- <div class="col-md-3 form-group mt-3">
                    <label for="fieldOptions">Field Options</label>
                    <textarea class="form-control" id="fieldOptions" placeholder="Enter Field Options" name="fieldOptions"></textarea>

                </div> -->


                <div class="col-md-3 form-group mt-3">
                    <label for="isRequired">Is Required</label>
                    <select class="form-control select2" id="isRequired" name="isRequired">
                        <option value="">Select</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>


                <div class="col-md-3 form-group mt-3">
                    <label for="isActive">Is Active</label>
                    <select class="form-control select2" id="isActive" name="isActive">
                        <option value="">Select</option>
                        <option value="1">Active</option>
                        <option value="0">In Active</option>
                    </select>
                </div>



            </div>
            <!-- submit button -->
            <button type="submit" class="btn btn-primary mt-3">Submit</button>

        </form>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

</div>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>