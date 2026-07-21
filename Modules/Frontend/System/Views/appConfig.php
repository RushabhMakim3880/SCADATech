<?php
$config = config('AppConfig');
?>

<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => $pageTitle]) ?>

<form method="POST" action="#" autocomplete="off"
    class="autoCrudForm"
    data-resource="api/system/getAppConfig/1"
    data-record-id="1"
    data-dropdowns='[]'>

    <div class="row">
        <div class="col-md-12">

            <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'App Config']]) ?>



            <div class="row">
                <div class="col-md-3 form-group mt-3">
                    <label for="appShortName">App Short Name</label>
                    <input type="text" class="form-control inputFocus" id="appShortName" placeholder="Enter App Short Name" name="appShortName">
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="appName">App Name</label>
                    <input type="text" class="form-control" id="appName" placeholder="Enter App Name" name="appName">
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="appTagline">App Tagline</label>
                    <input type="text" class="form-control" id="appTagline" placeholder="Enter App Tagline" name="appTagline">
                </div>


                <div class="col-md-3 form-group mt-3">
                    <label for="theme">Theme</label>
                    <input type="text" class="form-control" id="theme" placeholder="Enter Theme Number" name="theme">
                </div>


                <div class="col-md-3 form-group mt-3">
                    <label for="emailTemplate">Email Template</label>
                    <input type="text" class="form-control numberInput" id="emailTemplate" placeholder="Enter Email Template Number" name="emailTemplate" autocomplete="off">
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="cdnToLocal">CDN To Local</label>
                    <select class="form-control select2" id="cdnToLocal" name="cdnToLocal">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="combinedAssets">Combined Assets</label>
                    <select class="form-control select2" id="combinedAssets" name="combinedAssets">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>


                <div class="col-md-3 form-group mt-3">
                    <label for="chartTheme">Chart Theme</label>
                    <select class="form-control select2" id="chartTheme" name="chartTheme">
                        <option value="default">Default</option>
                        <option value="azul">Azul</option>
                        <option value="bee-inspired">Bee-Inspired</option>
                        <option value="blue">Blue</option>
                        <option value="caravan">Caravan</option>
                        <option value="carp">Carp</option>
                        <option value="cool">Cool</option>
                        <option value="dark-blue">Dark Blue</option>
                        <option value="dark-bold">Dark Bold</option>
                        <option value="dark-digerati">Dark Digerati</option>
                        <option value="dark-fresh-cut">Dark Fresh Cut</option>
                        <option value="dark-mushroom">Dark Mushroom</option>
                        <option value="dark">Dark</option>
                        <option value="eduardo">Eduardo</option>
                        <option value="forest">Forest</option>
                        <option value="fresh-cut">Fresh Cut</option>
                        <option value="fruit">Fruit</option>
                        <option value="gray">Gray</option>
                        <option value="green">Green</option>
                        <option value="helianthus">Helianthus</option>
                        <option value="infographic">Infographic</option>
                        <option value="inspired">Inspired</option>
                        <option value="jazz">Jazz</option>
                        <option value="london">London</option>
                        <option value="macarons">Macarons</option>
                        <option value="macarons2">Macarons2</option>
                        <option value="mint">Mint</option>
                        <option value="red-velvet">Red Velvet</option>
                        <option value="red">Red</option>
                        <option value="roma">Roma</option>
                        <option value="royal">Royal</option>
                        <option value="sakura">Sakura</option>
                        <option value="shine">Shine</option>
                        <option value="tech-blue">Tech Blue</option>
                        <option value="vintage">Vintage</option>
                    </select>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="manageTablePageSize">Table Page Size</label>
                    <input type="text" class="form-control numberInput" id="manageTablePageSize" placeholder="" name="manageTablePageSize">
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="manageTablePageSizeList">Table Page Size List</label>
                    <input type="text" class="form-control" id="manageTablePageSizeList" placeholder="" name="manageTablePageSizeList">
                    <small class="ms-0 text-warning">5, 10, 25, 50, 100, 250, 500, 1000, 2000</small>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="dataExportLimit">Data Export Limit</label>
                    <input type="text" class="form-control numberInput" id="dataExportLimit" placeholder="" name="dataExportLimit">
                    <small class="ms-0 text-warning">input number i.e. 2000</small>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="manageScreenIdType">Manage Data Id Show</label>
                    <select class="form-control" id="manageScreenIdType" placeholder="" name="manageScreenIdType">
                        <option value="idOnly">ID Only</option>
                        <option value="idWithIcon">ID + Icon</option>
                        <option value="iconWithId">Icon + ID</option>
                        <option value="iconOnly">Icon Only</option>
                    </select>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="manageScreenIdIcon">Manage Data Id Icon</label>
                    <input type="text" class="form-control iconPicker" id="manageScreenIdIcon" placeholder="" name="manageScreenIdIcon">
                </div>


                <!-- <div class="col-md-3 form-group mt-3">
                        <label class="form-label" for="dateFormat">Select Date Format</label>
                        <select name="dateFormat" class="form-control select2 dateFormats">
                        </select>
                    </div> -->
                <?php
                $dateFormats = dateFormats();
                $timeFormats = timeFormats();
                $dateTimeFormats = dateTimeFormats();
                ?>

                <div class="col-md-3 form-group mt-3">
                    <label class="form-label" for="dateFormat">Select Date Format</label>
                    <select name="dateFormat" class="form-control select2" id="dateFormat">
                        <?php foreach ($dateFormats as $format => $display): ?>
                            <option value="<?php echo $format; ?>"><?php echo $display; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>


                <div class="col-md-3 form-group mt-3">
                    <label class="form-label" for="timeFormat">Select Time Format</label>
                    <select name="timeFormat" class="form-control select2" id="timeFormat">
                        <?php foreach ($timeFormats as $format => $display): ?>
                            <option value="<?php echo $format; ?>"><?php echo $display; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label class="form-label" for="dateTimeFormat">Select DateTime Format</label>
                    <select name="dateTimeFormat" class="form-control select2" id="dateTimeFormat">
                        <?php foreach ($dateTimeFormats as $format => $display): ?>
                            <option value="<?php echo $format; ?>"><?php echo $display; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="pdfGenerator">Pdf Generator</label>
                    <select class="form-control select2" id="pdfGenerator" name="pdfGenerator">
                        <option value="wkhtmltopdf">wkhtmltopdf</option>
                        <option value="dompdf">dompdf</option>
                        <option value="mpdf">mpdf</option>
                    </select>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="fontSize">Font Size</label>
                    <select class="form-control select2" id="fontSize" name="fontSize">
                        <option value="0">Default</option>
                        <?php
                        for ($i = 10; $i <= 30; $i++) {
                            echo "<option value='$i'>$i</option>";
                        }
                        ?>
                    </select>
                </div>

            </div>

            <!-- submit button -->

            <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
        </div>

    </div>
    <!-- add penal for Features  -->
    <div class="row">
        <div class="col-md-12">
            <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Features']]) ?>

            <div class="row">


                <div class="col-md-3 form-group mt-3">
                    <label for="webPushNotification">Web Push Notification</label>
                    <select class="form-control select2" id="webPushNotification" name="webPushNotification">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>


                <div class="col-md-3 form-group mt-3">
                    <label for="twoFactorAuth">Two Factor Authentication</label>
                    <select class="form-control select2" id="twoFactorAuth" name="twoFactorAuth">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>


                <div class="col-md-3 form-group mt-3">
                    <label for="simpleCaptcha">Captcha</label>
                    <select class="form-control select2" id="simpleCaptcha" name="simpleCaptcha">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>


                <div class="col-md-3 form-group mt-3">
                    <label for="apiDocumentation">API Documentation</label>
                    <select class="form-control select2" id="apiDocumentation" name="apiDocumentation">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="singleSignOn">Single Sign On</label>
                    <select class="form-control select2" id="singleSignOn" name="singleSignOn">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>

            </div>
        </div>
    </div>

    <!-- add penal for Login Security  -->
    <div class="row">
        <div class="col-md-12">
            <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Login Security']]) ?>

            <div class="row">

                <div class="col-md-3 form-group mt-3">
                    <label for="maxLoginAttempts">Max Login Attempts</label>
                    <input type="text" class="form-control" id="maxLoginAttempts" placeholder="Max Login Attempts" name="maxLoginAttempts">
                </div>


                <div class="col-md-3 form-group mt-3">
                    <label for="lockoutTime">Lock Out Time</label>
                    <input type="text" class="form-control" id="lockoutTime" placeholder="Lock Out Time" name="lockoutTime">
                    <small class="ms-0 text-warning">IN Minutes</small>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="passwordExpiryDays">Password Expiry Days</label>
                    <input type="text" class="form-control" id="passwordExpiryDays" placeholder="Enter Password Expiry Days" name="passwordExpiryDays">
                    <small class="ms-0 text-warning">IN Days</small>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="passwordHistory">Password History</label>
                    <input type="text" class="form-control" id="passwordHistory" placeholder=" Password History" name="passwordHistory">
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="minPasswordLength">Minimum Password Length</label>
                    <input type="text" class="form-control" id="minPasswordLength" placeholder="Set minimum password length" name="minPasswordLength">
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="passwordStrength">Password Strength </label>
                    <select class="form-control select2" id="passwordStrength" name="passwordStrength">
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                        <option value="high">High</option>
                    </select>
                </div>

            </div>
        </div>
    </div>
    <!--  penal End Here  -->

    <!-- add penal for Notification Settings  -->

    <div class="row">
        <div class="col-md-12">
            <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Notification Settings']]) ?>

            <div class="row">

                <div class="col-md-3 form-group mt-3">
                    <label for="notificationLibrary">Library</label>
                    <select class="form-control select2" id="notificationLibrary" name="notificationLibrary">
                        <option value="Toastr">Toastr</option>
                        <option value="SweetAlert2">SweetAlert2</option>
                        <option value="Notyf">Notyf</option>
                    </select>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="notificationPositionX">Position X</label>
                    <select class="form-control select2" id="notificationPositionX" name="notificationPositionX">
                        <option value="center">Center</option>
                        <option value="left">Left</option>
                        <option value="right">Right</option>
                    </select>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="notificationPositionY">Position Y</label>
                    <select class="form-control select2" id="notificationPositionY" name="notificationPositionY">
                        <option value="top">Top</option>
                        <option value="center">Center</option>
                        <option value="bottom">Bottom</option>
                    </select>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="notificationDelay">Delay</label>
                    <input type="text" class="form-control" id="notificationDelay" placeholder="Enter value in milliseconds" name="notificationDelay">
                    <small class="ms-0 text-warning">in Milisecond</small>

                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="notificationCloseButton">Close Button</label>
                    <select class="form-control select2" id="notificationCloseButton" name="notificationCloseButton">
                        <option value="1">True</option>
                        <option value="0">False</option>
                    </select>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="notificationProgressBar">Progress Bar</label>
                    <select class="form-control select2" id="notificationProgressBar" name="notificationProgressBar">
                        <option value="1">True</option>
                        <option value="0">False</option>
                    </select>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="notificationPlaySound">Play Sound</label>
                    <select class="form-control select2" id="notificationPlaySound" name="notificationPlaySound">
                        <option value="1">True</option>
                        <option value="0">False</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <!--  penal End Here  -->

    <!-- add penal for File Upload Settings  -->
    <div class="row">
        <div class="col-md-12">
            <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'File Upload Settings']]) ?>

            <div class="row">
                <div class="col-md-3 form-group mt-3">
                    <label for="maxFileSizeMB">Maximum File Size</label>
                    <input type="text" class="form-control" id="maxFileSizeMB" placeholder="Enter Maximum File Size In MB" name="maxFileSizeMB">
                    <small class="ms-0 text-warning">in MB</small>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="maxTotalFileSizeMB">Maximum Total File Size</label>
                    <input type="text" class="form-control" id="maxTotalFileSizeMB" placeholder="Enter Maximum Total File Size In MB" name="maxTotalFileSizeMB">
                    <small class="ms-0 text-warning">in MB</small>
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="allowedFileTypes">File Types</label>
                    <input type="text" class="form-control" id="allowedFileTypes" placeholder="Comma-separated file types" name="allowedFileTypes">
                    <small class="ms-0 text-warning">jpg, jpeg, png,gif, pdf, doc, docx, xls, xlsx, ppt, pptx, txt, zip, rar</small>
                </div>

            </div>
        </div>
    </div>

    <!-- add penal for Company Details  -->
    <div class="row">
        <div class="col-md-12">
            <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Contact Details']]) ?>

            <div class="row">
                <div class="col-md-3 form-group mt-3">
                    <label for="contactEmail">Contact Email</label>
                    <input type="text" class="form-control" id="contactEmail" placeholder="Enter Email Address" name="contactEmail">
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="ownerCompanyName">Company Name</label>
                    <input type="text" class="form-control" id="ownerCompanyName" placeholder="Company Name" name="ownerCompanyName">
                </div>

                <div class="col-md-3 form-group mt-3">
                    <label for="websiteUrl">Website URL</label>
                    <input type="text" class="form-control" id="websiteUrl" placeholder="https://www.yourwebsite.com" name="websiteUrl">
                </div>

                <!-- websiteText -->
                <div class="col-md-3 form-group mt-3">
                    <label for="websiteText">Website Text</label>
                    <input type="text" class="form-control" id="websiteText" placeholder="www.yourwebsite.com" name="websiteText">
                </div>

                <!-- defaultLanguage -->
                <div class="col-md-3 form-group mt-3">
                    <label for="defaultLanguage">Default UI Language</label>
                    <select type="text" class="form-control select2" id="defaultLanguage" placeholder="Enter Default Language" name="defaultLanguage">
                        <option value="en">English</option>
                    </select>
                </div>

            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary mt-3">Submit</button>

</form>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>