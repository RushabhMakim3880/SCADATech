<?php
$config = config('AppConfig');
?>

<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => '']) ?>

<div class="row">
    <div class="col-md-12">

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => 'Manage Users']]) ?>

        <!-- <div class="btn-group float-end" id="manageUsers_columnSettings">
            <a href="#" class="btn btn-default dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-cog"></i></a>
            <ul class="dropdown-menu dropdown-menu-end"></ul>
        </div> -->

        <!-- <label> <input type="radio" class="dataTableCustomFilter reloadDataTable" name="test" id="test" value="1" data-target="manageUsers"> Test 1</label>
        <label> <input type="radio" class="dataTableCustomFilter reloadDataTable" name="test" id="test" value="2" data-target="manageUsers"> Test 2</label>
        <label> <input type="radio" class="dataTableCustomFilter reloadDataTable" name="test" id="test" value="3" data-target="manageUsers"> Test 3</label> -->



        <!-- checkbox test -->
        <!-- <div class="form-check">
            <input class="form-check-input dataTableCustomFilter reloadDataTable" type="checkbox" value="101" name="checkBox" id="flexCheckDefault" data-target="manageUsers">
            <label class="form-check-label" for="flexCheckDefault">
                Default checkbox
            </label>
        </div> -->

        <!-- select test -->
        <!-- <select class="form-select dataTableCustomFilter reloadDataTable" aria-label="Default select example" data-target="manageUsers" name="select">
            <option selected>Open this select menu</option>
            <option value="1">One</option>
            <option value="2">Two</option>
            <option value="3">Three</option>
        </select> -->

        <!-- input test -->
        <!-- <input type="text" name="inputText" class="form-control dataTableCustomFilter reloadDataTable" data-target="manageUsers" placeholder="Text input">

        <button class="btn btn-primary reloadDataTable" data-target="manageUsers">Reload</button> -->



        <div class="manageDataTable"
            data-module="manageUsers"
            data-addendpoint="users/addUser"
            data-addtype="normal"
            data-configendpoint="api/users/getDataTableColumns"
            data-endpoint="api/users/getDataTableData"
            data-features='{"columnControls": false,"export": false}'>
        </div>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>

</div>
<a class='btn btn-success' title="Add New " href="<?php echo base_url("users/addUser"); ?>"><i class="fa fa-plus-circle"></i>&nbsp;&nbsp;Add</a>


<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>