<?php
$config = config('AppConfig');
?>

<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => '']) ?>

<div class="row">
    <div class="col-md-12">

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => $pageTitle]]) ?>

        <form method="POST" action="#" autocomplete="off"
            class="autoCrudForm"
            data-resource="api/{{MODULE_NAME}}/get/<?= isset(${{PRIMARY_FIELD}}) ? ${{PRIMARY_FIELD}} : '' ?>"
            data-record-id="<?= isset(${{PRIMARY_FIELD}}) ? ${{PRIMARY_FIELD}} : '' ?>"
            data-dropdowns='{{FOREIGN_DROPDOWNS}}'>

            <div class="row">
                {{FORM_FIELDS}}
            </div>

            <!-- submit button -->
            <button type="submit" class="btn btn-primary mt-3">Submit</button>

        </form>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>
</div>

<a class="btn btn-info" href="<?= base_url("{{MODULE_NAME}}/manage{{ITEM_NAME}}"); ?>"><i class="fa fa-list"></i> View All</a>


<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>