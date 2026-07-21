<?php
$config = config('AppConfig');
?>

<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => '']) ?>

<div class="row">
    <div class="col-md-12">

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => $pageTitle]]) ?>

        <form method="POST" action="#" autocomplete="off"
            class="autoCrudForm"
            data-resource="api/tenantMaster/get/<?= isset($tenantId) ? $tenantId : '0' ?>"
            data-record-id="<?= isset($tenantId) ? $tenantId : '' ?>"
            data-dropdowns='[]'>

            <div class="row">

                <div class="col-md-3 form-group mt-3">
                    <label for="subDomain">Sub Domain<sup class='text-danger'>*</sup></label>
                    <input type="text" class="form-control" id="subDomain" name="subDomain" placeholder="Enter Sub Domain" required>
                </div>
                <div class="col-md-3 form-group mt-3">
                    <label for="customDomain">Custom Domain</label>
                    <input type="text" class="form-control" id="customDomain" name="customDomain" placeholder="Enter Custom Domain">
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="tenantName">Tenant Name</label>
                    <input type="text" class="form-control" id="tenantName" name="tenantName" placeholder="Enter Tenant Name">
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="companyName">Company Name</label>
                    <input type="text" class="form-control" id="companyName" name="companyName" placeholder="Enter Company Name">
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="mobile">Mobile </label>
                    <input type="text" class="form-control internationalNumber" id="mobile" placeholder="Enter Mobile No" name="mobile">
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" placeholder="test@gmail.com" name="email">
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="companyAddress">Company Address</label>
                    <textarea class="form-control" id="companyAddress" name="companyAddress" placeholder="Enter Company Address"></textarea>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="locationId">Location </label>
                    <select class="form-control select2" data-selecttype="ajax" id="locationId" name="locationId" data-endpoint="/api/locationMaster/getLocations">
                        <option value="">Default</option>

                    </select>
                </div>

                <!-- tenantType -->
                <div class="col-md-3 form-group mt-3">
                    <label for="tenantType">Tenant Type<sup class='text-danger'>*</sup></label>
                    <select class="form-control select2" id="tenantType" name="tenantType" required>
                        <option value="">Select</option>
                        <option value="clientLive">Client Live</option>
                        <option value="clientTrial">Client Trial</option>
                        <option value="clientFree">Client Free</option>
                        <option value="personalLive">Personal Live</option>
                        <option value="personalDemo">Personal Demo</option>
                    </select>
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

            <a title="View All" href="<?php echo base_url("tenantMaster/manageTenant"); ?>" class="btn btn-info  mt-3">
                <i class="fas fa-list"></i>&nbsp;&nbsp; View All
            </a>


        </form>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

</div>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>