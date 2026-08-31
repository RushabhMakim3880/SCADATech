<?php
$config = config('AppConfig');
?>

<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => $pageTitle]) ?>


<div class="row">
    <div class="col-md-12">
        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'SES Example']]) ?>

        <h1>Live Updates</h1>
        <div id="swMessages"></div>


        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>
</div>


<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>