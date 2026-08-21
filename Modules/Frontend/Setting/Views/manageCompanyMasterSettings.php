<?php
$config = config('AppConfig');
use App\Libraries\UserPermissionLib;
?>

<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => '']) ?>

<div class="row">
    <div class="col-md-12">
        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => $pageTitle]]) ?>

        <form method="POST" action="api/setting/saveCompanyMasterSetting" autocomplete="off"
            class="autoCrudForm"
            data-resource="api/setting/getCompanyMasterSetting"
            data-record-id="1">

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="sideAWidth" class="form-label fw-bold">Side A Width</label>
                        <input type="text" class="form-control numberInput virtualNumKeypad" name="sideAWidth" id="sideAWidth" placeholder="Enter Side A Width">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="sideBWidth" class="form-label fw-bold">Side B Width</label>
                        <input type="text" class="form-control numberInput virtualNumKeypad" name="sideBWidth" id="sideBWidth" placeholder="Enter Side B Width">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="sideAThickness" class="form-label fw-bold">Side A Thickness</label>
                        <input type="text" class="form-control numberInput virtualNumKeypad" name="sideAThickness" id="sideAThickness" placeholder="Enter Side A Thickness">
                    </div>
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa fa-save me-1"></i> Save Settings
                </button>
            </div>
        </form>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>
</div>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>
