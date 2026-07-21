<?php
$config = config('AppConfig');
?>

<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => '']) ?>

<div class="row">
    <div class="col-md-12">

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => $pageTitle]]) ?>

        <form method="POST" action="#" autocomplete="off"
            class="autoCrudForm"
            data-resource="api/StatusMaster/get/<?= isset($statusId) ? $statusId : '' ?>"
            data-record-id="<?= isset($statusId) ? $statusId : '' ?>"
            data-dropdowns='[]'>

            <div class="row">

                <div class="col-md-3 form-group mt-3">
                    <label for="statusName">Status Name<sup class='text-danger'>*</sup></label>
                    <input type="text" class="form-control" id="statusName" name="statusName" placeholder="Enter Status Name" required>
                </div>
                <div class="col-md-3 form-group mt-3">
                    <label for="module">Module<sup class='text-danger'>*</sup></label>
                    <input type="text" class="form-control" id="module" name="module" placeholder="Enter Module" required>
                </div>

                <div class=" col-md-3 form-group mt-3">
                    <label for="statusType">Status Type<sup class='text-danger'>*</sup></label>
                    <select class="form-control select2" id="statusType" name="statusType" required>
                        <option value="">Select Status Type</option>
                        <option value="Open">Open</option>
                        <option value="Won">Won</option>
                        <option value="Lost">Lost</option>
                    </select>
                </div>


                <div class="col-md-3 form-group mt-3">
                    <label for="isDefaultEntry">Is Default<sup class='text-danger'>*</sup></label>
                    <select class="form-control select2" id="isDefaultEntry" name="isDefaultEntry" required>
                        <option value="">Select</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="col-md-3 form-group mt-3">
                    <label for="isEditable">Is Editable<sup class='text-danger'>*</sup></label>
                    <select class="form-control select2" id="isEditable" name="isEditable" required>
                        <option value="">Select</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="col-md-3 form-group mt-3">
                    <label for="isAction">Is Action<sup class='text-danger'>*</sup></label>
                    <select class="form-control select2" id="isAction" name="isAction" required>
                        <option value="">Select</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="col-md-3 form-group mt-3">
                    <label for="sequence">Sequence</label>
                    <input type="text" class="form-control numberInput" id="sequence" name="sequence" placeholder="Enter Sequence">
                    <small class="form-text text-muted">Enter a valid number. Avoid duplicates in the sequence</small>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="icon">Icon</label>
                    <input type="text" class="form-control iconPicker" id="icon" name="icon" placeholder="Enter Icon">
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="textColor">Text Color</label>
                    <input type="text" class="form-control colorPicker" id="textColor" name="textColor" placeholder="Enter Text Color">
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="bgColor">Background Color</label>
                    <input type="text" class="form-control colorPicker" id="bgColor" name="bgColor" placeholder="Enter Background Color">
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="isActive">Is Active<sup class='text-danger'>*</sup></label>
                    <select class="form-control select2" id="isActive" name="isActive" required>
                        <option value="">Select</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

            </div>
            <!-- submit button -->
            <button type="submit" class="btn btn-primary mt-3">Submit</button>

            <a title="View All" href="<?php echo base_url("statusMaster/manageStatus"); ?>" class="btn btn-info  mt-3">
                <i class="fas fa-list"></i>&nbsp;&nbsp; View All
            </a>

        </form>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

</div>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>