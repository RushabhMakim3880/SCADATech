<?php
$config = config('AppConfig');
?>

<!-- <a class='btn btn-primary float-end m-3' href="<?= base_url('samples/addSampleNew') ?>"><i class="fa fa-plus-circle"></i> Add New</a> -->

<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => $pageTitle]) ?>
<!-- <div class="clearfix"></div> -->


<div class="row">
    <div class="col-md-12">
        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Manage  Samples']]) ?>

        <div class="d-flex gap-2">
            <select class="form-select w-auto reloadDataTable dataTableCustomFilter" data-target="newSample" name="leadFilter" aria-label="Lead Filter">
                <option value="freshLead">Fresh Lead</option>
                <option value="todayLead">Today Lead</option>
                <option value="missedLead">Missed Lead</option>
                <option value="next7days">Next 7 Days Followup</option>
            </select>
            <select class="form-select ms-auto w-auto reloadDataTable dataTableCustomFilter" data-target="newSample" name="statusType" aria-label="Lead Filter">
                <option value="all">All</option>
                <option value="open">Open</option>
                <option value="won">Won</option>
                <option value="loss">Lost</option>
            </select>
        </div>

        <div class="manageDataTable table-responsive mt-3"
            data-module="newSample"
            data-configendpoint="api/newSample/getDataTableColumns"
            data-endpoint="api/newSample/getDataTableData"
            data-features='{"columnControls": true,"export": true}'>
        </div>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

</div>

<!-- ad new link -->
<a href="<?= base_url('samples/addSampleNew') ?>" class="btn btn-primary" data-title="Add New Sample">Add New</a>

<button type="button" class="btn btn-primary apiPrintPreview" data-endpoint="api/newSample/generatePdf/0">Invoice Preview</button>
<button type="button" class="btn btn-warning apiFileDownload" data-endpoint="api/newSample/generatePdf/1">Invoice PDF Download</button>

<button type="button" class="btn btn-info apiPopup" data-title="This is Modal Title" data-size="lg" data-endpoint="api/newSample/infoPopupExample/0">Info Popup Example</button>
<button type="button" class="btn btn-warning apiPopup" data-title="This is Modal Title With Form" data-size="lg" data-endpoint="api/newSample/infoFormExample/0">Info Form Example</button>

<button type="button" class="btn btn-warning apiPopup" data-title="This is Modal Title With Form" data-size="lg" data-endpoint="samples/addSampleNewAjax">Add Form</button>
<!-- <button type="button" class="btn btn-primary previewInvoice">Invoice Preview</button>
<button type="button" class="btn btn-warning downloadInvoice">Invoice PDF Download</button> -->

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>