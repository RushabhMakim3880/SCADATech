<?php
$config = config('AppConfig');
?>

<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => '']) ?>

<div class="row">
    <div class="col-md-12">

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => $pageTitle]]) ?>

        <form method="POST" action="#" autocomplete="off"
            class="autoCrudForm"
            data-resource="api/locationMaster/get/<?= isset($locationId) ? $locationId : '0' ?>"
            data-record-id="<?= isset($locationId) ? $locationId : '' ?>"
            data-dropdowns='[
                                

                ]'>

            <div class="row">

                <!-- Location Name -->
                <div class="col-md-3 form-group mt-3">
                    <label for="locationName">Location Name <sup class="text-danger">*</sup> </label>
                    <input type="text" class="form-control" id="locationName" name="locationName" placeholder="Enter Name of Location" required>
                </div>


                <!-- Location Type -->
                <div class="col-md-3 form-group mt-3">
                    <label for="locationType">Location Type </label>
                    <select class="form-control" id="locationType" name="locationType">
                        <option value="">Select Location Type</option>
                        <option value="Country">Country</option>
                        <option value="State">State</option>
                        <option value="District">District</option>
                        <option value="Taluka">Taluka</option>
                        <option value="Village">Village</option>
                    </select>
                </div>

                <!-- Parent Location -->
                <!-- <div class="col-md-3 form-group mt-3">
                    <label for="parentLocationId">Parent Location</label>
                    <select class="form-control select2" id="parentLocationId" name="parentLocationId">
                        <option value="0">Select location</option>
                    </select>
                </div> -->


                <!-- groupId -->
                <div class="col-md-3 form-group mt-3">
                    <label for="parentLocationId">Parent Location</label>
                    <select class="form-control select2" data-selecttype="ajax" id="parentLocationId" name="parentLocationId" data-endpoint="/api/locationMaster/getLocations">
                    </select>
                </div>



            </div>

            <!-- submit button -->
            <button type="submit" class="btn btn-primary mt-3">Submit</button>

            <a title="View All" href="<?php echo base_url("locationMaster/manageLocationMaster"); ?>" class="btn btn-info  mt-3">
                <i class="fas fa-list"></i>&nbsp;&nbsp; View All
            </a>

        </form>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

</div>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>