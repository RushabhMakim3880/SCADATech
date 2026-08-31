<?php
$config = config('AppConfig');
use App\Libraries\UserPermissionLib;
?>

<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => '']) ?>

<div class="row">
    <div class="col-md-12">
        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'open', 'params' => ['title' => $pageTitle]]) ?>

        <form method="POST" action="api/setting/saveCompanyMasterSetting" autocomplete="off"
            class="autoCrudForm"
            data-resource="api/setting/getCompanyMasterSetting"
            data-record-id="1">

            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th class="text-start" style="width: 40%;">PARAMETER</th>
                            <th style="width: 30%;">LOW MIN</th>
                            <th style="width: 30%;">MAX</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th class="fw-bold align-middle text-start">SIDE A Width</th>
                            <td>
                                <input type="text" class="form-control text-center numberInput virtualNumKeypad" name="sideAWidthMin" id="sideAWidthMin" placeholder="Enter Low Min">
                            </td>
                            <td>
                                <input type="text" class="form-control text-center numberInput virtualNumKeypad" name="sideAWidthMax" id="sideAWidthMax" placeholder="Enter Max">
                            </td>
                        </tr>
                        <tr>
                            <th class="fw-bold align-middle text-start">SIDE B Width</th>
                            <td>
                                <input type="text" class="form-control text-center numberInput virtualNumKeypad" name="sideBWidthMin" id="sideBWidthMin" placeholder="Enter Low Min">
                            </td>
                            <td>
                                <input type="text" class="form-control text-center numberInput virtualNumKeypad" name="sideBWidthMax" id="sideBWidthMax" placeholder="Enter Max">
                            </td>
                        </tr>
                        <tr>
                            <th class="fw-bold align-middle text-start">SIDE A Thickness</th>
                            <td>
                                <input type="text" class="form-control text-center numberInput virtualNumKeypad" name="sideAThicknessMin" id="sideAThicknessMin" placeholder="Enter Low Min">
                            </td>
                            <td>
                                <input type="text" class="form-control text-center numberInput virtualNumKeypad" name="sideAThicknessMax" id="sideAThicknessMax" placeholder="Enter Max">
                            </td>
                        </tr>
                        <tr>
                            <th class="fw-bold align-middle text-start">DA MINIMUM BACKMARK</th>
                            <td colspan="2">
                                <input type="text" class="form-control text-center numberInput virtualNumKeypad" name="daMinimumBackmark" id="daMinimumBackmark" placeholder="Enter DA Minimum Backmark (Default: 20)">
                            </td>
                        </tr>
                        <tr>
                            <th class="fw-bold align-middle text-start">DB MINIMUM BACKMARK</th>
                            <td colspan="2">
                                <input type="text" class="form-control text-center numberInput virtualNumKeypad" name="dbMinimumBackmark" id="dbMinimumBackmark" placeholder="Enter DB Minimum Backmark (Default: 20)">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa fa-save me-1"></i> Save Settings
                </button>
            </div>
        </form>

        <?php echo view('templates/' . $config->theme . '/components/card', ['mode' => 'close']) ?>
    </div>
</div>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>
