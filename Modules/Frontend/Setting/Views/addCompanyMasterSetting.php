<?php
$config = config('AppConfig');
?>

<form method="POST" action="api/setting/saveCompanyMasterSettingItem" autocomplete="off"
    class="autoCrudForm"
    data-resource="api/setting/getCompanyMasterSettingById/<?= isset($id) ? $id : '' ?>"
    data-record-id="<?= isset($id) ? $id : '' ?>">

    <input type="hidden" name="companySettingsId" id="companySettingsId" value="<?= isset($id) ? $id : '' ?>">

    <div class="row">
        <div class="col-md-6 form-group mt-2">
            <label for="key">Setting Key <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="key" id="key" placeholder="Enter setting key" required>
        </div>

        <div class="col-md-6 form-group mt-2">
            <label for="companyId">Company ID</label>
            <input type="number" class="form-control" name="companyId" id="companyId" value="1" placeholder="1">
        </div>

        <div class="col-md-12 form-group mt-2">
            <label for="value">Setting Value</label>
            <textarea class="form-control" name="value" id="value" rows="4" placeholder="Enter setting value"></textarea>
        </div>
    </div>
</form>
