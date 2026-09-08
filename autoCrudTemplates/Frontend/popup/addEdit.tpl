<?php
$config = config('AppConfig');
?>

<form method="POST" action="#" autocomplete="off"
    class="autoCrudForm"
    data-resource="api/{{MODULE_NAME}}/get/<?= isset(${{PRIMARY_FIELD}}) ? ${{PRIMARY_FIELD}} : '' ?>"
    data-record-id="<?= isset(${{PRIMARY_FIELD}}) ? ${{PRIMARY_FIELD}} : '' ?>"
    data-dropdowns='{{FOREIGN_DROPDOWNS}}'>

    <div class="row">
        {{FORM_FIELDS}}
    </div>

</form>
