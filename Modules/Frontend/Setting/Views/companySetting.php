<h1 class="page-header text-center screenTitle"><i class="fa fa-cogs"></i> Screen Setting</h1>

<div class="plcViewBox p-3 rounded-3 shadow">
    <button class="btn mb-3 viewCloseBtn">
        <i class="fa fa-times-circle"></i>
    </button>

    <div class="row mb-4">
        <!-- 6 Head Limits (Left Side) -->
        <div class="col-md-6">
            <table class="table align-middle text-center">
                <thead>
                    <tr>
                        <th>PARAMETER</th>
                        <th>LOW MIN</th>
                        <th>MAX</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th class="fw-bold align-middle">SIDE A</th>
                        <td>
                            <input class="plc-input virtualNumKeypad" data-disable-color="#6c757d" data-disable-condition="" />
                        </td>
                        <td>
                            <input class="plc-input virtualNumKeypad" data-disable-color="#6c757d" data-disable-condition="" />
                        </td>
                    </tr>
                    <tr>
                        <th class="fw-bold align-middle">SIDE B</th>
                        <td>
                            <input class="plc-input virtualNumKeypad" data-disable-color="#6c757d" data-disable-condition="" />
                        </td>
                        <td>
                            <input class="plc-input virtualNumKeypad" data-disable-color="#6c757d" data-disable-condition="" />
                        </td>
                    </tr>
                    <tr>
                        <th class="fw-bold align-middle">Thickness</th>
                        <td>
                            <input class="plc-input virtualNumKeypad" data-disable-color="#6c757d" data-disable-condition="" />
                        </td>
                        <td>
                            <input class="plc-input virtualNumKeypad" data-disable-color="#6c757d" data-disable-condition="" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- RTC & Password Locks (Right Side) -->
        <div class="col-md-6">
            <table class="table align-middle text-center">
                <thead>
                    <tr>
                        <th>Lock</th>
                        <th>DD</th>
                        <th>MM</th>
                        <th>YYYY</th>
                        <th>Passwr</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th class="fw-bold align-middle">Lock 1</th>
                        <td>
                            <input class="plc-input virtualNumKeypad" data-tag-id="280" data-disable-color="#6c757d" data-disable-condition="" />
                        </td>
                        <td>
                            <input class="plc-input virtualNumKeypad" data-tag-id="281" data-disable-color="#6c757d" data-disable-condition="" />
                        </td>
                        <td>
                            <input class="plc-input virtualNumKeypad" data-tag-id="283" data-disable-color="#6c757d" data-disable-condition="" />
                        </td>
                        <td>
                            <input class="plc-input virtualNumKeypad" data-tag-id="282" data-disable-color="#6c757d" data-disable-condition="" />
                        </td>
                    </tr>
                    <tr>
                        <th class="fw-bold align-middle">Lock 2</th>
                        <td>
                            <input class="plc-input virtualNumKeypad" data-tag-id="284" data-disable-color="#6c757d" data-disable-condition="" />
                        </td>
                        <td>
                            <input class="plc-input virtualNumKeypad" data-tag-id="285" data-disable-color="#6c757d" data-disable-condition="" />
                        </td>
                        <td>
                            <input class="plc-input virtualNumKeypad" data-tag-id="287" data-disable-color="#6c757d" data-disable-condition="" />
                        </td>
                        <td>
                            <input class="plc-input virtualNumKeypad" data-tag-id="286" data-disable-color="#6c757d" data-disable-condition="" />
                        </td>
                    </tr>
                    <tr>
                        <th class="fw-bold align-middle">Lock 3</th>
                        <td>
                            <input class="plc-input virtualNumKeypad" data-tag-id="288" data-disable-color="#6c757d" data-disable-condition="" />
                        </td>
                        <td>
                            <input class="plc-input virtualNumKeypad" data-tag-id="289" data-disable-color="#6c757d" data-disable-condition="" />
                        </td>
                        <td>
                            <input class="plc-input virtualNumKeypad" data-tag-id="291" data-disable-color="#6c757d" data-disable-condition="" />
                        </td>
                        <td>
                            <input class="plc-input virtualNumKeypad" data-tag-id="290" data-disable-color="#6c757d" data-disable-condition="" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <hr class="my-4">

    <!-- Bottom Controls -->
    <div class="row align-items-center justify-content-center text-center gap-4">
        <div class="col-md-5 mb-3">
            <label class="fw-bold d-block mb-2">Head Selection (PB)</label>
            <button class="plc-btn btn" data-ui-type="button" data-tag-id="295" data-behavior="maintain"
                data-indicator-id="295" data-on-color="#82c779" data-off-color="#0f75bc"
                data-disable-color="#6c757d" data-disable-condition=""
                data-on-label="6 Head" data-off-label="4 Head"
                data-on-confirm="" data-off-confirm=""></button>
        </div>

        <div class="col-md-5 mb-3">
            <label class="fw-bold d-block mb-2">Safety Bypass</label>
            <button class="plc-btn btn" data-ui-type="button" data-tag-id="318" data-behavior="maintain"
                data-indicator-id="318" data-on-color="#82c779" data-off-color="#0f75bc"
                data-disable-color="#6c757d" data-disable-condition=""
                data-on-label="Safety<br>Bypass ON" data-off-label="Safety<br>Bypass OFF"
                data-on-confirm="" data-off-confirm=""></button>
        </div>
    </div>
</div>
