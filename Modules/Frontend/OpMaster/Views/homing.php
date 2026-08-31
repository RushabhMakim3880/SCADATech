<h1 class="page-header text-center screenTitle"><i class="fa fa-home-alt"></i> Homing</h1>

<div class="plcViewBox p-3 rounded-3 shadow">
    <button class="btn mb-3 viewCloseBtn">
        <i class="fa fa-times-circle"></i>
    </button>

    <table class="table align-middle text-center">
        <thead>
            <tr>
                <td>
                    <button class="plc-btn btn" data-ui-type="button" data-tag-id="323" data-behavior="maintain"
                        data-indicator-id="323" data-on-color="#28a745" data-off-color="#9cbc0fff"
                        data-disable-color="#6c757d" data-disable-condition="" data-on-label="Home<br>Mode"
                        data-off-label="Zero<br>Mode" data-on-confirm="" data-off-confirm=""></button>
                </td>
                <th>MACHINE POS<br>(MM)</th>
                <th>PROXY WEAR<br>(MM)</th>
                <th>HOME POS<br>(MM)</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <button class="plc-btn btn" data-ui-type="button" data-tag-id="331" data-behavior="maintain"
                        data-indicator-id="331" data-on-color="#82c779" data-off-color="#0f75bc"
                        data-disable-color="#6c757d" data-on-label="Princher<br>Home Mode"
                        data-off-label="Princher<br>Home Mode" data-on-confirm="" data-off-confirm=""></button>
                </td>
                <td>
                    <span title="384: SD_X_MACHINE_POS">
                        <input class="plc-input virtualNumKeypad" data-tag-id="384" data-disable-color="#6c757d"
                            data-disable-condition="" disabled />
                    </span>
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad" data-tag-id="421" data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <!-- <td>
                    <input class="plc-input virtualNumKeypad" data-tag-id="00000" data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td> -->
            </tr>
            <tr>
                <td>
                    <button class="plc-btn btn" data-ui-type="button" data-tag-id="333" data-behavior="maintain"
                        data-indicator-id="333" data-on-color="#82c779" data-off-color="#0f75bc"
                        data-disable-color="#6c757d" data-on-label="Head 1<br>Home Mode"
                        data-off-label="Head 1<br>Home Mode" data-on-confirm="" data-off-confirm=""></button>
                </td>
                <td>
                    <span title="385: SD_Y1_MACHINE_POS">
                        <input class="plc-input virtualNumKeypad" data-tag-id="385" data-disable-color="#6c757d"
                            data-disable-condition="" disabled />
                    </span>
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad" data-tag-id="422" data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad" data-tag-id="410" data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
            </tr>
            <tr>
                <td>
                    <button class="plc-btn btn" data-ui-type="button" data-tag-id="335" data-behavior="maintain"
                        data-indicator-id="335" data-on-color="#82c779" data-off-color="#0f75bc"
                        data-disable-color="#6c757d" data-on-label="Head 2<br>Home Mode"
                        data-off-label="Head 2<br>Home Mode" data-on-confirm="" data-off-confirm=""></button>
                </td>
                <td>
                    <span title="386: SD_Y2_MACHINE_POS">
                        <input class="plc-input virtualNumKeypad" data-tag-id="386" data-disable-color="#6c757d"
                            data-disable-condition="" disabled />
                    </span>
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad" data-tag-id="423" data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad" data-tag-id="411" data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
            </tr>
            <tr>
                <td>
                    <button class="plc-btn btn" data-ui-type="button" data-tag-id="337" data-behavior="maintain"
                        data-indicator-id="337" data-on-color="#82c779" data-off-color="#0f75bc"
                        data-disable-color="#6c757d" data-on-label="Head 3<br>Home Mode"
                        data-off-label="Head 3<br>Home Mode" data-on-confirm="" data-off-confirm=""></button>
                </td>
                <td>
                    <span title="387: SD_Y3_MACHINE_POS">
                        <input class="plc-input virtualNumKeypad" data-tag-id="387" data-disable-color="#6c757d"
                            data-disable-condition="" disabled />
                    </span>
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad" data-tag-id="424" data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad" data-tag-id="412" data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
            </tr>
            <tr>
                <td>
                    <button class="plc-btn btn" data-ui-type="button" data-tag-id="339" data-behavior="maintain"
                        data-indicator-id="339" data-on-color="#82c779" data-off-color="#0f75bc"
                        data-disable-color="#6c757d" data-on-label="Head 4<br>Home Mode"
                        data-off-label="Head 4<br>Home Mode" data-on-confirm="" data-off-confirm=""></button>
                </td>
                <td>
                    <span title="388: SD_Y4_MACHINE_POS">
                        <input class="plc-input virtualNumKeypad" data-tag-id="388" data-disable-color="#6c757d"
                            data-disable-condition="" disabled />
                    </span>
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad" data-tag-id="425" data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad" data-tag-id="413" data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
            </tr>
            <tr>
                <td>
                    <button class="plc-btn btn" data-ui-type="button" data-tag-id="341" data-behavior="maintain"
                        data-indicator-id="341" data-on-color="#82c779" data-off-color="#0f75bc"
                        data-disable-color="#6c757d" data-on-label="Head 5<br>Home Mode"
                        data-off-label="Head 5<br>Home Mode" data-on-confirm="" data-off-confirm=""></button>
                </td>
                <td>
                    <span title="389: SD_Y5_MACHINE_POS">
                        <input class="plc-input virtualNumKeypad" data-tag-id="389" data-disable-color="#6c757d"
                            data-disable-condition="" disabled />
                    </span>
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad" data-tag-id="426" data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad" data-tag-id="414" data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
            </tr>
            <tr>
                <td>
                    <button class="plc-btn btn" data-ui-type="button" data-tag-id="343" data-behavior="maintain"
                        data-indicator-id="343" data-on-color="#82c779" data-off-color="#0f75bc"
                        data-disable-color="#6c757d" data-on-label="Head 6<br>Home Mode"
                        data-off-label="Head 6<br>Home Mode" data-on-confirm="" data-off-confirm=""></button>
                </td>
                <td>
                    <span title="390: SD_Y6_MACHINE_POS">
                        <input class="plc-input virtualNumKeypad" data-tag-id="390" data-disable-color="#6c757d"
                            data-disable-condition="" disabled />
                    </span>
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad" data-tag-id="427" data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
                <td>
                    <input class="plc-input virtualNumKeypad" data-tag-id="415" data-disable-color="#6c757d"
                        data-disable-condition="" />
                </td>
            </tr>
        </tbody>
    </table>