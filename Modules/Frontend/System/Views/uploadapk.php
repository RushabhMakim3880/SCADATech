<?php
$config = config('AppConfig');
?>

<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => $pageTitle]) ?>

<div class="row">
    <div class="col-md-12">

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Upload APK']]) ?>

        <form method="POST" action="#" autocomplete="off"
            class="autoCrudForm"
            data-resource="api/system/uploadapk"
            data-record-id=""
            data-dropdowns='[]'>

            <div class="row">
                <div class="col-md-3 form-group mt-3" style="display: none;">
                    <label for="imagename"></label>
                    <input type="text" class="form-control" id="imagename" name="imagename">
                </div>


                <div class="col-md-3 form-group mt-3">
                    <label>Upload APK File</label>
                    <input type="file" name="apkFile" class="form-control" id="apkFile" accept=".apk">
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Submit</button>
        </form>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

</div>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>