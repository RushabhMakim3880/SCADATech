<?php
$config = config('AppConfig');
?>
<style>
    .permission-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        /* Flexible grid */
        gap: 10px;
        /* Adjust spacing */
    }

    .permission-card {
        break-inside: avoid;
        width: 100%;
    }

    .card {
        display: flex;
        flex-direction: column;
    }
</style>

<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => $pageTitle]) ?>

<div class="d-inline-block" style="width:200px;">
    <form class="autoCrudForm" data-record-id='0' data-dropdowns='[
    {"name": "groupId", "endpoint": "/api/users/getGroups"}
    ]'>
        <select id="groupId" class="select2" name="tenantId" data-dropdown="groupId">
            <option value="">Select Group</option>
        </select>
    </form>
</div>

<!-- save button -->
<button class="btn d-inline-block btn-primary " id="savePermissions">Save</button>

<div class="row mt-3" id="permissionsContainer">


</div>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>