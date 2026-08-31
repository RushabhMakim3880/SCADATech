<?php

use App\Libraries\AssetManager;
use App\Libraries\SettingManager;
use App\Libraries\Tenant;

AssetManager::loadCssAssets();

$tenantId = Tenant::id();

$config = SettingManager::getAllResolvedSettings($tenantId);

if ($config['customCss']) {
    echo "<style>";
    echo $config['customCss'];
    echo "</style>";
}

?>

<?php
$fontFamily = 'Open_Sans';
$fontType = "variable"; // or "static" based on your requirement
?>

<style>
    <?php if ($fontType === "variable") { ?>@font-face {
        font-family: '<?= $fontFamily; ?>';
        src: url('<?= base_url(); ?>assets/fonts/<?= $fontFamily; ?>/Full.ttf') format('truetype');
        font-weight: 100 900;
        font-style: normal;
    }

    @font-face {
        font-family: '<?= $fontFamily; ?>';
        src: url('<?= base_url(); ?>assets/fonts/<?= $fontFamily; ?>/FullItalic.ttf') format('truetype');
        font-weight: 100 900;
        font-style: italic;
    }

    <?php } else { ?>

    /* Light (300) */
    @font-face {
        font-family: '<?= $fontFamily; ?>';
        src: url('<?= base_url(); ?>assets/fonts/<?= $fontFamily; ?>/Light.ttf') format('truetype');
        font-weight: 300;
        font-style: normal;
    }

    @font-face {
        font-family: '<?= $fontFamily; ?>';
        src: url('<?= base_url(); ?>assets/fonts/<?= $fontFamily; ?>/LightItalic.ttf') format('truetype');
        font-weight: 300;
        font-style: italic;
    }

    /* Regular (400) */
    @font-face {
        font-family: '<?= $fontFamily; ?>';
        src: url('<?= base_url(); ?>assets/fonts/<?= $fontFamily; ?>/Regular.ttf') format('truetype');
        font-weight: 400;
        font-style: normal;
    }

    @font-face {
        font-family: '<?= $fontFamily; ?>';
        src: url('<?= base_url(); ?>assets/fonts/<?= $fontFamily; ?>/Italic.ttf') format('truetype');
        font-weight: 400;
        font-style: italic;
    }

    /* Medium (500) */
    @font-face {
        font-family: '<?= $fontFamily; ?>';
        src: url('<?= base_url(); ?>assets/fonts/<?= $fontFamily; ?>/Medium.ttf') format('truetype');
        font-weight: 500;
        font-style: normal;
    }

    @font-face {
        font-family: '<?= $fontFamily; ?>';
        src: url('<?= base_url(); ?>assets/fonts/<?= $fontFamily; ?>/MediumItalic.ttf') format('truetype');
        font-weight: 500;
        font-style: italic;
    }

    /* SemiBold (600) */
    @font-face {
        font-family: '<?= $fontFamily; ?>';
        src: url('<?= base_url(); ?>assets/fonts/<?= $fontFamily; ?>/SemiBold.ttf') format('truetype');
        font-weight: 600;
        font-style: normal;
    }

    @font-face {
        font-family: '<?= $fontFamily; ?>';
        src: url('<?= base_url(); ?>assets/fonts/<?= $fontFamily; ?>/SemiBoldItalic.ttf') format('truetype');
        font-weight: 600;
        font-style: italic;
    }

    /* Bold (700) */
    @font-face {
        font-family: '<?= $fontFamily; ?>';
        src: url('<?= base_url(); ?>assets/fonts/<?= $fontFamily; ?>/Bold.ttf') format('truetype');
        font-weight: 700;
        font-style: normal;
    }

    @font-face {
        font-family: '<?= $fontFamily; ?>';
        src: url('<?= base_url(); ?>assets/fonts/<?= $fontFamily; ?>/BoldItalic.ttf') format('truetype');
        font-weight: 700;
        font-style: italic;
    }

    <?php } ?>html,
    body {
        font-family: '<?= $fontFamily; ?>', sans-serif;
    }
</style>



<script>
    var base_url = "<?php echo base_url(); ?>";

    <?php
    $uri = service('uri');
    $currentUrl = $uri->getSegment(1);
    $config = SettingManager::getJsSideSettings($tenantId);
    if ($config['webPushNotification'] and !in_array($currentUrl, ['auth'])) {
        echo 'var webPushPublicKey = "' . getenv('webPushPublicKey') . '";';
    }
    ?>
    window.appSettings = <?= json_encode($config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
    window.isStrictProgramAlignEnabled = <?= getenv('isStrictProgramAlignEnabled') === 'false' ? 'false' : 'true'; ?>;
</script>