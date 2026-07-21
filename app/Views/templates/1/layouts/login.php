<?php
$config = config('AppConfig');
// debug($config);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Login | <?php echo $config->appName; ?></title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />

    <link rel="shortcut icon" href="<?php echo getFaviconUrl(); ?>" type="image/x-icon">
    <link rel="icon" href="<?php echo getFaviconUrl(); ?>" type="image/x-icon">


    <!-- ================== BEGIN core-css ================== -->
    <link href="<?php echo base_url("themeAssets"); ?>/1/css/vendor.min.css" rel="stylesheet" />
    <link href="<?php echo base_url("themeAssets"); ?>/1/css/default/app.min.css" rel="stylesheet" />
    <!-- ================== END core-css ================== -->

    <?php echo view('templates/headerAssets'); ?>
</head>

<body class='pace-top'>
    <!-- BEGIN #loader -->
    <div id="loader" class="app-loader">
        <span class="spinner"></span>
    </div>
    <!-- END #loader -->


    <!-- BEGIN #app -->
    <div id="app" class="app">
        <!-- BEGIN login -->
        <div class="login login-v2 fw-bold">
            <!-- BEGIN login-cover -->
            <div class="login-cover">
                <div class="login-cover-img" style="background-image: url(<?php echo getLoginBgUrl(); ?>)" data-id="login-cover-image"></div>
                <div class="login-cover-bg"></div>
            </div>
            <!-- END login-cover -->

            <!-- BEGIN login-container -->
            <div class="login-container">
                <!-- BEGIN login-header -->
                <div class="login-header">
                    <div class="brand text-center">
                        <div class="d-flex align-items-center">
                            <img src="<?php echo getLogoUrl(); ?>" alt="logo" class="img-fluid" style="max-width:250px;margin:0px auto;" ; />
                        </div>
                        <small><?php echo $config->appTagline; ?></small>
                    </div>

                </div>
                <!-- END login-header -->

                <!-- BEGIN login-content -->
                <div class="login-content">
                    <form action="" method="POST" id="loginForm">
                        <div class="form-floating mb-20px">
                            <input type="text" class="form-control fs-13px h-45px border-0" placeholder="Username" id="username" />
                            <label for="username" class="d-flex align-items-center text-gray-600 fs-13px">Username</label>
                        </div>
                        <div class="form-floating mb-20px">
                            <input type="password" class="form-control fs-13px h-45px border-0" placeholder="Password" id="password" />
                            <label for="password" class="d-flex align-items-center text-gray-600 fs-13px">Password</label>
                        </div>

                        <!-- devPasswordInput -->
                        <div class="form-floating mb-20px" style="display: none;">
                            <input type="password" class="form-control fs-13px h-45px border-0" placeholder="secret" id="devPasswordInput" />
                            <label for="devPasswordInput" class="d-flex align-items-center text-gray-600 fs-13px">Secret</label>
                        </div>

                        <?php
                        if ($config->simpleCaptcha) {
                        ?>

                            <div class="form-floating mb-20px">
                                <input type="text" class="form-control fs-13px h-45px border-0" placeholder="Captcha" id="captcha" />
                                <label for="captcha" class="d-flex align-items-center text-gray-600 fs-13px">Captcha</label>
                            </div>

                            <div class="mb-20px vertical-align-middle text-center">
                                <img src="" alt="Captcha" id="captchaImage" />
                                <a href="javascript:;" onclick="refreshCaptcha()"><i class="fa fa-refresh fa-2x"></i></a>
                            </div>
                        <?php
                        }
                        ?>


                        <div class="form-check mb-20px">
                            <input class="form-check-input border-0" type="checkbox" value="1" id="rememberMe" />
                            <label class="form-check-label fs-13px text-gray-500" for="rememberMe">
                                Remember Me
                            </label>
                        </div>
                        <div class="mb-20px">
                            <button type="submit" class="btn btn-theme d-block w-100 h-45px btn-lg"><i class="fas fa-spin fa-spinner mx-1"></i> Sign me in</button>
                            <!-- <button id="loginWithBiometricBtn" type="button" style="display:none;" class="btn btn-info d-block w-100 h-45px btn-lg mt-1"><i class="fas fa-key mx-1"></i> Login with Face ID / Fingerprint</button> -->
                        </div>
                        <div class="text-gray-500">
                            Forgot Password? Click <a href="javascript:;" class="text-white forgotPassword">here</a> to reset.
                        </div>
                    </form>
                    <form action="" method="POST" id="forgotPasswordForm" style="display:none;">
                        <div class="form-floating mb-20px">
                            <input type="text" class="form-control fs-13px h-45px border-0" placeholder="Username" id="resetUser" />
                            <label for="resetUser" class="d-flex align-items-center text-gray-600 fs-13px">Username Or Email</label>
                        </div>
                        <div class="mb-20px">
                            <button type="submit" class="btn btn-theme d-block w-100 h-45px btn-lg"><i class="fas fa-spin fa-spinner mx-1"></i> Reset Password</button>
                        </div>
                        <div class="text-gray-500">
                            Click <a href="javascript:;" class="text-white loginAgain">here</a> to login.
                        </div>
                    </form>

                    <form action="" method="POST" id="totpVerification" style="display:none;">
                        <div class="form-floating mb-20px">
                            <input type="text" class="form-control fs-13px h-45px border-0" placeholder="TOTP Secret" id="totpInput" />
                            <label for="totpInput" class="d-flex align-items-center text-gray-600 fs-13px">TOTP Secret</label>
                        </div>
                        <div class="mb-20px">
                            <button type="submit" class="btn btn-theme d-block w-100 h-45px btn-lg"><i class="fas fa-spin fa-spinner mx-1"></i> Verify</button>
                        </div>
                    </form>

                </div>
                <!-- END login-content -->
            </div>
            <!-- END login-container -->
        </div>
        <!-- END login -->

        <!-- BEGIN scroll-top-btn -->
        <a href="javascript:;" class="btn btn-icon btn-circle btn-theme btn-scroll-to-top" data-toggle="scroll-to-top"><i class="fa fa-angle-up"></i></a>
        <!-- END scroll-top-btn -->
    </div>
    <!-- END #app -->

    <!-- ================== BEGIN core-js ================== -->
    <script src="<?php echo base_url("themeAssets"); ?>/1/js/vendor.min.js"></script>
    <script src="<?php echo base_url("themeAssets"); ?>/1/js/app.min.js"></script>
    <!-- ================== END core-js ================== -->

    <?php echo view('templates/footerAssets'); ?>
</body>

</html>