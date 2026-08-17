<style>
    /* #0a4c7a for momentary
         #0f75bc for normal */
    input[readonly] {
        background-color: #ffffff !important;
        opacity: 1 !important;
        border-width: 1px !important;
        /* cursor: not-allowed; */
    }

    #toolbar {
        /* position: absolute; */
        /* top: 10px;
        left: 10px; */
        /* z-index: 1000; */
    }

    #toolbar button {
        margin-right: 5px;
    }

    #container {
        width: 100%;
        height: 160px;
        background-color: white;
    }

    .selected-row {
        background-color: #bdf5b5;
    }

    .data-row {
        cursor: pointer;
    }

    .data-row:hover {
        background-color: #b3dffc;
    }

    body {
        background: url('<?= base_url('assets/img/datanicsBack.jpg') ?>') no-repeat center center fixed;
        background-size: cover;
        height: 100vh;
        margin: 0;
    }

    .centerLogo {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    #spaContainer {
        position: relative;
    }

    .viewCloseBtn {
        position: absolute;
        top: 10px;
        right: 0px;
        z-index: 1000;
        background-color: white;
        padding: 2px 5px;
    }

    .screenTitle {
        font-weight: 700;
        color: #0f75bc;
        margin-bottom: 5px;
    }

    #content {
        padding-top: 5px;
    }

    .table-sm {
        font-size: 0.75rem;
    }

    .plcViewBox {
        background-color: rgba(255, 255, 255, 0.85);
    }

    .plc-btn.btn-sm,
    .plcLikeBtn.btn-sm {
        /* font-size: small; */
        min-width: 100px;
    }

    .programOutputTable {
        border-collapse: separate;
        border-spacing: 0;
    }

    .programOutputTable thead th {
        position: sticky;
        top: 0;
        background: #f8f9fa;
        z-index: 2;
    }

    .programOutputTable td:first-child,
    .programOutputTable th:first-child {
        position: sticky;
        left: 0;
        background: #fff;
        z-index: 1;
    }

    .programOutputTable thead th:first-child {
        z-index: 3;
        /* ensures top-left cell is on top */
    }

    /* For Chrome, Safari, Edge, Opera */
    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* For Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }

    .dot {
        height: 15px;
        width: 15px;
        border-radius: 50%;
        display: inline-block;
        background-color: gray;
        /* margin: 5px; */
    }

    .dot.green {
        background-color: green;
    }

    .dot.red {
        background-color: red;
    }

    .log-entry {
        word-wrap: break-word;
    }
