<?php
$config = config('AppConfig');
?>

<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => $pageTitle]) ?>

<div class="row">
    <div class="col-md-6">

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Setup Instructions']]) ?>
        <p>Scan the following QR code with your Google Authenticator app:</p>
        <img src="<?= $qrCodeDataUri ?>" alt="2FA QR Code">
        <p>If you cannot scan the QR code, manually enter this secret into your app:</p>
        <p><strong><?= esc($secret) ?></strong></p>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

</div>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>