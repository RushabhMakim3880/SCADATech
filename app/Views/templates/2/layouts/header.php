<?php
$config = config('AppConfig');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title><?php echo $pageTitle ?? ""; ?> | <?php echo $config->appName ?: "My Web App"; ?></title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />

    <!-- add favicon -->
    <link rel="shortcut icon" href="<?php echo getFaviconUrl(); ?>" type="image/x-icon">
    <link rel="icon" href="<?php echo getFaviconUrl(); ?>" type="image/x-icon">


    <!-- add manifest.json -->
    <link rel="manifest" href="<?php echo base_url("manifest.json"); ?>">

    <!-- ================== BEGIN core-css ================== -->
    <link href="<?php echo base_url("themeAssets"); ?>/2/dist/css/tabler.min.css?1692870487" rel="stylesheet" />
    <!-- <link href="<?php echo base_url("themeAssets"); ?>/2/dist/css/tabler-flags.min.css?1692870487" rel="stylesheet" />
    <link href="<?php echo base_url("themeAssets"); ?>/2/dist/css/tabler-payments.min.css?1692870487" rel="stylesheet" />
    <link href="<?php echo base_url("themeAssets"); ?>/2/dist/css/tabler-vendors.min.css?1692870487" rel="stylesheet" />
    <link href="<?php echo base_url("themeAssets"); ?>/2/dist/css/demo.min.css?1692870487" rel="stylesheet" /> -->
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


    <!-- ================== END core-css ================== -->

    <?php echo view('templates/headerAssets'); ?>
    <script>
        var WPN = <?php echo (int)$config->webPushNotification; ?>;
    </script>
</head>