<?php
$config = config('AppConfig');
?>

<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => $pageTitle]) ?>

<div class="row">
    <div class="col-md-12">

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Add Logo & Background']]) ?>

        <form method="POST" class="brandingForm" action="#" autocomplete="off">

            <div class="row">

                <!-- Company Logo (Dark Logo) -->
                <div class="col-md-3 form-group mt-3">
                    <label for="darkBg">Dark Logo</label>
                    <div class="imageUploader">
                        <div class="user_pic_container_box">
                            <img data-type="square" data-width="200" data-height="100" class='user_pic_container' src='<?php echo $darkBg; ?>' />
                        </div>
                        <a class="imgremovebtn" href="#"><i class="fa fa-times"></i></a>
                        <input readonly style="display: none" id='file_browse' type="file" accept="image/*">
                        <input type="hidden" name="darkBg" class="value_container" value="nochange" />
                    </div>
                </div>

                <!-- Favicon (Favicon) -->
                <div class="col-md-3 form-group mt-3">
                    <label for="favicon">Favicon</label>
                    <div class="imageUploader">
                        <div class="user_pic_container_box">
                            <img data-type="square" data-width="512" class='user_pic_container' src='<?php echo $favicon; ?>' />
                        </div>
                        <a class="imgremovebtn" href="#"><i class="fa fa-times"></i></a>
                        <input readonly style="display: none" id='file_browse' type="file" accept="image/*">
                        <input type="hidden" name="favicon" class="value_container" value="nochange" />
                    </div>
                </div>

                <!-- Background image (Login Background Image) -->
                <div class="col-md-3 form-group mt-3">
                    <label for="loginBg">Background image</label>
                    <div class="imageUploader">
                        <div class="user_pic_container_box">
                            <img data-type="square" data-width="1920" data-height="1080" class='user_pic_container' src='<?php echo $loginBg; ?>' />
                        </div>
                        <a class="imgremovebtn" href="#"><i class="fa fa-times"></i></a>
                        <input readonly style="display: none" id='file_browse' type="file" accept="image/*">
                        <input type="hidden" name="loginBg" class="value_container" value="nochange" />
                    </div>
                </div>

                <!-- Login Screen Logo (Light Logo) -->
                <div class="col-md-3 form-group mt-3">
                    <label for="lightBg">Light Logo</label>
                    <div class="imageUploader">
                        <div class="user_pic_container_box">
                            <img data-type="square" data-width="200" data-height="100" class='user_pic_container' src='<?php echo $lightBg; ?>' />
                        </div>
                        <a class="imgremovebtn" href="#"><i class="fa fa-times"></i></a>
                        <input readonly style="display: none" id='file_browse' type="file" accept="image/*">
                        <input type="hidden" name="lightBg" class="value_container" value="nochange" />
                    </div>
                </div>

                <!-- Print Logo (Print Logo) -->
                <div class="col-md-3 form-group mt-3">
                    <label for="printLg">Print Logo</label>
                    <div class="imageUploader">
                        <div class="user_pic_container_box">
                            <img data-type="square" data-width="200" data-height="100" class='user_pic_container' src='<?php echo $printLg; ?>' />
                        </div>
                        <a class="imgremovebtn" href="#"><i class="fa fa-times"></i></a>
                        <input readonly style="display: none" id='file_browse' type="file" accept="image/*">
                        <input type="hidden" name="printLg" class="value_container" value="nochange" />
                    </div>
                </div>

            </div>

            <!-- submit button -->
            <button type="submit" class="btn btn-primary mt-3">Submit</button>

        </form>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

</div>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>