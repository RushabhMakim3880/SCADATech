<?php
$config = config('AppConfig');
?>


<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => '']) ?>

<div class="row">
    <div class="col-md-12">

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Add User']]) ?>

        <form method="POST" action="#" autocomplete="off"
            class="autoCrudForm"
            data-resource="api/users/get/<?= isset($userId) ? $userId : '' ?>"
            data-record-id="<?= isset($userId) ? $userId : '' ?>"
            data-dropdowns='[
                {"name": "groupId", "endpoint": "/api/users/groups"}

            ]'>

            <!-- <div class="row">
                <div class="col-md-6 mb-3 col-lg-4 col-xl-3">
                    <label class="form-label">Member Image</label>
                    <div class="imageUploader">
                        <div class="user_pic_container_box">
                            <img data-type="circle" data-width="500" class='user_pic_container' src='<?php echo $profile_pic; ?>' />
                        </div>
                        <a class="imgremovebtn" href="#"><i class="fa fa-times"></i></a>
                        <input readonly style="display: none" id='file_browse' type="file" accept="image/*">
                        <input type="hidden" name="profile_pic" class="value_container" value="nochange" />
                    </div>
                </div>
            </div> -->


            <div class="row">
                <!-- <div class="col-md-3 form-group mt-3">
                    <label for="username">Date Test</label>
                    <input type="text" class="form-control dateTimePicker" id="date" placeholder="date" name="date" autocomplete="off" value="">
                </div> -->

                <div class="col-md-3 form-group mt-3">
                    <label for="username">Username</label>
                    <input type="text" class="form-control inputFocus" id="username" placeholder="Username" name="username" autocomplete="off">
                </div>
                <!-- <div class="col-md-3 form-group mt-3">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" placeholder="Email" name="email">
                </div> -->
                <!-- firstName -->
                <div class="col-md-3 form-group mt-3">
                    <label for="firstName">First Name</label>
                    <input type="text" class="form-control" id="firstName" placeholder="First Name" name="firstName">
                </div>
                <!-- lastName -->
                <div class="col-md-3 form-group mt-3">
                    <label for="lastName">Last Name</label>
                    <input type="text" class="form-control" id="lastName" placeholder="Last Name" name="lastName">
                </div>
                <!-- mobile -->
                <!-- <div class="col-md-3 form-group mt-3">
                    <label for="mobile">Mobile</label>
                    <input type="text" class="form-control internationalNumber" id="mobile" placeholder="Mobile" name="mobile">
                </div> -->

                <?php if (isset($loginUser) && $loginUser != '1'): ?>

                    <!-- groupId -->
                    <div class="col-md-3 form-group mt-3">
                        <label for="groupId">Group</label>
                        <select class="form-control select2" id="groupId" name="groupId" required>
                            <option value="0">Select Group</option>
                            <option value="1">Admin asdf</option>
                        </select>
                    </div>

                    <!-- groupId -->
                    <!-- <div class="col-md-3 form-group mt-3">
                    <label for="testDropDown">Test Dropdown</label>
                    <select class="form-control select2" data-selectype="ajax" id="testDropDown" name="testDropDown" data-endpoint="/api/users/testDropDown">
                    </select>
                </div> -->

                    <!-- isActive -->
                    <div class="col-md-3 form-group mt-3">
                        <label for="isActive">Active</label>
                        <select class="form-control select2" id="isActive" name="isActive" required>
                            <option value="1" data-test='1'>Yes</option>
                            <option value="0" data-test='0'>No</option>
                        </select>
                    </div>
                <?php endif; ?>



                <!-- password -->
                <div class="col-md-3 form-group mt-3">
                    <label for="password999">Password</label>
                    <input type="password" class="form-control" id="password999" placeholder="Password" name="password999" autocomplete="off">
                </div>
            </div>

            <!-- submit button -->
            <button type="submit" class="btn btn-primary mt-3">Submit</button>
            <a title="View All" href="<?php echo base_url("users/manageUsers"); ?>" class="btn btn-info  mt-3">
                <i class="fas fa-list"></i>&nbsp;&nbsp; View All
            </a>

        </form>


        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

</div>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>