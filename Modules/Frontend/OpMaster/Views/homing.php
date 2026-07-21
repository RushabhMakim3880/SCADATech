<h1 class="page-header text-center screenTitle"><i class="fa fa-home-alt"></i> Homing</h1>

<div class="plcViewBox p-3 rounded-3 shadow">
    <button class="btn mb-3 viewCloseBtn">
        <i class="fa fa-times-circle"></i>
    </button>

    <table class="table align-middle text-center">
        <thead>
            <tr>
                <td>
                    <button class="plc-btn btn"
                        data-ui-type="button"
                        data-tag-id="12"
                        data-behavior="maintain"
                        data-indicator-id="12"
                        data-on-color="#28a745"
                        data-off-color="#0f75bc"
                        data-disable-color="#6c757d"
                        data-disable-condition=""
                        data-on-label="Homing<br>Mode"
                        data-off-label="Homing<br>Mode"
                        data-on-confirm=""
                        data-off-confirm=""></button>
                </td>
                <th>MACHINE POS<br>(MM)</th>
                <th>HOMING SPEED<br>(RPM)</th>
                <th>PROXY WEAR<br>(MM)</th>
                <th>WEAR<br>(MM)</th>
                <th>HOME POS<br>(MM)</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <button class="plc-btn btn"
                        data-ui-type="button"
                        data-tag-id="323"
                        data-behavior="maintain"
                        data-indicator-id="323"
                        data-on-color="#82c779"
                        data-off-color="#0f75bc"
                        data-disable-color="#6c757d"
                        data-on-label="Princher<br>Home Mode"
                        data-off-label="Princher<br>Home Mode"
                        data-on-confirm=""
                        data-off-confirm=""></button>
                </td>
                <td>
                    <span title="353: X_ACTPOS">
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="353"
                        data-disable-color="#6c757d"
                        data-disable-condition="" disabled />
                    </span>
                </td>
                <td>
                    <input type="number" class="plc-input virtualNumKeypad"
                        data-tag-id="222"
                        data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="324"
                        data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="326"
                        data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="322"
                        data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <button class="plc-btn btn"
                        data-ui-type="button"
                        data-tag-id="325"
                        data-behavior="momentary"
                        data-indicator-id="355"
                        data-on-color="#82c779"
                        data-off-color="#0a4c7a"
                        data-disable-color="#6c757d"
                        data-disable-condition=""
                        data-on-label="X Alarm<br>Reset"
                        data-off-label="X Alarm<br>Reset"
                        data-on-confirm=""
                        data-off-confirm=""></button>
                </td>
            </tr>
            <tr>
                <td>
                    <button class="plc-btn btn"
                        data-ui-type="button"
                        data-tag-id="329"
                        data-behavior="maintain"
                        data-indicator-id="329"
                        data-on-color="#82c779"
                        data-off-color="#0f75bc"
                        data-disable-color="#6c757d"
                        data-on-label="Head 1<br>Home Mode"
                        data-off-label="Head 1<br>Home Mode"
                        data-on-confirm=""
                        data-off-confirm=""></button>
                </td>
                <td>
                    <span title="358: Y1_ACTPOS">
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="358"
                        data-disable-color="#6c757d"
                        data-disable-condition="" disabled />
                    </span>
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="223"
                        data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="330"
                        data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="332"
                        data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="328"
                        data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <button class="plc-btn btn"
                        data-ui-type="button"
                        data-tag-id="331"
                        data-behavior="momentary"
                        data-indicator-id="359"
                        data-on-color="#82c779"
                        data-off-color="#0a4c7a"
                        data-disable-color="#6c757d"
                        data-disable-condition=""
                        data-on-label="Y Alarm<br>Reset"
                        data-off-label="Y Alarm<br>Reset"
                        data-on-confirm=""
                        data-off-confirm=""></button>
                </td>
            </tr>
            <tr>
                <td>
                    <button class="plc-btn btn"
                        data-ui-type="button"
                        data-tag-id="335"
                        data-behavior="maintain"
                        data-indicator-id="335"
                        data-on-color="#82c779"
                        data-off-color="#0f75bc"
                        data-disable-color="#6c757d"
                        data-on-label="Head 2<br>Home Mode"
                        data-off-label="Head 2<br>Home Mode"
                        data-on-confirm=""
                        data-off-confirm=""></button>
                </td>
                <td>
                    <span title="363: Y2_ACTPOS">
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="363"
                        data-disable-color="#6c757d"
                        data-disable-condition="" disabled />
                    </span>
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="224"
                        data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="336"
                        data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="338"
                        data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="334"
                        data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <button class="plc-btn btn"
                        data-ui-type="button"
                        data-tag-id="337"
                        data-behavior="momentary"
                        data-indicator-id="364"
                        data-on-color="#82c779"
                        data-off-color="#0a4c7a"
                        data-disable-color="#6c757d"
                        data-disable-condition=""
                        data-on-label="Y2 Alarm<br>Reset"
                        data-off-label="Y2 Alarm<br>Reset"
                        data-on-confirm=""
                        data-off-confirm=""></button>
                </td>
            </tr>
            <tr>
                <td>
                    <button class="plc-btn btn"
                        data-ui-type="button"
                        data-tag-id="341"
                        data-behavior="maintain"
                        data-indicator-id="341"
                        data-on-color="#82c779"
                        data-off-color="#0f75bc"
                        data-disable-color="#6c757d"
                        data-on-label="Head 3<br>Home Mode"
                        data-off-label="Head 3<br>Home Mode"
                        data-on-confirm=""
                        data-off-confirm=""></button>
                </td>
                <td>
                    <span title="368: Y3_ACTPOS">
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="368"
                        data-disable-color="#6c757d"
                        data-disable-condition="" disabled />
                    </span>
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="225"
                        data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="342"
                        data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="344"
                        data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="340"
                        data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <button class="plc-btn btn"
                        data-ui-type="button"
                        data-tag-id="343"
                        data-behavior="momentary"
                        data-indicator-id="369"
                        data-on-color="#82c779"
                        data-off-color="#0a4c7a"
                        data-disable-color="#6c757d"
                        data-disable-condition=""
                        data-on-label="Y3 Alarm<br>Reset"
                        data-off-label="Y3 Alarm<br>Reset"
                        data-on-confirm=""
                        data-off-confirm=""></button>
                </td>
            </tr>
            <tr>
                <td>
                    <button class="plc-btn btn"
                        data-ui-type="button"
                        data-tag-id="347"
                        data-behavior="maintain"
                        data-indicator-id="347"
                        data-on-color="#82c779"
                        data-off-color="#0f75bc"
                        data-disable-color="#6c757d"
                        data-on-label="Head 4<br>Home Mode"
                        data-off-label="Head 4<br>Home Mode"
                        data-on-confirm=""
                        data-off-confirm=""></button>
                </td>
                <td>
                    <span title="373: Y4_ACTPOS">
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="373"
                        data-disable-color="#6c757d"
                        data-disable-condition="" disabled />
                    </span>
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="226"
                        data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="348"
                        data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="350"
                        data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="346"
                        data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <button class="plc-btn btn"
                        data-ui-type="button"
                        data-tag-id="349"
                        data-behavior="momentary"
                        data-indicator-id="374"
                        data-on-color="#82c779"
                        data-off-color="#0a4c7a"
                        data-disable-color="#6c757d"
                        data-disable-condition=""
                        data-on-label="Y4 Alarm<br>Reset"
                        data-off-label="Y4 Alarm<br>Reset"
                        data-on-confirm=""
                        data-off-confirm=""></button>
                </td>
            </tr>

            <tr>
                <td>
                    <button class="plc-btn btn"
                        data-ui-type="button"
                        data-tag-id="382"
                        data-behavior="maintain"
                        data-indicator-id="382"
                        data-on-color="#82c779"
                        data-off-color="#0f75bc"
                        data-disable-color="#6c757d"
                        data-on-label="EDGE FINDER<br>Home Mode"
                        data-off-label="EDGE FINDER<br>Home Mode"
                        data-on-confirm=""
                        data-off-confirm=""></button>
                </td>
                <td>
                    <span title="383: X1_ACTPOS">
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="383"
                        data-disable-color="#6c757d"
                        data-disable-condition="" disabled />
                    </span>
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="384"
                        data-disable-color="#6c757d"
                        data-disable-condition="" /><br>
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="385"
                        data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="386"
                        data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td></td>
                <td>
                    <input class="plc-input virtualNumKeypad"
                        data-tag-id="387"
                        data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <button class="plc-btn btn"
                        data-ui-type="button"
                        data-tag-id="388"
                        data-behavior="momentary"
                        data-indicator-id="389"
                        data-on-color="#82c779"
                        data-off-color="#0a4c7a"
                        data-disable-color="#6c757d"
                        data-disable-condition=""
                        data-on-label="EDGE FINDER<br>Reset"
                        data-off-label="EDGE FINDER<br>Reset"
                        data-on-confirm=""
                        data-off-confirm=""></button>
                </td>
            </tr>
        </tbody>
    </table>




    <!-- <button class="plc-btn btn"
        data-ui-type="button"
        data-tag-id="1"
        data-behavior="maintain"
        data-indicator-id="1"
        data-on-color="#28a745"
        data-off-color="#dc3545"
        data-disable-color="#6c757d"
        data-disable-condition="2=103"
        data-on-label="Stop Pump"
        data-off-label="Start Pump"
        data-on-confirm=""
        data-off-confirm=""></button>

    <select class="plc-dropdown"
        data-ui-type="dropdown"
        data-tag-id="2"
        data-list='[50:Auto,100:Manual,150:Off]'
        data-confirm="Change mode?"
        data-disable-condition="1=false">
    </select>


    <span class="plc-output"
        data-ui-type="output"
        data-tag-id="2"
        data-label="Current Pressure">
    </span>


    <input class="plc-input virtualNumKeypad"
        data-tag-id="2"
        data-disable-color="#6c757d"
        data-disable-condition="1=false" />

    <div class="plc-listbox list-group d-inline-flex flex-wrap"
        data-ui-type="listbox"
        data-tag-id="2"
        data-disable-color="#ccc"
        data-disable-condition="1=false"
        data-confirm="Are you sure to change this settings?"
        data-list='[101:Auto,102:Manual,103:Off]'>
    </div>

    <div class="plc-gauge"
        data-ui-type="gauge"
        data-tag-id="2"
        data-min="0"
        data-max="200"
        data-label="Pressure"
        data-ranges='[{"from":0,"to":50,"color":"#28a745"},{"from":51,"to":100,"color":"#ffc107"},{"from":101,"to":200,"color":"#dc3545"}]'
        style="width: 200px; height: 200px;">
    </div>


    <button class="plc-btn btn btn-sm"
        data-ui-type="button"
        data-tag-id="10"
        data-behavior="momentary"
        data-indicator-id="10"
        data-on-color="#28a745"
        data-off-color="#dc3545"
        data-disable-color="#6c757d"
        data-disable-condition=""
        data-on-label="Started..."
        data-off-label="Stopped"
        data-on-confirm="Are you sure to stop?"
        data-off-confirm="Start the pump now?"></button>

    <span class="plc-output"
        data-ui-type="output"
        data-tag-id="10"
        data-label="Current Pressure">
    </span> -->
</div>





<!-- <a href="javascript:void(0);" class="btn btn-primary apiPopup" data-title="Machine Setup" data-size="sm" data-endpoint="<?= base_url("MachineMaster/machineSetup"); ?>">Machine Setup</a> -->