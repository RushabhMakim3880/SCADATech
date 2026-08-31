<?php
$config = config('AppConfig');
?>

<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => '']) ?>

<form method="POST" action="#" autocomplete="off"
    class="autoCrudForm"
    data-resource="api/setting/getSetting/1"
    data-record-id="1"
    data-dropdowns='[]'>

    <div class="row">
        <div class="col-md-12">

            <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Setting']]) ?>

            <div class="row">

                <div class="col-md-3 form-group mt-3">
                    <label for="name">Name<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" placeholder="Enter Name" name="name" required>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="gstNo">Gst No.</label>
                    <input type="text" class="form-control" id="gstNo" placeholder="Enter Gst No." name="gstNo">
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="phone">Phone No.</label>
                    <input type="text" class="form-control numberInput" maxlength="12" id="phone" placeholder="Enter 12 Digit Mobile Number" name="phone">
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="address">Address</label>
                    <input type="text" class="form-control" id="address" name="address">
                </div>

            </div>


            <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
        </div>
    </div>

    <button type="submit" class="btn btn-primary mt-3">Submit</button>

</form>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>