<h1 class="page-header text-center screenTitle"><i class="fa fa-gears"></i> Auto Control</h1>

<div class="plcViewBox p-3 rounded-3 shadow">
    <button class="btn mb-3 viewCloseBtn">
        <i class="fa fa-times-circle"></i>
    </button>

    <div class="programAlignment">

        <div class="float-end">

        </div>


        <div class="clearfix"></div>

        <!-- canvas container -->
        <div id="canvasContainer"></div>
        <div id="tooltip"></div>

        <div class="row">
            <div class="col-3 border">

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

                <div class="" style="background-color: #333; color: #fff; padding: 10px; border-radius: 5px;max-height:500px;min-height:200px;overflow-y:auto;">
                    <h3>Active Alarms</h3>
                    <ul class="list-unstyled" id="notificationList" style="font-size: small;">
                        <!-- Notifications will be dynamically added here -->
                        <!-- <li class="text-center">No Alarms yet.</li> -->
                        <!-- sample danger notificaiton -->
                        <!-- <li class="text-danger">Sample Danger Notification</li>
                        <li class="text-success">Sample Success Notification</li>
                        <li class="text-warning">Sample Warning Notification</li>
                        <li class="text-info">Sample Info Notification</li> -->

                    </ul>

                </div>

                <!-- button to refresh alarms -->
                <div class="text-center mt-2">
                    <button class="btn btn-primary btn-sm" id="refreshAlarmsBtn" onclick="loadActiveAlarms();"><i class="fa fa-sync"></i> Refresh Alarms</button>
                </div>

            </div>

            <div class="col-6 border">
                <div class="row">
                    <div class="col-auto border">
                        <table class="table table-sm align-middle text-center">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th class="text-center">AUTO SPEED (RPM)</th>
                                    <th class="text-center">MACHINE POS (MM)</th>
                                    <th class="text-center">LAST SET POS (MM)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>PRINCHER</th>
                                    <td>
                                    <span title="192: S_A_X_VELOCITY">    
                                    <input class="plc-input virtualNumKeypad"
                                            data-tag-id="192"
                                            data-disable-color="#6c757d"
                                            data-disable-condition="" disabled />
                                    </span>
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
                                    <span title="321: S_X_AUTO_MM">    
                                    <input class="plc-input virtualNumKeypad"
                                            data-tag-id="321"
                                            data-disable-color="#6c757d"
                                            data-disable-condition="" disabled />
                                    </span>
                                        </td>

                                </tr>
                                <tr>
                                    <th>HEAD 1</th>
                                    <td>
                                        <input class="plc-input virtualNumKeypad"
                                            data-tag-id="193"
                                            data-disable-color="#6c757d"
                                            data-disable-condition="" />
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
                                        <span title="327: S_Y1_AUTO_MM">
                                        <input class="plc-input virtualNumKeypad"
                                            data-tag-id="327"
                                            data-disable-color="#6c757d"
                                            data-disable-condition="" disabled />
                                        </span>
                                    </td>

                                </tr>
                                <tr>
                                    <th>HEAD 2</th>
                                    <td>
                                        <input class="plc-input virtualNumKeypad"
                                            data-tag-id="194"
                                            data-disable-color="#6c757d"
                                            data-disable-condition="" />
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
                                        <span title="333: S_Y2_AUTO_MM">
                                        <input class="plc-input virtualNumKeypad"
                                            data-tag-id="333"
                                            data-disable-color="#6c757d"
                                            data-disable-condition="" disabled />
                                        </span>
                                    </td>

                                </tr>
                                <tr>
                                    <th>HEAD 3</th>
                                    <td>
                                        <input class="plc-input virtualNumKeypad"
                                            data-tag-id="195"
                                            data-disable-color="#6c757d"
                                            data-disable-condition="" />
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
                                    <span title="339: S_Y3_AUTO_MM">    
                                    <input class="plc-input virtualNumKeypad"
                                            data-tag-id="339"
                                            data-disable-color="#6c757d"
                                            data-disable-condition="" disabled />
                                    </span>
                                        </td>

                                </tr>
                                <tr>
                                    <th>HEAD 4</th>
                                    <td><input class="plc-input virtualNumKeypad"
                                            data-tag-id="196"
                                            data-disable-color="#6c757d"
                                            data-disable-condition="" /></td>
                                    <td>
                                    <span title="373: Y4_ACTPOS">    
                                    <input class="plc-input virtualNumKeypad"
                                            data-tag-id="373"
                                            data-disable-color="#6c757d"
                                            data-disable-condition="" disabled />
                                    </span>
                                        </td>
                                    <td>
                                    <span title="345: S_Y4_AUTO_MM">    
                                    <input class="plc-input virtualNumKeypad"
                                            data-tag-id="345"
                                            data-disable-color="#6c757d"
                                            data-disable-condition="" disabled />
                                    </span>
                                        </td>

                                </tr>
                            </tbody>
                        </table>

                        <table class="table table-sm align-middle text-center">
                            <tbody>
                                <tr>
                                    <th>LEAD SCRAP</th>
                                    <td><input class="virtualNumKeypad leadScrapInput" style="width:80px;" /></td>
                                    <th>PRINCHER SCRAP</th>
                                    <td>    
                                    <input class="virtualNumKeypad princherScrapInput" style="width:80px;" disabled /></td>
                                </tr>
                                <tr>
                                    <th>OIL TEMP</th>
                                    <td>
                                        <span title="351: TEMP_PV">
                                        <input class="plc-input virtualNumKeypad" style="width:80px;"
                                            data-tag-id="351"
                                            data-disable-color="#6c757d"
                                            data-disable-condition="" disabled />
                                        </span>
                                        </td>
                                    <th>BAR LENGTH</th>
                                    <td><input class="plc-input virtualNumKeypad" style="width:80px;"
                                            data-tag-id="212"
                                            data-disable-color="#6c757d"
                                            data-disable-condition="316=false" /></td>
                                </tr>
                                <tr>
                                    <th>EDGE FINDER FIX MM</th>
                                    <td><input class="plc-input virtualNumKeypad"
                                            data-tag-id="494"
                                            data-disable-color="#6c757d"
                                            data-disable-condition="492=false" /></td>
                                    <th>SKIP CUT OPR</th>
                                    <td>
                                        <input type="checkbox" onclick="return confirm('Are you sure you want to skip cut operation?');" id="skipCutOperation" style="width: 2em; height: 2em; vertical-align: middle;" disabled>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                    <div class="col-auto">
                        <table class='table table-sm'>
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
                                    <th>Cycles Run</th>
                                    <td><span id="completedCycles">0</span></td>
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
                                    <th>Punches</th>
                                    <td><span id="punchCounters">0</span></td>
                                </tr>
                                <tr>
                                    <th>Markers</th>
                                    <td><span id="MarkingCounters">0</span></td>
                                </tr>
                                <tr>
                                    <th>Total Operations</th>
                                    <td><span id="TotalOperationsCounter">0</span></td>
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
                                        <button class="plc-btn btn btn-sm w-100"
                                            data-tag-id="491"
                                            data-behavior="maintain"
                                            data-indicator-id="491"
                                            data-on-color="#82c779"
                                            data-off-color="#0f75bc"
                                            data-disable-color="#6c757d"
                                            data-on-label="EDGE FINDER<br>ON"
                                            data-off-label="EDGE FINDER<br>OFF"
                                            data-on-confirm=""
                                            data-off-confirm=""></button>
                                    </td>
                                    <td>
                                        <button class="plc-btn btn btn-sm w-100"
                                            data-tag-id="299"
                                            data-behavior="maintain"
                                            data-indicator-id="299"
                                            data-on-color="#82c779"
                                            data-off-color="#0f75bc"
                                            data-disable-color="#6c757d"
                                            data-on-label="PRINCHER<br>AUTO"
                                            data-off-label="PRINCHER<br>SEMI AUTO"
                                            data-on-confirm=""
                                            data-off-confirm=""></button>
                                    </td>
                                    <td>
                                        <button class="plc-btn btn btn-sm w-100 scrapTypeToggle"
                                            data-tag-id="1"
                                            data-local-flag="true"
                                            data-on-color="#82c779"
                                            data-off-color="#0a4c7a"
                                            data-on-label="PRINCHER SCRAP<br>SELECTED"
                                            data-off-label="LEAD SCRAP<br>SELECTED"></button>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm w-100 btn-primary plcLikeBtn" onclick="loadProgramAlign();">PROGRAM<br>ALIGN</button>
                                    </td>
                                    <td>
                                        <button class="plc-btn btn btn-sm w-100"
                                            data-tag-id="191"
                                            data-behavior="maintain"
                                            data-indicator-id="191"
                                            data-on-color="#82c779"
                                            data-off-color="#0f75bc"
                                            data-disable-color="#6c757d"
                                            data-on-label="6<br>METER"
                                            data-off-label="12<br>METER"
                                            data-on-confirm=""
                                            data-off-confirm=""></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <button class="plc-btn btn btn-sm w-100"
                                            data-tag-id="492"
                                            data-behavior="maintain"
                                            data-indicator-id="492"
                                            data-on-color="#82c779"
                                            data-off-color="#0f75bc"
                                            data-disable-color="#6c757d"
                                            data-on-label="EDGE FINDER<br>FIX DISTANCE"
                                            data-off-label="EDGE FINDER<br>AUTO DISTANCE"
                                            data-on-confirm=""
                                            data-off-confirm=""></button>
                                    </td>
                                    <td>
                                        <button class="plc-btn btn btn-sm w-100"
                                            data-tag-id="316"
                                            data-behavior="maintain"
                                            data-indicator-id="316"
                                            data-on-color="#82c779"
                                            data-off-color="#0f75bc"
                                            data-disable-color="#6c757d"
                                            data-on-label="PRINCHER<br>NO REF"
                                            data-off-label="PRINCHER<br>REF"
                                            data-on-confirm=""
                                            data-off-confirm=""></button>
                                    </td>
                                    <td>
                                        <button class="plc-btn btn btn-sm w-100"
                                            data-tag-id="205"
                                            data-behavior="momentary"
                                            data-indicator-id="205"
                                            data-on-color="#82c779"
                                            data-off-color="#0a4c7a"
                                            data-disable-color="#6c757d"
                                            data-on-label="AUTO<br>LOADING"
                                            data-off-label="AUTO<br>LOADING"
                                            data-on-confirm=""
                                            data-off-confirm=""></button>
                                    </td>
                                    <td>
                                        <button class="plc-btn btn btn-sm w-100"
                                            data-tag-id="202"
                                            data-behavior="momentary"
                                            data-indicator-id="8"
                                            data-on-color="#82c779"
                                            data-off-color="#0a4c7a"
                                            data-disable-color="#6c757d"
                                            data-on-label="ALL<br>HOME"
                                            data-off-label="ALL<br>HOME"
                                            data-on-confirm=""
                                            data-off-confirm=""></button>
                                    </td>
                                    <td>
                                        <button class="plc-btn btn btn-sm w-100"
                                            data-tag-id="217"
                                            data-behavior="maintain"
                                            data-indicator-id="217"
                                            data-on-color="#82c779"
                                            data-off-color="#0f75bc"
                                            data-disable-color="#6c757d"
                                            data-on-label="DECLAMP<br>AT END"
                                            data-off-label="DECLAMP<br>AT CYCL END"
                                            data-on-confirm=""
                                            data-off-confirm=""></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <button style="height:51px;" class="plc-btn btn btn-sm w-100"
                                            data-tag-id="493"
                                            data-behavior="momentary"
                                            data-indicator-id="493"
                                            data-on-color="#82c779"
                                            data-off-color="#0a4c7a"
                                            data-disable-color="#6c757d"
                                            data-on-label="REFERENCE"
                                            data-off-label="REFERENCE"
                                            data-on-confirm=""
                                            data-off-confirm=""></button>
                                    </td>
                                    <td>
                                        <a class="plc-btn btn btn-sm w-100"
                                            data-tag-id="304"
                                            data-indicator-only="true"
                                            data-behavior="maintain"
                                            data-indicator-id="304"
                                            data-on-color="#82c779"
                                            data-off-color="#ff5b57"
                                            data-disable-color="#6c757d"
                                            data-on-label="PRINCHER<br>SAFE"
                                            data-off-label="PRINCHER<br>NOT SAFE"
                                            data-on-confirm=""
                                            data-off-confirm=""
                                            data-bs-toggle="modal"
                                            href="#princherSafeScreen"></a>
                                    </td>
                                    <td>
                                        <!-- <button class="btn btn-sm w-100 autoStartMode btn-primary" data-mode="selected">AUTO START<br>FROM SELECTED</button> -->
                                        <button class="plc-btn btn btn-sm w-100"
                                            data-tag-id="211"
                                            data-behavior="maintain"
                                            data-indicator-id="211"
                                            data-on-color="#82c779"
                                            data-off-color="#f59c1a"
                                            data-disable-color="#6c757d"
                                            data-on-label="AUTO START<br>FROM SELECTED"
                                            data-off-label="AUTO START<br>FROM SELECTED"
                                            data-on-confirm=""
                                            data-off-confirm=""></button>
                                    </td>
                                    <td>
                                        <!-- <button class="btn btn-sm w-100 autoStartMode btn-primary" data-mode="first">AUTO START<br>FROM FIRST</button> -->

                                        <button class="plc-btn btn btn-sm w-100"
                                            data-tag-id="210"
                                            data-behavior="maintain"
                                            data-indicator-id="210"
                                            data-on-color="#82c779"
                                            data-off-color="#f59c1a"
                                            data-disable-color="#6c757d"
                                            data-on-label="AUTO START<br>FROM FIRST"
                                            data-off-label="AUTO START<br>FROM FIRST"
                                            data-on-confirm=""
                                            data-off-confirm=""></button>

                                    </td>
                                    <td>
                                        <button class="plc-btn btn btn-sm w-100"
                                            data-tag-id="209"
                                            data-behavior="maintain"
                                            data-indicator-id="209"
                                            data-on-color="#82c779"
                                            data-off-color="#f59c1a"
                                            data-disable-color="#6c757d"
                                            data-on-label="FULLY<br>AUTO"
                                            data-off-label="SEMI<br>AUTO"
                                            data-on-confirm=""
                                            data-off-confirm=""></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <button style="height:51px;" class="plc-btn btn btn-sm w-100"
                                            data-tag-id="490"
                                            data-behavior="momentary"
                                            data-indicator-id="189"
                                            data-on-color="#ff5b57"
                                            data-off-color="#82c779"
                                            data-disable-color="#6c757d"
                                            data-on-label="ALARM RESET"
                                            data-off-label="ALARM RESET"
                                            data-on-confirm=""
                                            data-off-confirm=""></button>
                                    </td>
                                    <td>
                                        <button class="plc-btn btn btn-sm w-100"
                                            data-tag-id="556"
                                            data-behavior="momentary"
                                            data-indicator-id="556"
                                            data-on-color="#82c779"
                                            data-off-color="#0a4c7a"
                                            data-disable-color="#6c757d"
                                            data-on-label="PRINCHER<br>AT ZERO"
                                            data-off-label="PRINCHER<br>AT ZERO"
                                            data-on-confirm=""
                                            data-off-confirm=""></button>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>
            <div class="col-3 border">
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
                <br>
                <button class="btn btn-danger" id="clearGoBack"><i class="fa fa-trash"></i> Clear Program</button>
                <button class="btn btn-info" id="initDebug">DEBUG</button>
                <span class="float-end">
                    <input type="checkbox" onclick="return confirm('Are you sure you want to enable auto run, in step by step mode?.. this is for debugging purpose only');" id="autoRunCheckbox" style="width: 2em; height: 2em; vertical-align: middle;">
                    <button class="btn btn-primary" id="btnNextStep"><i class="fa fa-step-forward"></i> Next Opr.</button>
                </span>
            </div>

        </div>










    </div>


</div>
</div>