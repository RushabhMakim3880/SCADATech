<?php
$config = config('AppConfig');
?>
<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => 'Dashboard Designer']); ?>

<style type="text/css">
    .grid-stack {
        background: #FAFAD2;
    }

    .grid-stack-item-content {
        background-color: #fff;
    }

    .widget-controls {
        display: none;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        /* Additional styling: background, padding, etc. */
        z-index: 10;
        /* ensures overlay appears above content */
    }

    .grid-stack-item-content:hover .widget-controls {
        display: flex;
        gap: 10px;
        /* space between icons */
        justify-content: center;
        align-items: center;
    }

    .widget-controls a {
        cursor: pointer;
        font-size: 1.5em;
        /* Optional: background, color, padding, border-radius for icons */
    }
</style>

<div class="float-end">
    <button class="btn btn-success float-end ms-1" id="saveDashboard"><i class="fa fa-save"></i></button>
    <button class="btn btn-primary float-end ms-1" id="add-widget" data-bs-toggle="modal" data-bs-target="#widgetModal"><i class="fa fa-plus-circle"></i></button>
    <select class="float-end h3 switchDashboard">
        <option value="1">Dashboard 1</option>
        <option value="2">Dashboard 2</option>
        <option value="3">Dashboard 3</option>
    </select>
</div>

<div class="form-group">
    <label for="dashBoardName">Dashboard Name</label><br>
    <input id="dashBoardName" value="" type="text" class="form-control-sm" />
</div>

<div class="grid-stack mt-3">

</div>


<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>


<!-- Modal -->
<div class="modal fade" id="widgetModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Widget</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"></span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="widgetId" value="0">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="widgetName">Widget Name</label>
                            <input type="text" class="form-control" id="widgetName" placeholder="Enter widget name">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="widgetName">Copy From</label>
                            <select class="form-control" id="copyFrom" placeholder="">
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label for="htmlTemplate">HTML Template</label>
                            <textarea type="text" class="form-control" rows="8" id="htmlTemplate" placeholder="Enter HTML Template"><b>asdf</b></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label for="dataSource">Data Source JSON</label>
                            <textarea type="text" class="form-control" rows="8" id="dataSource" placeholder="Enter Data Source JSON Object"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-success addWidgetBtn text-white"><i class="fa fa-plus-circle"></i> Add</button>
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>