</style>
<!-- BEGIN #content -->
<div id="content" class="app-content">

    <div class="">
        <img src="<?= base_url('assets/img/hptlogo2.png') ?>" alt="OpMaster Image" class="centerLogo"
            style="max-height: 150px; width: auto;">
    </div>

    <?php
    if (isset($identifier) and $identifier != '') {
        echo "<div class='alert alert-warning text-center'>Identifier: <strong>$identifier</strong>.</div>";
    }
    ?>

    <div id="spaContainer"></div>

    <?php
    if ($isIpc) {
        ?>

        <div class="position-fixed bottom-0 w-100 text-center pb-2" style="pointer-events: none;">
            <div class="d-inline-flex flex-wrap gap-3" style="pointer-events: auto;">
                <button data-route="homing" class="btn btn-primary btn-lg loadRoutes"
                    style="font-size: large; min-width: 150px;">
                    <i class="fa fa-home-alt fa-2x"></i> <br>
                    Homing
                </button>
                <button data-route="machineParameters" class="btn btn-primary btn-lg loadRoutes"
                    style="font-size: large; min-width: 150px;">
                    <i class="fa fa-cogs fa-2x"></i> <br>
                    Settings
                </button>

                <button data-route="manualControl" class="btn btn-primary btn-lg loadRoutes"
                    style="font-size: large; min-width: 150px;">
                    <i class="fa fa-user-gear fa-2x"></i> <br>
                    Manaul Control
                </button>
                <button data-route="autoControl" class="btn btn-primary btn-lg loadRoutes"
                    style="font-size: large; min-width: 150px;">
                    <i class="fa fa-gears fa-2x"></i> <br>
                    Auto Control
                </button>
                <a class="btn btn-primary btn-lg" style="font-size: large; min-width: 150px;" data-bs-toggle="modal"
                    href="#inputList">
                    <i class="fa fa-rotate-270 fa-arrow-right-to-bracket fa-2x"></i> <br>
                    Input List
                </a>
                <a class="btn btn-primary btn-lg" style="font-size: large; min-width: 150px;" data-bs-toggle="modal"
                    href="#outputList">
                    <i class="fa fa-rotate-90 fa-arrow-right-from-bracket fa-2x"></i> <br>
                    Output List
                </a>
                <a class="btn btn-primary btn-lg" style="font-size: large; min-width: 150px;" data-bs-toggle="modal"
                    href="#debugLog">
                    <i class="fa fa-list fa-2x"></i> <br>
                    Log
                </a>

                <!-- <button data-route="log" class="btn btn-primary btn-lg loadRoutes" style="font-size: large; min-width: 150px;">
                    <i class="fa fa-list fa-2x"></i> <br>
                    Log
                </button> -->
                <button class="btn btn-primary btn-lg appLogOut" style="font-size: large; min-width: 150px;">
                    <i class="fa fa-sign-out fa-2x"></i> <br>
                    Log Out
                </button>

                <button class="btn btn-primary btn-lg exitBtn nativeChannelBtns"
                    style="font-size: large; min-width: 150px;">
                    <i class="fa fa-times fa-2x"></i> <br>
                    Exit
                </button>

                <div class="dropdown nativeChannelBtns">
                    <button class="btn btn-primary btn-lg dropdown-toggle" data-bs-toggle="dropdown"
                        style="font-size: large; min-width: 150px;">
                        <i class="fa fa-list fa-2x"></i> <br>
                        ...
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item h3 blueTitle nodeProcess" data-node-action="status"
                                href="javascript:;"><i class="fa fa-check-circle"></i> Check Status</a></li>
                        <li><a class="dropdown-item h3 blueTitle nodeProcess" data-node-action="start"
                                href="javascript:;"><i class="fa fa-play-circle"></i> Start Process</a></li>
                        <li><a class="dropdown-item h3 blueTitle nodeProcess" data-node-action="stop" href="javascript:;"><i
                                    class="fa fa-stop-circle"></i> Stop Process</a></li>
                        <li><a class="dropdown-item h3 blueTitle nodeProcess" data-node-action="restart"
                                href="javascript:;"><i class="fa fa-rotate"></i> Restart Process</a></li>
                        <li><a class="dropdown-item h3 blueTitle nodeProcess" data-node-action="logs" href="javascript:;"><i
                                    class="fa fa-list"></i> Process Log</a></li>
                        <li><a class="dropdown-item h3 blueTitle shutdownBtn" href="javascript:;"><i
                                    class="fa fa-power-off"></i> Shutdown System</a></li>
                        <li><a class="dropdown-item h3 blueTitle restartBtn" href="javascript:;"><i
                                    class="fa fa-rotate"></i> Restart System</a></li>
                    </ul>
                </div>

                <!-- shutdownBtn -->
                <!-- <button class="btn btn-primary btn-lg shutdownBtn nativeChannelBtns" style="font-size: large; min-width: 150px;">
                <i class="fa fa-power-off fa-2x"></i> <br>
                Shutdown
            </button> -->

                <!-- restartBtn -->
                <!-- <button class="btn btn-primary btn-lg restartBtn nativeChannelBtns" style="font-size: large; min-width: 150px;">
                <i class="fa fa-rotate fa-2x"></i> <br>
                Restart
            </button> -->
            </div>
        </div>



            <?php
    }
    ?>

</div>
<!-- END #content -->



