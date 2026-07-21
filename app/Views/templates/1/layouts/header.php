<?php
$config = config('AppConfig');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title><?php echo $pageTitle ?? ""; ?> | <?php echo $config->appName ?: "My Web App"; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <meta content="" name="description" />
    <meta content="" name="author" />
    <meta name="mobile-web-app-capable" content="yes">

    <!-- Required for iOS PWA -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="<?php echo $config->appName; ?>">
    <link rel="apple-touch-icon" href="<?php echo getFaviconUrl(); ?>">
    <link rel="apple-touch-icon" sizes="192x192" href="<?php echo getFaviconShortUrl(); ?>">

    <!-- add favicon -->
    <link rel="shortcut icon" href="<?php echo getFaviconUrl(); ?>" type="image/x-icon">
    <link rel="icon" href="<?php echo getFaviconUrl(); ?>" type="image/x-icon">


    <!-- add manifest.json -->
    <link rel="manifest" href="<?php echo base_url("manifest.json"); ?>">

    <!-- ================== BEGIN core-css ================== -->
    <link href="<?php echo base_url("themeAssets"); ?>/1/css/vendor.min.css" rel="stylesheet" />
    <link href="<?php echo base_url("themeAssets"); ?>/1/css/default/app.min.css" rel="stylesheet" />
    <!-- ================== END core-css ================== -->

    <?php echo view('templates/headerAssets'); ?>
    <script>
        var WPN = <?php echo (int)$config->webPushNotification; ?>;
    </script>
</head>