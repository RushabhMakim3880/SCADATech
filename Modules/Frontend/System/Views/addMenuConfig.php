<?php
$config = config('AppConfig');
?>
<style>
    .nested-sortable {
        list-style: none;
        padding-left: 0;
    }

    .nested-sortable .list-group-item {
        cursor: grab;
        background: #f8f9fa;
        margin-bottom: 5px;
        border-radius: 5px;
    }

    .nested-sortable .sub-menu {
        padding-left: 20px;
        list-style: none;
    }

    .menu-content {
        display: none;
        background: #e9ecef;
        padding: 10px;
        border-radius: 5px;
    }

    .menu-actions {
        /* display: flex; */
        justify-content: space-between;
    }
</style>

<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => $pageTitle]) ?>

<div class="float-end">
    <form class="autoCrudForm d-flex gap-2 align-items-center" data-resource="api/Tenant/getAll" data-record-id="0" data-dropdowns='[
        {"name": "tenantId", "endpoint": "/api/tenantMaster/tenantDropdown"}
    ]'>
        <div>
            <select id="tenantIdSelector" class="form-select select2" name="tenantId" data-dropdown="tenantId"></select>
        </div>
        <div>
            <select id="menuLocationSelector" class="form-select select2" name="menuLocation" required>
                <option value="sidebarMain">Main Sidebar</option>
                <option value="mobileBottom">Mobile Bottom</option>
            </select>
        </div>
    </form>
</div>

<div class="row">

    <div class="col-xl-4 col-lg-6 col-md-8 col-sm-12">

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Menu Configuration']]) ?>


        <ul id="menuList" class="list-group nested-sortable">
            <!-- Menu items will be added dynamically -->
        </ul>


        <button class="btn btn-primary mt-3 float-end" id="autoAddRoutes"><i class="fa fa-redo"></i> Add Missing Routes</button>
        <button class="btn btn-primary mt-3" id="addMenuItem"><i class="fa fa-plus-circle"></i> Add New Item</button>
        <div class="clearfix"></div>
        <button class="btn btn-success mt-3" id="saveMenu"><i class="fa fa-save"></i> Save Menu</button>


        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

</div>

<button class="btn btn-info mt-3" id="jsonExportMenu"><i class="fa fa-download"></i> Download</button>
<button class="btn btn-warning mt-3 restoreDefaultMenu"><i class="fa fa-undo"></i> Restore Default</button>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>