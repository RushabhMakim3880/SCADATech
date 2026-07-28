<h1 class="page-header text-center screenTitle"><i class="fa fa-gears"></i> Auto Control</h1>

<div class="plcViewBox p-3 rounded-3 shadow">
    <button class="btn mb-3 viewCloseBtn">
        <i class="fa fa-times-circle"></i>
    </button>

    <div class="programAlignment">

        <div class="float-end">

        </div>


        <div class="clearfix"></div>

        <div class="row g-3">
            <div class="col-12 col-lg-6 border order-1">

                <div class=""
                    style="background-color: #333; color: #fff; padding: 10px; border-radius: 5px;max-height:350px;min-height:200px;overflow-y:auto;">
                    <h3>Alarm</h3>
                    <ul class="list-unstyled" id="notificationList" style="font-size: small;">
                    </ul>

                </div>

                <div class="text-center mt-2">
                    <td>
                        <button style="height:51px;" class="plc-btn btn btn-sm w-100" data-tag-id="312"
                            data-behavior="momentary" data-indicator-id="12" data-on-color="#ff5b57"
                            data-off-color="#82c779" data-disable-color="#6c757d" data-on-label="ALARM RESET"
                            data-off-label="ALARM RESET" data-on-confirm="" data-off-confirm=""></button>
                    </td>
                </div>



            </div>

            <div class="col-12 col-lg-6 border order-2">
                <h3>Program Align</h3>
                <div id="toolbar" class="my-1">
                    <button class="btn btn-secondary" id="btnReset"><i class="fa fa-refresh"></i></button>
                    <button class="btn btn-secondary" id="btnLeft"><i class="fa fa-arrow-left"></i></button>
                    <button class="btn btn-secondary" id="btnRight"><i class="fa fa-arrow-right"></i></button>
                    <button class="btn btn-secondary" id="btnCenter"><i class="fa fa-align-center"></i></button>
                    <button class="btn btn-secondary" id="btnFit"><i class="fa fa-text-width"></i></button>
                    <button class="btn btn-secondary" id="btnExpand"><i class="fa fa-expand"></i></button>
                    <button class="btn btn-secondary" id="btnFlip"><i class="fa fa-random"></i></button>
                    <button class="btn btn-danger" id="btnShowHideCanvas"><i class="fa fa-eye-slash"></i></button>
                </div>

                <!-- canvas container -->
                <div id="canvasContainer"></div>
                <div id="tooltip"></div>

                <div style="max-height:600px;min-height:200px;overflow-y:auto;">
                    <div class="programOutput">
                        <div class="d-flex align-items-center justify-content-center" style="min-height: 300px;">
                            <div class="alert alert-info w-100 text-center m-0">
                                <h4 class="alert-heading">Program Output</h4>
                                <p>Program output will be displayed here after <b>Program Align</b>.</p>
                                <hr>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-2 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-danger" id="clearGoBack"><i class="fa fa-trash"></i> </button>
                        <!-- <button class="btn btn-info" id="initDebug">DEBUG</button> -->
                        <!-- <span>
                            <input type="checkbox" id="toolWiseOperationAlign"
                                style="width: 2em; height: 2em; vertical-align: middle;">
                            Tool Wise Opr Align
                        </span> -->
                    </div>

                    <div>
                        <input type="checkbox"
                            onclick="return confirm('Are you sure you want to enable auto run, in step by step mode?.. this is for debugging purpose only');"
                            id="autoRunCheckbox" style="width: 2em; height: 2em; vertical-align: middle;">
                        <button class="btn btn-primary" id="btnNextStep"><i class="fa fa-step-forward"></i> Next
                            Opr.</button>
                    </div>
                </div>
            </div>

            <div class="col-12 border order-3">
                <div class="row">
                    <div class="col-12 col-lg-8 border order-2">
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
                        </table>

                        <table class="table table-sm align-middle text-center" style="font-size:0.8rem;">
                            <tbody>
                                <tr>
                                    <th>CYCLE TIME(MIN)</th>
                                    <td>
                                        <span title="491:SD_CYC_TIME">
                                            <input class="plc-input virtualNumKeypad" style="width:80px;"
                                                data-tag-id="491" data-disable-color="#6c757d" data-disable-condition=""
                                                disabled />
                                        </span>
                                    </td>
                                    <th>CUT GAP(MM)</th>
                                    <td>
                                        <span title="490:SET_CUT_GAP">
                                            <input class="plc-input virtualNumKeypad" style="width:80px;"
                                                data-tag-id="490" data-disable-color="#6c757d" data-disable-condition=""
                                                disabled />
                                        </span>
                                        <!-- <span title="685:SD_BSIDE_HOLE_TIME_SEC">
                                            <input class="plc-input virtualNumKeypad" style="width:80px;"
                                                data-tag-id="685" data-disable-color="#6c757d" data-disable-condition=""
                                                disabled />
                                        </span> -->
                                    </td>
                                </tr>
                                <!-- <tr>
                                    <th>CUT GAP(MM)</th>
                                    <td><input class="plc-input virtualNumKeypad" style="width:80px;" data-tag-id="686"
                                            data-disable-color="#6c757d" data-disable-condition="" /></td>
                                    <th>MARKING</th>
                                    <td>
                                        <span title="269:M_CHAR">
                                            <input class="plc-input virtualNumKeypad" style="width:80px;"
                                                data-tag-id="269" data-disable-color="#6c757d" data-disable-condition=""
                                                disabled />
                                        </span>
                                        <span title="495:SD_M_ACT_POS">
                                            <input class="plc-input virtualNumKeypad" style="width:80px;"
                                                data-tag-id="495" data-disable-color="#6c757d" data-disable-condition=""
                                                disabled />
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Marking Bypass</th>
                                    <td>
                                        <button class="plc-btn btn btn-xs" style="width: 80px;" data-tag-id="452"
                                            data-indicator-id="452" data-behavior="maintain" data-on-color="#82c779"
                                            data-off-color="#ff5b57" data-disable-color="#6c757d" data-on-label="ON"
                                            data-off-label="OFF" data-on-confirm="" data-off-confirm=""></button>
                                    </td> -->
                                <th class="cutOperationsControl">OIL TEMP</th>
                                <td>

                                    <span title="371:SD_ACT_TEMP">
                                        <input class="plc-input virtualNumKeypad" style="width:80px;" data-tag-id="371"
                                            data-disable-color="#6c757d" data-disable-condition="" disabled />
                                    </span>
                                </td>
                                <th>SKIP CUT OPR</th>
                                <td>
                                    <input type="checkbox"
                                        onclick="return confirm('Are you sure you want to skip cut operation?');"
                                        id="skipCutOperation" style="width: 2em; height: 2em; vertical-align: middle;"
                                        disabled>
                                </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    <div class="col-12 col-lg-4 order-1">
                        <table class='table table-sm' style="font-size: 0.9rem;">
                            <tbody>
                                <tr>
                                    <th>RUN SUMMARY</th>
                                    <td class="text-center">
                                        <div id="debugDot1" class="dot" title="Line Read Command"></div>
                                        <div id="debugDot2" class="dot" title="Auto Run Command"></div><br>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Actual Bar Length</th>
                                    <td><span id="actualBarLength">0</span> mm</td>
                                </tr>
                                <tr>
                                    <th>Program Length</th>
                                    <td><span id="programLength">0</span> mm</td>
                                </tr>
                                <tr>
                                    <th>Next Line</th>
                                    <td><span id="nextLineNumber">0</span></td>
                                </tr>
                                <tr>
                                    <th>Total Items</th>
                                    <td><span id="totalItemsDone">0</span></td>
                                </tr>
                                <tr>
                                    <th>Holes</th>
                                    <td><span id="punchCounters">0</span></td>
                                </tr>
                                <tr>
                                    <th>Marker</th>
                                    <td>
                                        <span class="plc-output" data-ui-type="output" data-tag-id="413"
                                            data-label="Marker">
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total Operations</th>
                                    <td><span id="TotalOperationsCounter">0</span></td>
                                </tr>
                                <tr>
                                    <th>Program</th>
                                    <td><span id="runningProgramName"></span></td>
                                </tr>
                                <tr>
                                    <th>Auto Cycle On</th>
                                    <td class="text-center">
                                        <div id="autoCycleOnIndicator" class="dot" title="Auto Cycle On"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Program Align</th>
                                    <td class="text-center">
                                        <div id="programAlignIndicator" class="dot" title="Program Align Command"></div>
                                        <br>
                                    </td>
                                </tr>

                            </tbody>
                        </table>


                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <table class="table table-sm align-middle text-center">
                            <tbody>
                                <tr>
                                    <td>
                                        <button class="plc-btn btn btn-sm w-100" data-tag-id="316"
                                            data-behavior="maintain" data-indicator-id="316" data-on-color="#82c779"
                                            data-off-color="#f59c1a" data-disable-color="#6c757d"
                                            data-on-label="AUTO START<br>FROM FIRST"
                                            data-off-label="AUTO START<br>FROM FIRST" data-on-confirm=""
                                            data-off-confirm=""></button>
                                    </td>
                                    <td>
                                        <button class="plc-btn btn btn-sm w-100" data-tag-id="317"
                                            data-behavior="maintain" data-indicator-id="317" data-on-color="#82c779"
                                            data-off-color="#f59c1a" data-disable-color="#6c757d"
                                            data-on-label="AUTO START<br>FROM SELECTED"
                                            data-off-label="AUTO START<br>FROM SELECTED" data-on-confirm=""
                                            data-off-confirm=""></button>
                                    </td>
                                    <td>
                                        <button class="plc-btn btn btn-sm w-100 autoLoadingBypass" data-tag-id="320"
                                            data-behavior="maintain" data-indicator-id="320" data-on-color="#82c779"
                                            data-off-color="#0f75bc" data-disable-color="#6c757d"
                                            data-on-label="CYCLE<br>FULLY AUTO" data-off-label="CYCLE<br>SEMI AUTO"
                                            data-on-confirm="" data-off-confirm=""></button>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm w-100 btn-primary plcLikeBtn"
                                            onclick="loadProgramAlign();">PROGRAM<br>ALIGN</button>
                                    </td>
                                    <td>
                                        <button class="plc-btn btn btn-sm w-100" data-tag-id="313"
                                            data-behavior="momentary" data-indicator-id="297" data-on-color="#82c779"
                                            data-off-color="#0a4c7a" data-disable-color="#6c757d"
                                            data-on-label="ALL<br>HOME" data-off-label="ALL<br>HOME" data-on-confirm=""
                                            data-off-confirm=""></button>
                                    </td>
                                    <td>
                                        <a class="plc-btn btn btn-sm w-100" data-tag-id="213" data-indicator-only="true"
                                            data-behavior="maintain" data-indicator-id="213" data-on-color="#82c779"
                                            data-off-color="#ff5b57" data-disable-color="#6c757d"
                                            data-on-label="PRINCHER<br>SAFE" data-off-label="PRINCHER<br>NOT SAFE"
                                            data-on-confirm="" data-off-confirm="" data-bs-toggle="modal"
                                            href="#princherSafeScreen"></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <button class="plc-btn btn btn-sm w-100" data-tag-id="319"
                                            data-behavior="maintain" data-indicator-id="319" data-on-color="#82c779"
                                            data-off-color="#0f75bc" data-disable-color="#6c757d"
                                            data-on-label="DECLAMP AT<br>LOADING SIDE"
                                            data-off-label="DECLAMP AT<br>UNLOAD SIDE" data-on-confirm=""
                                            data-off-confirm=""></button>
                                    </td>
                                    <td>
                                        <button class="plc-btn btn btn-sm w-100" data-tag-id="311"
                                            data-behavior="momentary" data-indicator-id="311" data-on-color="#82c779"
                                            data-off-color="#0f75bc" data-disable-color="#6c757d"
                                            data-on-label="PRINCHER<br>@ HOME" data-off-label="PRINCHER<br>@ HOME"
                                            data-on-confirm="" data-off-confirm=""></button>
                                    </td>
                                    <td>
                                        <button class="plc-btn btn btn-sm w-100" data-tag-id="315"
                                            data-behavior="maintain" data-indicator-id="315" data-on-color="#82c779"
                                            data-off-color="#0f75bc" data-disable-color="#6c757d"
                                            data-on-label="AUTO LOADING<br>ON" data-off-label="AUTO LOADING<br>OFF"
                                            data-on-confirm="" data-off-confirm=""></button>
                                    </td>
                                    <td>
                                        <button class="plc-btn btn btn-sm w-100 autoLoadingBypass" data-tag-id="314"
                                            data-behavior="momentary" data-indicator-id="314" data-on-color="#82c779"
                                            data-off-color="#0a4c7a" data-disable-color="#6c757d"
                                            data-on-label="LOADING<br>PB" data-off-label="LOADING<br>PB"
                                            data-on-confirm="" data-off-confirm=""></button>
                                    </td>
                                    <td>
                                        <button class="plc-btn btn btn-sm w-100" data-tag-id="329"
                                            data-behavior="maintain" data-indicator-id="329" data-on-color="#82c779"
                                            data-off-color="#0f75bc" data-disable-color="#6c757d"
                                            data-on-label="REFERENCE<br>PB" data-off-label="REFERENCE<br>PB"
                                            data-on-confirm="" data-off-confirm=""></button>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <button class="plc-btn btn btn-sm w-100 scrapTypeToggle" data-tag-id="1"
                                            data-local-flag="true" data-on-color="#82c779" data-off-color="#0a4c7a"
                                            data-on-label="PRINCHER SCRAP<br>SELECTED"
                                            data-off-label="LEAD SCRAP<br>SELECTED"></button>
                                    </td>
                                    <td colspan="4">
                                        <table class="w-100 table-xs">
                                            <tr>
                                                <td>
                                                    LEAD SCRAP(MM):
                                                </td>
                                                <td>
                                                    <input class="virtualNumKeypad leadScrapInput"
                                                        style="width:80px;" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    PRINCHER SCRAP (MM):
                                                </td>
                                                <td>
                                                    <input class="virtualNumKeypad princherScrapInput"
                                                        style="width:80px;" disabled />
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <button class="plc-btn btn btn-sm w-100" data-tag-id="326"
                                            data-behavior="maintain" data-indicator-id="326" data-on-color="#82c779"
                                            data-off-color="#0f75bc" data-disable-color="#6c757d"
                                            data-on-label="AUTO<br>REFERENCE" data-off-label="AUTO<br>REFERENCE"
                                            data-on-confirm="" data-off-confirm=""></button>
                                    </td>
                                    <td colspan="4">
                                        <table class="w-100 table-xs">
                                            <tr>
                                                <td>
                                                    RM LENGTH(MM):
                                                </td>
                                                <td>
                                                    <input class="plc-input virtualNumKeypad" style="width:80px;"
                                                        data-tag-id="296" data-disable-color="#6c757d" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    RM THICKNESS:
                                                </td>
                                                <td>
                                                    <span title="495:S_RM_THICKNESS">
                                                        <input class="plc-input virtualNumKeypad" style="width:80px;"
                                                            data-tag-id="495" data-disable-color="#6c757d" disabled />
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>

        </div>
    </div>

