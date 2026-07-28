<h1 class="page-header text-center screenTitle"><i class="fa fa-user-gear"></i> Manual Control</h1>

<div class="plcViewBox p-3 rounded-3 shadow">
    <button class="btn mb-3 viewCloseBtn">
        <i class="fa fa-times-circle"></i>
    </button>

    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-auto">
                    <table class="table table-sm align-middle text-center">
                        <thead>
                            <tr>
                                <th colspan="3" class="text-center">MOTOR OPERATIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3">
                                    <button class="plc-btn btn btn-sm" data-ui-type="button" data-tag-id="324"
                                        data-behavior="momentary" data-indicator-id="220" data-on-color="#82c779"
                                        data-off-color="#0a4c7a" data-disable-color="#6c757d"
                                        data-on-label="MAIN HYD<br>MOTOR" data-off-label="MAIN HYD<br>MOTOR"
                                        data-on-confirm="" data-off-confirm=""></button>

                                    <button class="plc-btn btn btn-sm" data-ui-type="button" data-tag-id="322"
                                        data-behavior="momentary" data-indicator-id="218" data-on-color="#82c779"
                                        data-off-color="#0a4c7a" data-disable-color="#6c757d"
                                        data-on-label="HEAD LUB<br>MOTOR" data-off-label="HEAD LUB<br>MOTOR"
                                        data-on-confirm="" data-off-confirm=""></button>

                                    <button class="plc-btn btn btn-sm" data-ui-type="button" data-tag-id="325"
                                        data-behavior="maintain" data-indicator-id="221" data-on-color="#82c779"
                                        data-off-color="#0a4c7a" data-disable-color="#6c757d"
                                        data-on-label="OIL CIRC.<br>MOTOR" data-off-label="OIL CIRC.<br>MOTOR"
                                        data-on-confirm="" data-off-confirm=""></button><!-- <button class="plc-btn btn btn-sm" data-ui-type="button" data-tag-id="380"
                                        data-behavior="momentary" data-indicator-id="381" data-on-color="#82c779"
                                        data-off-color="#0a4c7a" data-disable-color="#6c757d"
                                        data-on-label="PRINCHER HYD MOTOR" data-off-label="PRINCHER HYD MOTOR"
                                        data-on-confirm="" data-off-confirm=""></button> -->
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <button class="plc-btn btn btn-sm" data-ui-type="button" data-tag-id="328"
                                        data-behavior="momentary" data-indicator-id="223" data-on-color="#82c779"
                                        data-off-color="#0a4c7a" data-disable-color="#6c757d"
                                        data-on-label="PRINCHER LUB<br>MOTOR" data-off-label="PRINCHER LUB<br>MOTOR"
                                        data-on-confirm="" data-off-confirm=""></button>
                                </td>
                                <td>MANUAL SPEED</td>
                                <td><input class="plc-input virtualNumKeypad" data-tag-id="417"
                                        data-disable-color="#6c757d" data-disable-condition="" /></td>
                                <!-- <td>
                                    <button class="plc-btn btn btn-sm" data-ui-type="button" data-tag-id="235"
                                        data-behavior="momentary" data-indicator-id="20" data-on-color="#82c779"
                                        data-off-color="#0a4c7a" data-disable-color="#6c757d"
                                        data-on-label="AIR COOLER<br>MOTOR" data-off-label="AIR COOLER<br>MOTOR"
                                        data-on-confirm="" data-off-confirm=""></button>
                                </td>
                                <td>
                                    <button class="plc-btn btn btn-sm" data-ui-type="button" data-tag-id="234"
                                        data-behavior="momentary" data-indicator-id="18" data-on-color="#82c779"
                                        data-off-color="#0a4c7a" data-disable-color="#6c757d"
                                        data-on-label="CHAIN<br>FEEDER REV" data-off-label="CHAIN<br>FEEDER REV"
                                        data-on-confirm="" data-off-confirm=""></button>
                                </td> -->
                            </tr>
                            <tr>
                                <td>PRINCHER GO MM</td>
                                <td><input class="plc-input virtualNumKeypad" data-tag-id="344"
                                        data-disable-color="#6c757d" data-disable-condition="" /></td>
                                <td>
                                    <button class="plc-btn btn btn-sm" data-ui-type="button" data-tag-id="327"
                                        data-behavior="momentary" data-indicator-id="327" data-on-color="#82c779"
                                        data-off-color="#0a4c7a" data-disable-color="#6c757d"
                                        data-on-label="PRINCHER GO" data-off-label="PRINCHER GO" data-on-confirm=""
                                        data-off-confirm=""></button>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col">
                    <div class="h-100 d-flex flex-column border rounded p-2">
                        <div id="toolbar" class="my-1">
                            <button class="btn btn-secondary" id="btnReset"><i class="fa fa-refresh"></i></button>
                            <button class="btn btn-secondary" id="btnLeft"><i class="fa fa-arrow-left"></i></button>
                            <button class="btn btn-secondary" id="btnRight"><i class="fa fa-arrow-right"></i></button>
                            <button class="btn btn-secondary" id="btnCenter"><i class="fa fa-align-center"></i></button>
                            <button class="btn btn-secondary" id="btnFit"><i class="fa fa-text-width"></i></button>
                            <button class="btn btn-secondary" id="btnExpand"><i class="fa fa-expand"></i></button>
                            <button class="btn btn-secondary" id="btnFlip"><i class="fa fa-random"></i></button>
                            <button class="btn btn-danger" id="btnShowHideCanvas"><i
                                    class="fa fa-eye-slash"></i></button>
                        </div>

                        <div class="flex-grow-1"
                            style="background-color: #333; color: #fff; padding: 10px; border-radius: 5px; max-height:420px; min-height:220px; width:100%; overflow-y:auto;">
                            <h3>Active Alarms</h3>
                            <ul class="list-unstyled" id="notificationList" style="font-size: small;">
                                <!-- Notifications will be dynamically added here -->
                            </ul>
                        </div>

                    </div>
                </div>

                <div class="row">

                    <div class="col-auto" style="clear:both;">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th class="text-center" colspan="4">POSITION AND SPEED</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th></th>
                                    <th>MACHINE</th>
                                    <th>WORK</th>
                                    <th>SPEED</th>
                                </tr>
                                <tr>
                                    <th>PRINCHER</th>
                                    <td>
                                        <span title="503:SD_X_ACT_POS">
                                            <input class="plc-input virtualNumKeypad" data-tag-id="384"
                                                data-disable-color="#6c757d" data-disable-condition="" disabled />
                                        </span>
                                    </td>
                                    <td>
                                        <span title="505:SD_X_WORK_POS">
                                            <input class="plc-input virtualNumKeypad" data-tag-id="483"
                                                data-disable-color="#6c757d" data-disable-condition="" disabled />
                                        </span>
                                    </td>
                                    <td>
                                        <span title="504:SD_X_ACT_VELOCITY">
                                            <input class="plc-input virtualNumKeypad" data-tag-id="372"
                                                data-disable-color="#6c757d" data-disable-condition="" disabled />
                                        </span>
                                    </td>

                                </tr>
                                <tr>
                                    <th>Y1</th>
                                    <td>
                                        <span title="506:SD_Y1_ACT_POS">
                                            <input class="plc-input virtualNumKeypad" data-tag-id="385"
                                                data-disable-color="#6c757d" data-disable-condition="" disabled />
                                        </span>
                                    </td>
                                    <td>
                                        <span title="508:SD_Y1_WORK_POS">
                                            <input class="plc-input virtualNumKeypad" data-tag-id="484"
                                                data-disable-color="#6c757d" data-disable-condition="" disabled />
                                        </span>
                                    </td>
                                    <td>
                                        <span title="507:SD_Y1_ACT_velocity">
                                            <input class="plc-input virtualNumKeypad" data-tag-id="373"
                                                data-disable-color="#6c757d" data-disable-condition="" disabled />
                                        </span>
                                    </td>

                                </tr>
                                <tr>
                                    <th>Y2</th>
                                    <td>
                                        <span title="509:SD_Y2_ACT_POS">
                                            <input class="plc-input virtualNumKeypad" data-tag-id="386"
                                                data-disable-color="#6c757d" data-disable-condition="" disabled />
                                        </span>
                                    </td>
                                    <td>
                                        <span title="511:SD_Y2_WORK_POS">
                                            <input class="plc-input virtualNumKeypad" data-tag-id="485"
                                                data-disable-color="#6c757d" data-disable-condition="" disabled />
                                        </span>
                                    </td>
                                    <td>
                                        <span title="510:SD_Y2_ACT_VELOCITY">
                                            <input class="plc-input virtualNumKeypad" data-tag-id="374"
                                                data-disable-color="#6c757d" data-disable-condition="" disabled />
                                        </span>
                                    </td>

                                </tr>
                                <tr>
                                    <th>Y3</th>
                                    <td>
                                        <span title="512:SD_Z1_ACT_POS">
                                            <input class="plc-input virtualNumKeypad" data-tag-id="387"
                                                data-disable-color="#6c757d" data-disable-condition="" disabled />
                                        </span>
                                    </td>
                                    <td>
                                        <span title="514:SD_Z1_WORK_POS">
                                            <input class="plc-input virtualNumKeypad" data-tag-id="486"
                                                data-disable-color="#6c757d" data-disable-condition="" disabled />
                                        </span>
                                    </td>
                                    <td>
                                        <span title="513:SD_Z1_ACT_velocity">
                                            <input class="plc-input virtualNumKeypad" data-tag-id="375"
                                                data-disable-color="#6c757d" data-disable-condition="" disabled />
                                        </span>
                                    </td>

                                </tr>
                                <tr>
                                    <th>Y4</th>
                                    <td>
                                        <span title="515:SD_Z2_ACT_POS">
                                            <input class="plc-input virtualNumKeypad" data-tag-id="388"
                                                data-disable-color="#6c757d" data-disable-condition="" disabled />
                                        </span>
                                    </td>
                                    <td>
                                        <span title="517:SD_Z2_WORK_POS">
                                            <input class="plc-input virtualNumKeypad" data-tag-id="487"
                                                data-disable-color="#6c757d" data-disable-condition="" disabled />
                                        </span>
                                    </td>
                                    <td>
                                        <span title="516:SD_Z2_ACT_VELOCITY">
                                            <input class="plc-input virtualNumKeypad" data-tag-id="376"
                                                data-disable-color="#6c757d" data-disable-condition="" disabled />
                                        </span>
                                    </td>

                                </tr>
                                <tr>
                                    <th>Y5</th>
                                    <td>
                                        <span title="515:SD_Z2_ACT_POS">
                                            <input class="plc-input virtualNumKeypad" data-tag-id="389"
                                                data-disable-color="#6c757d" data-disable-condition="" disabled />
                                        </span>
                                    </td>
                                    <td>
                                        <span title="517:SD_Z2_WORK_POS">
                                            <input class="plc-input virtualNumKeypad" data-tag-id="488"
                                                data-disable-color="#6c757d" data-disable-condition="" disabled />
                                        </span>
                                    </td>
                                    <td>
                                        <span title="516:SD_Z2_ACT_VELOCITY">
                                            <input class="plc-input virtualNumKeypad" data-tag-id="377"
                                                data-disable-color="#6c757d" data-disable-condition="" disabled />
                                        </span>
                                    </td>

                                </tr>
                                <tr>
                                    <th>Y6</th>
                                    <td>
                                        <span title="515:SD_Z2_ACT_POS">
                                            <input class="plc-input virtualNumKeypad" data-tag-id="390"
                                                data-disable-color="#6c757d" data-disable-condition="" disabled />
                                        </span>
                                    </td>
                                    <td>
                                        <span title="517:SD_Z2_WORK_POS">
                                            <input class="plc-input virtualNumKeypad" data-tag-id="489"
                                                data-disable-color="#6c757d" data-disable-condition="" disabled />
                                        </span>
                                    </td>
                                    <td>
                                        <span title="516:SD_Z2_ACT_VELOCITY">
                                            <input class="plc-input virtualNumKeypad" data-tag-id="378"
                                                data-disable-color="#6c757d" data-disable-condition="" disabled />
                                        </span>
                                    </td>

                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="col-auto">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th colspan="2" class="text-center">MANUAL OPERATION SELECTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="plc-listbox list-group" style="display:grid;
                   grid-auto-flow:column;
                   grid-template-rows:repeat(8,auto);
                   gap:5px;
                   font-size:21px; /* 👈 Font size added here */" data-ui-type="listbox" data-tag-id="310"
                                    data-disable-color="#ccc" data-disable-condition="" data-confirm="" <?php
                                    if (getenv('isCuttingOperationEnabled') === 'true') {
                                        ?>
                                        data-list='[1:PRINCHER FWD/REV,2:HEAD1 UP/DOWN,3:HEAD2 UP/DOWN,4:HEAD3 UP/DOWN,5:HEAD4 UP/DOWN,6:HEAD5 UP/DOWN,7:HEAD6 UP/DOWN,8:HOLD1 UP/DOWN,9:PUNCH1 UP/DOWN,10:HOLD2 UP/DOWN,11:PUNCH2 UP/DOWN,12:HOLD3 UP/DOWN,13:PUNCH3 UP/DOWN,14:HOLD4 UP/DOWN,15:PUNCH4 UP/DOWN,16:HOLD5 UP/DOWN,17:PUNCH5 UP/DOWN,18:HOLD6 UP/DOWN,19:PUNCH6 UP/DOWN,20:MARKING BODY UP/DOWN,21:MARKING CYL UP/DOWN,22:MARKING CASSATE UP/DOWN,23:PRINCHER UP/DOWN,24:PRINCHER CLAMP/DECLAMP,25:CUT HOLD UP/DOWN,26:CUTTING CYL UP/DOWN,27:INFEED UP/DOWN,28:OUTFEED UP/DOWN,29:CHAIN FEEDER FWD/REV]'>
                                        <?php
                                    } else {
                                        ?>
                                        data-list='[1:PRINCHER FWD/REV,2:HEAD1 UP/DOWN,3:HEAD2 UP/DOWN,4:HEAD3
                                        UP/DOWN,5:HEAD4 UP/DOWN,6:HEAD5 UP/DOWN,7:HEAD6 UP/DOWN,8:HOLD1 UP/DOWN,9:PUNCH1
                                        UP/DOWN,10:HOLD2 UP/DOWN,11:PUNCH2 UP/DOWN,12:HOLD3 UP/DOWN,13:PUNCH3
                                        UP/DOWN,14:HOLD4 UP/DOWN,15:PUNCH4 UP/DOWN,16:HOLD5 UP/DOWN,17:PUNCH5
                                        UP/DOWN,18:HOLD6 UP/DOWN,19:PUNCH6 UP/DOWN,20:MARKING BODY UP/DOWN,21:MARKING CYL
                                        UP/DOWN,22:MARKING CASSATE UP/DOWN,23:PRINCHER UP/DOWN,24:PRINCHER
                                        CLAMP/DECLAMP,25:CUT HOLD UP/DOWN,26:CUTTING CYL UP/DOWN,27:INFEED
                                        UP/DOWN,28:OUTFEED UP/DOWN,29:CHAIN FEEDER FWD/REV'>
                                        <?php
                                    }
                                    ?>


                                </div>
                            </td>
                            <td class="align-middle px-3">
                                <div class="d-flex flex-column gap-3 justify-content-center h-100">
                                    <button class="plc-btn btn btn-md w-100" data-ui-type="button" data-tag-id="321"
                                        data-behavior="momentary" data-indicator-id="321" data-on-color="#82c779"
                                        data-off-color="#0a4c7a" data-disable-color="#6c757d"
                                        data-on-label="UP/FORWARD<br>PB" data-off-label="UP/FORWARD<br>PB"
                                        data-on-confirm="" data-off-confirm=""></button>

                                    <button class="plc-btn btn btn-md w-100" data-ui-type="button" data-tag-id="330"
                                        data-behavior="momentary" data-indicator-id="330" data-on-color="#82c779"
                                        data-off-color="#0a4c7a" data-disable-color="#6c757d"
                                        data-on-label="DOWN/REV<br>PB" data-off-label="DOWN/REV<br>PB"
                                        data-on-confirm="" data-off-confirm=""></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-auto">
                <div class="plc-gauge float-start" data-ui-type="gauge" data-tag-id="367" data-min="0" data-max="250"
                    data-label="Accumulator" data-ranges='' style="width: 195px; height: 210px;">
                </div>
                <div class="plc-gauge float-start" data-ui-type="gauge" data-tag-id="369" data-min="0" data-max="400"
                    data-label="Marking" data-ranges='' style="width: 195px; height: 210px;">
                </div>
                <div class="plc-gauge float-start" data-ui-type="Princher" data-tag-id="370" data-min="0" data-max="250"
                    data-label="Princher" data-ranges='' style="width: 195px; height: 210px;">
                </div>
                <div class="plc-gauge float-start" data-ui-type="gauge" data-tag-id="371" data-min="0" data-max="250"
                    data-label="TEMPRETURE" data-ranges='' style="width: 195px; height: 210px;">
                </div>
                <div class="plc-gauge float-start" data-ui-type="gauge" data-tag-id="368" data-min="0" data-max="250"
                    data-label="In Feed" data-ranges='' style="width: 195px; height: 210px;">
                </div>
            </div>

        </div>