<?php
$config = config('AppConfig');
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Login | <?php echo $config->appName; ?></title>
    <!-- CSS files -->
    <link href="<?php echo base_url("themeAssets"); ?>/2/dist/css/tabler.min.css?1692870487" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css" rel="stylesheet" />
    <style>
        @import url('https://rsms.me/inter/inter.css');

        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }

        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
    </style>

    <?php echo view('templates/headerAssets'); ?>
</head>

<body class="d-flex flex-column" data-bs-theme="dark">
    <script src="<?php echo base_url("themeAssets"); ?>/2/dist/js/demo-theme.min.js?1692870487"></script>
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="." class="navbar-brand navbar-brand-autodark">
                    <img src="<?php echo getLogoUrl(); ?>" alt="logo" class="img-fluid" style="max-width:250px;margin:0px auto;" ; />
                </a>
            </div>

            <form action="" method="POST" id="loginForm">
                <div class="card card-md">
                    <div class="card-body">
                        <h2 class="h2 text-center mb-4">Login to your account</h2>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" placeholder="Username or Email" autocomplete="off" id="username">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">
                                Password
                                <span class="form-label-description">
                                    <a href="javascript:;" class="forgotPassword">I forgot password</a>
                                </span>
                            </label>
                            <div class="input-group input-group-flat">
                                <input type="password" class="form-control" placeholder="Your password" autocomplete="off" id="password">
                            </div>
                        </div>


                        <?php
                        if ($config->simpleCaptcha) {
                        ?>
                            <div class="mb-2">
                                <label class="form-label">Captcha</label>
                                <input type="text" class="form-control" placeholder="Captcha" autocomplete="off" id="captcha">

                                <div class="m-2">
                                    <img src="" alt="Captcha" id="captchaImage" />
                                    <a href="javascript:;" onclick="refreshCaptcha()"><i class="fa fa-sync fa-2x"></i></a>
                                </div>
                            </div>
                        <?php
                        }
                        ?>

                        <div class="mb-2">
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input" id="rememberMe" />
                                <span class="form-check-label">Remember me on this device</span>
                            </label>
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-spin fa-spinner mx-1"></i> Sign in</button>
                        </div>
                    </div>
                </div>
            </form>

            <form action="" method="POST" id="forgotPasswordForm" style="display:none;">
                <div class="card card-md">
                    <div class="card-body">
                        <h2 class="h2 text-center mb-4">Reset Password</h2>
                        <div class="mb-3">
                            <label class="form-label">Username
                                <span class="form-label-description">
                                    <a href="javascript:;" class="loginAgain">Login Again</a>
                                </span>
                            </label>
                            <input type="text" class="form-control" placeholder="Username or Email" autocomplete="off" id="resetUser">
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-spin fa-spinner mx-1"></i> Reset Password</button>
                        </div>
                    </div>
                </div>
            </form>

            <form action="" method="POST" id="totpVerification" style="display:none;">
                <div class="card card-md">
                    <div class="card-body">
                        <h2 class="h2 text-center mb-4">2FA Authentication Required</h2>
                        <div class="mb-3">
                            <label class="form-label">TOTP Secret</label>
                            <input type="text" class="form-control" placeholder="TOTP Secret" autocomplete="off" id="totpInput">
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-spin fa-spinner mx-1"></i> Verify</button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
    <!-- Libs JS -->
    <!-- Tabler Core -->
    <script src="<?php echo base_url("themeAssets"); ?>/2/dist/js/tabler.min.js?1692870487" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/js/all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <?php echo view('templates/footerAssets'); ?>
</body>

</html>