<?php
if ($isIpc) {
    ?>

    <!-- #modal-dialog -->
    <div class="modal fade" id="princherSafeScreen">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">PRINCHER SAFE</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <?php
                        $list = [
                            '32' => 'E STOP OPERATING PANEL',
                            '33' => 'E stop princher remote',
                            '34' => 'E stop outfeed remote',
                            '39' => 'Phase protector',
                            '13' => 'Barrier proxy',
                            '355' => 'X servo error',
                            '359' => 'Y1 servo error',
                            '364' => 'Y2 servo error',
                            '369' => 'Y3 servo error',
                            '374' => 'Y4 servo error',
                            '65' => 'Marking body up proxy',
                            '42' => 'Princher down proxy',
                            '24' => 'Main hyd motor run',
                            '495' => 'Edge finder declamp proxy',
                            '496' => 'Punch 1 cmd',
                            '497' => 'Punch 2 cmd',
                            '498' => 'Punch 3 cmd',
                            '499' => 'Punch 4 cmd',
                            '500' => 'Marking cmd',
                            '501' => 'Cutting cmd',
                            '502' => 'Punch 1 safety stop',
                            '503' => 'Punch 2 safety stop',
                            '504' => 'Punch 3 safety stop',
                            '505' => 'Punch 4 safety stop',
                            '67' => 'Marking cylinder up proxy',
                            '53' => 'Marking casset 1 proxy',
                            '71' => 'Punch 1 up proxy',
                            '73' => 'Punch 2 up proxy',
                            '75' => 'Punch 3 up proxy',
                            '77' => 'Punch 4 up proxy',
                            '58' => 'Cutting cylinder up proxy',
                            '58' => 'Cutting hold up proxy',
                            '92' => 'In feed 90 pressure ok',
                            '506' => 'In feed 90 proxy',
                            '136' => 'Princher clamp pressure ok',
                        ];

                        $half = ceil(count($list) / 2);
                        $list1 = array_slice($list, 0, $half, true);
                        $list2 = array_slice($list, $half, null, true);

                        echo "<div class='col-auto'>";
                        foreach ($list1 as $id => $name) {
                            echo "<div class='mt-1'>
                                    <button class='plc-btn btn btn-sm'
                                        data-ui-type='button'
                                        data-tag-id='$id'
                                        data-behavior='momentary'
                                        data-indicator-id='$id'
                                        data-on-color='#82c779'
                                        data-off-color='#ff5b57'
                                        data-disable-color='#6c757d'
                                        data-on-label='ON'
                                        data-off-label='OFF'
                                        data-on-confirm=''
                                        indicator-only='true'
                                        data-off-confirm=''></button>
                                    $name
                                </div>";
                        }
                        echo "</div>";
                        echo "<div class='col-auto'>";
                        foreach ($list2 as $id => $name) {
                            echo "<div class='mt-1'>
                                    <button class='plc-btn btn btn-sm'
                                        data-ui-type='button'
                                        data-tag-id='$id'
                                        data-behavior='momentary'
                                        data-indicator-id='$id'
                                        data-on-color='#82c779'
                                        data-off-color='#ff5b57'
                                        data-disable-color='#6c757d'
                                        data-on-label='ON'
                                        data-off-label='OFF'
                                        data-on-confirm=''
                                        indicator-only='true'
                                        data-off-confirm=''></button>
                                    $name
                                </div>";
                        }
                        echo "</div>";
                        ?>
                    </div>
                </div>
                <!-- <div class="modal-footer">
                <a href="javascript:;" class="btn btn-white" data-bs-dismiss="modal">Close</a>
                <a href="javascript:;" class="btn btn-success">Action</a>
            </div> -->
            </div>
        </div>
    </div>

    <div class="modal fade" id="inputList">
        <div class="modal-dialog modal-xl" style="max-width: 98%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">INPUT LIST</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <?php
                        $list = [
                            '32' => 'IX2.0, PB ESTOP 1 OPERATING PENAL',
                            '87' => 'IX3.0, PB ESTOP 2 PRINCHER REMOTE',
                            '34' => 'IX4.0, PB ESTOP 3 OUTFEED REMOTE',
                            '43' => 'IX5.0, M/C SIDE HARD OT E STOP',
                            '48' => 'IX6.0, PRINCHER SIDE HARD OT E STOP',
                            '49' => 'IX7.0, PROXY PRINCHER STACK',
                            '13' => 'IX8.0, PROXY BARRIER',
                            '39' => 'IX9.0, PHASE PROTECTOR GIC',
                            '87' => 'IX2.1, P.S. LOW LUBRICATION',
                            '89' => 'IX3.1, SS A/M',
                            '30' => 'IX4.1, PB AUTO START/STOP',
                            '35' => 'IX5.1, PB HYD MOTOR ON/OFF',
                            '38' => 'IX6.1, PB UP/FWD',
                            '31' => 'IX7.1, PB DOWN/REV',
                            '36' => 'IX8.1, PB OUTFEED U',
                            '37' => 'IX9.1, PB PRINCHER REFERENCE',
                            '90' => 'IX19.0, SS  PRINCHER CLAMP',
                            '91' => 'IX19.1, SS PRINCHER DECLAMP',
                            '24' => 'IX19.2, MAIN MOTOR(20 HP) RUN',
                            '25' => 'IX19.3, MAIN MOTOR(20 HP) TRIP',
                            '26' => 'IX19.4, OIL CIRCULATION MOTOR(3 HP) RUN',
                            '27' => 'IX19.5, OIL CIRCULATION MOTOR(3 HP) TRIP',
                            '20' => 'IX19.6, COOLER MOTOR(3 HP) RUN',
                            '21' => 'IX19.7, COOLER MOTOR(3 HP) TRIP',
                            '22' => 'IX20.0, LUBRICATION MOTOR(0.25 HP) RUN',
                            '23' => 'IX20.1, LUBRICATION MOTOR(0.25 HP) TRIP',
                            '28' => 'IX20.2, OUT FEED MOTOR(3 HP) RUN',
                            '29' => 'IX20.3, OUT FEED MOTOR(3 HP) TRIP',
                            '16' => 'IX20.4, CHAIN FEEDER MOTOR FORWARD RUN',
                            '17' => 'IX20.5, CHAIN FEEDER MOTOR - 1 TRIP',
                            '18' => 'IX20.6, CHAIN FEEDER MOTOR REVERCE RUN',
                            '19' => 'IX20.7, CHAIN FEEDER MOTOR - 2 TRIP',
                            '14' => 'IX22.0, PROXY CHAIN FEEDER 1',
                            '15' => 'IX22.1, PROXY CHAIN FEEDER 2',
                            '50' => 'IX22.2, PROXY PRINCHER U',
                            '42' => 'IX22.3, PROXY PRINCHER D',
                            '47' => 'IX22.4, PROXY PRINCHER SERVO REF',
                            '41' => 'IX22.5, PHOTO PRINCHER ANGLE SLOW',
                            '40' => 'IX22.6, PHOTO PRINCHER ANGLE REF',
                            '51' => 'IX22.7, PROXY 1 IN FEED 0 DEGREE.',
                            '52' => 'IX23.0, PROXY 2 IN FEED 0 DEGREE',
                            '508' => 'IX23.1, PHOTO OUTFEED',
                            '68' => 'IX23.2, PROXY OUT FEED U',
                            '69' => 'IX23.3, PROXY OUTFEED D',
                            '65' => 'IX23.4, PROXY M BODY U',
                            '64' => 'IX23.5, PROXY M BODY D',
                            '53' => 'IX23.6, PROXY CASSATE 1',
                            '54' => 'IX23.7, PROXY CASSATE 2',
                            '55' => 'IX25.0, PROXY CASSATE 3',
                            '56' => 'IX25.1, PROXY CASSATE 4',
                            '67' => 'IX25.2, PROXY M CYL U',
                            '66' => 'IX25.3, PROXY M CYL D',
                            '59' => 'IX25.4, PROXY CUTTING HOLD U',
                            '58' => 'IX25.5, PROXY CUT CYL U',
                            '57' => 'IX25.6, PROXY CUTTING CYL D',
                            '86' => 'IX25.7, P.S. CUT HOLD D',
                            '60' => 'IX26.0, PROXY HOLD 1 U',
                            '79' => 'IX26.1, PROXY SERVO 1 U',
                            '78' => 'IX26.2, PROXY SERVO 1 D',
                            '71' => 'IX26.3, PROXY PUNCH 1 U',
                            '70' => 'IX26.4, PROXY PUNCH 1 D',
                            '61' => 'IX26.5, PROXY HOLD 2 U',
                            '81' => 'IX26.6, PROXY SERVO 2 U',
                            '80' => 'IX26.7, PROXY SERVO 2 D',
                            '73' => 'IX28.0, PROXY PUNCH 2 U',
                            '72' => 'IX28.1, PROXY PUNCH 2 D',
                            '62' => 'IX28.2, PROXY HOLD 3 U',
                            '83' => 'IX28.3, PROXY SERVO 3 U',
                            '82' => 'IX28.4, PROXY SERVO 3 D',
                            '75' => 'IX28.5, PROXY PUNCH 3 U',
                            '74' => 'IX28.6, PROXY PUNCH 3 D',
                            '63' => 'IX28.7, PROXY HOLD 4 U',
                            '85' => 'IX29.0, PROXY SERVO 4 U',
                            '84' => 'IX29.1, PROXY SERVO 4 D',
                            '77' => 'IX29.2, PROXY PUNCH 4 U',
                            '76' => 'IX29.3, PROXY PUNCH 4 D',
                            '88' => 'IX31.4, RETURN LINE FILTER CHOCK',
                            '44' => 'IX31.5, PRINCHER LOW LUB. PROXY',
                            '45' => 'IX31.6, PRINCHER  LUB PUMP ON',
                            '46' => 'IX31.7, PRINCHER LUB PUMP TRIP',
                            '381' => 'IX32.0, PRINCHER HYD. PUMP ON',
                            '509' => 'IX32.1, PRINCHER HYD. PUMP TRIP',
                            '506' => 'IX32.2, PROXY IN FEED 90',
                            '510' => 'IX32.3, EDGE CLAMP PRESSURE SWITCH',
                            '511' => 'IX32.4, EDGE FIND PHOTO',
                            '512' => 'IX32.5, EDGE FIND FWD LIMIT',
                            '513' => 'IX32.6, EDGE FIND RVS LIMIT',
                            '495' => 'IX32.7, EDGE DECLAMP PROXY',
                        ];

                        $half = ceil(count($list) / 2);
                        $finalList = [];
                        $finalList[] = array_slice($list, 0, $half, true);
                        $finalList[] = array_slice($list, $half, null, true);

                        foreach ($finalList as $list) {
                            echo "<div class='col-6'>";
                            foreach ($list as $id => $name) {
                                echo "<div class='mt-1 d-flex align-items-center text-nowrap'>
                                    <button class='plc-btn btn btn-sm flex-shrink-0 me-1'
                                        data-ui-type='button'
                                        data-tag-id='$id'
                                        data-behavior='momentary'
                                        data-indicator-id='$id'
                                        data-on-color='#82c779'
                                        data-off-color='#ff5b57'
                                        data-disable-color='#6c757d'
                                        data-on-label='ON'
                                        data-off-label='OFF'
                                        data-on-confirm=''
                                        indicator-only='true'
                                        data-off-confirm=''></button>
                                    <span class='ms-1' style='font-size: 13px;'>$name</span>
                                </div>";
                            }
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
                <!-- <div class="modal-footer">
                <a href="javascript:;" class="btn btn-white" data-bs-dismiss="modal">Close</a>
                <a href="javascript:;" class="btn btn-success">Action</a>
            </div> -->
            </div>
        </div>
    </div>

    <div class="modal fade" id="outputList">
        <div class="modal-dialog modal-xl" style="max-width: 98%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">OUTPUT LIST</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <?php
                        $list = [
                            '142' => 'QX0.0, MAIN MOTOR(25 HP)',
                            '143' => 'QX1.0, OIL CIRCULATION MOTOR(3 HP)',
                            '139' => 'QX2.0, COOLER MOTOR(3 HP)',
                            '140' => 'QX3.0, LUBRICATION MOTOR(0.25 HP)',
                            '137' => 'QX5.0, CHAIN FEEDER MOTOR F(1 HP)',
                            '138' => 'QX6.0, CHAIN FEEDER MOTOR R(1 HP)',
                            '144' => 'QX7.0, PRINCHER LUB PUMP(1- PHASE, 0.5A)',
                            '507' => 'QX0.1, PRINCHER HYD. MOTOR ',
                            '145' => 'QX1.1, PUNCH - 1 SERVO  BREAK OUTPUT',
                            '146' => 'QX2.1, PUNCH - 2 SERVO  BREAK OUTPUT',
                            '147' => 'QX3.1, PUNCH - 3 SERVO  BREAK OUTPUT',
                            '148' => 'QX4.1, PUNCH - 4 SERVO  BREAK OUTPUT',
                            '149' => 'QX5.1, PUNCH - 5 SERVO  BREAK OUTPUT',
                            '150' => 'QX6.1, PUNCH - 6 SERVO  BREAK OUTPUT',
                            '189' => 'QX7.1, TL RED',
                            '190' => 'QX8.0, TL YELLOW',
                            '187' => 'QX8.1, TL GREEN',
                            '188' => 'QX8.2, TL HOOTER',
                            '141' => 'QX8.3, INDI AUTO CYCLE ON',
                            '157' => 'QX8.4, SV HIGH PRESSURE',
                            '168' => 'QX8.5, SV LOW PRESSURE',
                            '178' => 'QX8.6, SV PRINCH U',
                            '176' => 'QX8.7, SV PRINCH D',
                            '175' => 'QX9.0, SV PRINCH CLAMP',
                            '177' => 'QX9.1, SV PRINCH DECLAMP',
                            '166' => 'QX9.2, SV IN FEED 0',
                            '167' => 'QX9.3, SV IN FEED 90',
                            '174' => 'QX9.4, SV OUT FEED U',
                            '173' => 'QX9.5, SV OUT FEED D',
                            '170' => 'QX9.6, SV M BODY U',
                            '169' => 'QX9.7, SV M BODY D',
                            '151' => 'QX10.0, SV CASSAT D',
                            '152' => 'QX10.1, SV CASSAT U',
                            '172' => 'QX10.2, SV M CYL U',
                            '171' => 'QX10.3, SV M CYL D',
                            '154' => 'QX10.4, SV CUT CYL U',
                            '153' => 'QX10.5, SV CUT CYL D',
                            '156' => 'QX10.6, SV CUT HOLD U',
                            '155' => 'QX10.7, SV CUT HOLD D',
                            '159' => 'QX11.0, SV HOLD 1 U',
                            '158' => 'QX11.1, SV HOLD 1 D',
                            '180' => 'QX11.2, SV PUNCH 1 U',
                            '179' => 'QX11.3, SV PUNCH 1 D',
                            '161' => 'QX11.4, SV HOLD 2 U',
                            '160' => 'QX11.5, SV HOLD 2 D',
                            '182' => 'QX11.6, SV PUNCH 2 U',
                            '181' => 'QX11.7, SV PUNCH 2 D',
                            '163' => 'QX12.0, SV HOLD 3 U',
                            '162' => 'QX12.1, SV HOLD 3 D',
                            '184' => 'QX12.2, SV PUNCH 3 U',
                            '183' => 'QX12.3, SV PUNCH 3 D',
                            '165' => 'QX12.4, SV HOLD 4 U',
                            '164' => 'QX12.5, SV HOLD 4 D',
                            '186' => 'QX12.6, SV PUNCH 4 U',
                            '185' => 'QX12.7, SV PUNCH 4 D',
                            '393' => 'QX14.1, EDGE FIND CLAMP',
                            '395' => 'QX14.2, EDGE FIND DECLAMP',
                        ];

                        $half = ceil(count($list) / 2);
                        $finalList = [];
                        $finalList[] = array_slice($list, 0, $half, true);
                        $finalList[] = array_slice($list, $half, null, true);

                        foreach ($finalList as $list) {
                            echo "<div class='col-6'>";
                            foreach ($list as $id => $name) {
                                echo "<div class='mt-1 d-flex align-items-center text-nowrap'>
                                    <button class='plc-btn btn btn-sm flex-shrink-0 me-1'
                                        data-ui-type='button'
                                        data-tag-id='$id'
                                        data-behavior='momentary'
                                        data-indicator-id='$id'
                                        data-on-color='#82c779'
                                        data-off-color='#ff5b57'
                                        data-disable-color='#6c757d'
                                        data-on-label='ON'
                                        data-off-label='OFF'
                                        data-on-confirm=''
                                        indicator-only='true'
                                        data-off-confirm=''></button>
                                    <span class='ms-1' style='font-size: 13px;'>$name</span>
                                </div>";
                            }
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
                <!-- <div class="modal-footer">
                <a href="javascript:;" class="btn btn-white" data-bs-dismiss="modal">Close</a>
                <a href="javascript:;" class="btn btn-success">Action</a>
            </div> -->
            </div>
        </div>
    </div>

    <div class="modal fade" id="debugLog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Debug Log</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a href="#autoCycleLog" data-bs-toggle="tab" class="nav-link active">Auto Cycle
                                Log</a></li>
                        <li class="nav-item"><a href="#tagWriteLog" data-bs-toggle="tab" class="nav-link">Tag Write Log</a>
                        </li>
                        <li class="nav-item"><a href="#liveTagView" data-bs-toggle="tab" class="nav-link">Live Tag View</a>
                        </li>
                    </ul>

                    <div class="tab-content panel p-3 rounded">
                        <div class="tab-pane fade active show" id="autoCycleLog">

                        </div>
                        <div class="tab-pane fade" id="tagWriteLog">

                        </div>
                        <div class="tab-pane fade" id="liveTagView">

                        </div>
                    </div>
                </div>
                <!-- <div class="modal-footer">
                <a href="javascript:;" class="btn btn-white" data-bs-dismiss="modal">Close</a>
                <a href="javascript:;" class="btn btn-success">Action</a>
            </div> -->
            </div>
        </div>
    </div>

    <?php
}
?>