<?php
$config = config("AppConfig");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo @$title; ?></title>
</head>

<body style="font-family: Arial, sans-serif; margin: 0; padding: 0;">
    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table width="600" cellspacing="0" cellpadding="0" style="border: 1px solid #cccccc; border-collapse: collapse;">
                    <!-- Logo -->
                    <tr>
                        <td align="center" bgcolor="#44525f" style="padding: 0px;">
                            <img src="<?= getLogoUrl(); ?>" alt="Logo" height="" style="display: block;height: 70px;padding: 20px 0px 0px;">
                            <h4 style="color: #ccc;font-weight: 100;margin: 5px 0 20px;"><?= $config->appTagline; ?></h4>
                        </td>
                    </tr>

                    <!-- Email Content -->
                    <tr>
                        <td bgcolor="#ffffff" style="padding: 20px; text-align: left;">
                            <h1 style="font-size: 24px; margin-top: 0;"><?php echo @$subTitle; ?></h1>
                            <p style="margin: 18px 0; font-size: 16px; line-height: 1.6;"><?php echo @$body; ?></p>
                            <!-- Add more content here -->
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td bgcolor="#44525f" style="padding: 20px; text-align: center;">
                            <p style="margin: 0; color: #ffffff; font-size: 14px;">&copy; <?php echo date("Y"); ?> <?= $config->ownerCompanyName; ?>. All rights reserved.</p>
                            <p style="margin: 5px; color: #ffffff; font-size: 10px;">Visit Website <a style="color:white;" href='<?= $config->websiteUrl; ?>'><?= $config->websiteText; ?></a></p>
                            <p style="margin: 5px; color: #aaa; font-size: 8px;">Email Generated From: <?php echo base_url(); ?></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>