</div>
<div class="modal fade" id="dataTags">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">CHIP BREAK CYCLE DATA</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">

                <table class="table table-sm ">
                    <tbody>
                        <tr>
                            <th></th>
                            <th>A SIDE</th>
                            <th>B SIDE</th>
                        </tr>
                        <tr>
                            <th>DEPTH INCREMENTS (MM)</th>
                            <td><input class="plc-input virtualNumKeypad" data-tag-id="555" data-disable-color="#6c757d"
                                    data-disable-condition="" /></td>
                            <td><input class="plc-input virtualNumKeypad" data-tag-id="556" data-disable-color="#6c757d"
                                    data-disable-condition="" /></td>
                        </tr>
                        <tr>
                            <th>RETRACT (MM)</th>
                            <td><input class="plc-input virtualNumKeypad" data-tag-id="557" data-disable-color="#6c757d"
                                    data-disable-condition="" /></td>
                            <td><input class="plc-input virtualNumKeypad" data-tag-id="558" data-disable-color="#6c757d"
                                    data-disable-condition="" /></td>
                        </tr>
                        <tr>
                            <th>STEP CLEARANCE (MM)</th>
                            <td><input class="plc-input virtualNumKeypad" data-tag-id="595" data-disable-color="#6c757d"
                                    data-disable-condition="" /></td>
                            <td><input class="plc-input virtualNumKeypad" data-tag-id="596" data-disable-color="#6c757d"
                                    data-disable-condition="" /></td>
                        </tr>
                        <tr>
                            <th>TOP OFFSET (MM)</th>
                            <td><input class="plc-input virtualNumKeypad" data-tag-id="662" data-disable-color="#6c757d"
                                    data-disable-condition="" /></td>
                            <td><input class="plc-input virtualNumKeypad" data-tag-id="663" data-disable-color="#6c757d"
                                    data-disable-condition="" /></td>
                        </tr>
                        <tr>
                            <th>BOTTOM OFFSET (MM)</th>
                            <td><input class="plc-input virtualNumKeypad" data-tag-id="534" data-disable-color="#6c757d"
                                    data-disable-condition="" /></td>
                            <td><input class="plc-input virtualNumKeypad" data-tag-id="535" data-disable-color="#6c757d"
                                    data-disable-condition="" /></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>