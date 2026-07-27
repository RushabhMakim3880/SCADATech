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
        background-color: #b3dffc;
    }

    .nextopr-row {
        background-color: #bdf5b5;
    }

    .data-row {
        cursor: pointer;
    }

    /* .data-row:hover {
        background-color: #b3dffc;
    } */

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

    .dragHandle {
        cursor: grab;
        user-select: none;
        padding: 0 .25rem;
        display: inline-block;
    }

    .sortable-ghost {
        opacity: .6;
    }
</style>
<!-- BEGIN #content -->
<div id="content" class="app-content">

    <div style="position: fixed;top:50px;left:10px;">
        <strong>ID:</strong> <?= getenv("scadaId"); ?></br>
    </div>

    <div class="">
        <img src="<?= base_url('assets/img/DetanicsLogo.png') ?>" alt="OpMaster Image" class="centerLogo"
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

        <div class="position-fixed bottom-0 w-100 text-center pb-2" style="pointer-events: none;left:0px;">
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
                    Manual Control
                </button>
                <button data-route="programPrepare" class="btn btn-primary btn-lg loadRoutes"
                    style="font-size: large; min-width: 150px;">
                    <i class="fa fa-file fa-2x"></i> <br>
                    Program
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
                            '32' => ['value' => 'P1_SAFETY_STOP', 'isReverse' => true],
                            '33' => ['value' => 'P2_SAFETY_STOP', 'isReverse' => true],
                            '34' => ['value' => 'P3_SAFETY_STOP', 'isReverse' => true],
                            '39' => ['value' => 'P4_SAFETY_STOP', 'isReverse' => true],
                            '13' => ['value' => 'P5_SAFETY_STOP', 'isReverse' => true],
                            '355' => ['value' => 'P6_SAFETY_STOP', 'isReverse' => true],
                            '359' => ['value' => 'I_PROXY_PUNCH_1_U', 'isReverse' => false],
                            '364' => ['value' => 'I_PROXY_PUNCH_2_U', 'isReverse' => false],
                            '369' => ['value' => 'I_PROXY_PUNCH_3_U', 'isReverse' => false],
                            '374' => ['value' => 'I_PROXY_PUNCH_4_U', 'isReverse' => false],
                            '65' => ['value' => 'I_PROXY_PUNCH_5_U', 'isReverse' => false],
                            '42' => ['value' => 'I_PROXY_PUNCH_6_U', 'isReverse' => false],
                            '24' => ['value' => 'I_PROXY_MARKING_CYL_U', 'isReverse' => false], //ok
                            '495' => ['value' => 'I_PROXY_CASSATE_1', 'isReverse' => false],
                            '496' => ['value' => 'I_PROXY_CUTCYL_U', 'isReverse' => false],
                            '497' => ['value' => 'I_PROXY_CUTHOLD_U', 'isReverse' => false],
                            '498' => ['value' => 'I_PROXY_MARKING_BODY_U', 'isReverse' => false],
                            '499' => ['value' => 'MARKING_SAFETY_STOP', 'isReverse' => true],
                            '500' => ['value' => 'I_PROXY_PRINCHER_D', 'isReverse' => false],
                            '501' => ['value' => 'I_MAIN_MOTOR_RUN', 'isReverse' => false],
                            '502' => ['value' => 'IN_FEED_90_OK', 'isReverse' => false],
                            '503' => ['value' => 'MARKING_CMD', 'isReverse' => true],
                            '504' => ['value' => 'CUTTING_CMD', 'isReverse' => true],
                            '505' => ['value' => 'PRINCHER_CLAMP_PRESSURE_OK', 'isReverse' => false],
                        ];

                        $half = ceil(count($list) / 2);
                        $list1 = array_slice($list, 0, $half, true);
                        $list2 = array_slice($list, $half, null, true);

                        echo "<div class='col-auto'>";
                        foreach ($list1 as $id => $item) {
                            $name = $item['value'];
                            $onColor = "#82c779";
                            $offColor = "#ff5b57";

                            if ($item['isReverse']) {
                                $onColor = "#ff5b57";
                                $offColor = "#82c779";
                            }

                            echo "<div class='mt-1'>
                                    <button class='plc-btn btn btn-sm'
                                        data-ui-type='button'
                                        data-tag-id='$id'
                                        data-behavior='momentary'
                                        data-indicator-id='$id'
                                        data-on-color='$onColor'
                                        data-off-color='$offColor'
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
                        foreach ($list2 as $id => $item) {
                            $name = $item['value'];
                            $onColor = "#82c779";
                            $offColor = "#ff5b57";

                            if ($item['isReverse']) {
                                $onColor = "#ff5b57";
                                $offColor = "#82c779";
                            }

                            echo "<div class='mt-1'>
                                    <button class='plc-btn btn btn-sm'
                                        data-ui-type='button'
                                        data-tag-id='$id'
                                        data-behavior='momentary'
                                        data-indicator-id='$id'
                                        data-on-color='$onColor'
                                        data-off-color='$offColor'
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">INPUT LIST</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <?php
                        $list = [
                            '32' => ['value' => 'IX2.0, PB ESTOP 1 OPERATING PENAL', 'isReverse' => false],
                            '87' => ['value' => 'IX3.0, PB ESTOP 2 PRINCHER REMOTE', 'isReverse' => false],
                            '34' => ['value' => 'IX4.0, PB ESTOP 3 OUTFEED REMOTE', 'isReverse' => false],
                            '43' => ['value' => 'IX5.0, M/C SIDE HARD OT E STOP', 'isReverse' => false],
                            '48' => ['value' => 'IX6.0, PRINCHER SIDE HARD OT E STOP', 'isReverse' => false],
                            '49' => ['value' => 'IX7.0, PROXY PRINCHER STACK', 'isReverse' => false],
                            '13' => ['value' => 'IX8.0, PROXY BARRIER', 'isReverse' => false],
                            '39' => ['value' => 'IX9.0, PHASE PROTECTOR GIC', 'isReverse' => false],
                            '87' => ['value' => 'IX2.1, P.S. LOW LUBRICATION', 'isReverse' => false],
                            '89' => ['value' => 'IX3.1, SS A/M', 'isReverse' => false],
                            '30' => ['value' => 'IX4.1, PB AUTO START/STOP', 'isReverse' => false],
                            '35' => ['value' => 'IX5.1, PB HYD MOTOR ON/OFF', 'isReverse' => false],
                            '38' => ['value' => 'IX6.1, PB UP/FWD', 'isReverse' => false],
                            '31' => ['value' => 'IX7.1, PB DOWN/REV', 'isReverse' => false],
                            '36' => ['value' => 'IX8.1, PB OUTFEED U', 'isReverse' => false],
                            '37' => ['value' => 'IX9.1, PB PRINCHER REFERENCE', 'isReverse' => false],
                            '90' => ['value' => 'IX19.0, SS  PRINCHER CLAMP', 'isReverse' => false],
                            '91' => ['value' => 'IX19.1, SS PRINCHER DECLAMP', 'isReverse' => false],
                            '24' => ['value' => 'IX19.2, MAIN MOTOR(20 HP) RUN', 'isReverse' => false],
                            '25' => ['value' => 'IX19.3, MAIN MOTOR(20 HP) TRIP', 'isReverse' => false],
                            '26' => ['value' => 'IX19.4, OIL CIRCULATION MOTOR(3 HP) RUN', 'isReverse' => false],
                            '27' => ['value' => 'IX19.5, OIL CIRCULATION MOTOR(3 HP) TRIP', 'isReverse' => false],
                            '20' => ['value' => 'IX19.6, COOLER MOTOR(3 HP) RUN', 'isReverse' => false],
                            '21' => ['value' => 'IX19.7, COOLER MOTOR(3 HP) TRIP', 'isReverse' => false],
                            '22' => ['value' => 'IX20.0, LUBRICATION MOTOR(0.25 HP) RUN', 'isReverse' => false],
                            '23' => ['value' => 'IX20.1, LUBRICATION MOTOR(0.25 HP) TRIP', 'isReverse' => false],
                            '28' => ['value' => 'IX20.2, OUT FEED MOTOR(3 HP) RUN', 'isReverse' => false],
                            '29' => ['value' => 'IX20.3, OUT FEED MOTOR(3 HP) TRIP', 'isReverse' => false],
                            '16' => ['value' => 'IX20.4, CHAIN FEEDER MOTOR FORWARD RUN', 'isReverse' => false],
                            '17' => ['value' => 'IX20.5, CHAIN FEEDER MOTOR - 1 TRIP', 'isReverse' => false],
                            '18' => ['value' => 'IX20.6, CHAIN FEEDER MOTOR REVERCE RUN', 'isReverse' => false],
                            '19' => ['value' => 'IX20.7, CHAIN FEEDER MOTOR - 2 TRIP', 'isReverse' => false],
                            '14' => ['value' => 'IX22.0, PROXY CHAIN FEEDER 1', 'isReverse' => false],
                            '15' => ['value' => 'IX22.1, PROXY CHAIN FEEDER 2', 'isReverse' => false],
                            '50' => ['value' => 'IX22.2, PROXY PRINCHER U', 'isReverse' => false],
                            '42' => ['value' => 'IX22.3, PROXY PRINCHER D', 'isReverse' => false],
                            '47' => ['value' => 'IX22.4, PROXY PRINCHER SERVO REF', 'isReverse' => false],
                            '41' => ['value' => 'IX22.5, PHOTO PRINCHER ANGLE SLOW', 'isReverse' => false],
                            '40' => ['value' => 'IX22.6, PHOTO PRINCHER ANGLE REF', 'isReverse' => false],
                            '51' => ['value' => 'IX22.7, PROXY 1 IN FEED 0 DEGREE.', 'isReverse' => false],
                            '52' => ['value' => 'IX23.0, PROXY 2 IN FEED 0 DEGREE', 'isReverse' => false],
                            '508' => ['value' => 'IX23.1, PHOTO OUTFEED', 'isReverse' => false],
                            '68' => ['value' => 'IX23.2, PROXY OUT FEED U', 'isReverse' => false],
                            '69' => ['value' => 'IX23.3, PROXY OUTFEED D', 'isReverse' => false],
                            '65' => ['value' => 'IX23.4, PROXY M BODY U', 'isReverse' => false],
                            '64' => ['value' => 'IX23.5, PROXY M BODY D', 'isReverse' => false],
                            '53' => ['value' => 'IX23.6, PROXY CASSATE 1', 'isReverse' => false],
                            '54' => ['value' => 'IX23.7, PROXY CASSATE 2', 'isReverse' => false],
                            '55' => ['value' => 'IX25.0, PROXY CASSATE 3', 'isReverse' => false],
                            '56' => ['value' => 'IX25.1, PROXY CASSATE 4', 'isReverse' => false],
                            '67' => ['value' => 'IX25.2, PROXY M CYL U', 'isReverse' => false],
                            '66' => ['value' => 'IX25.3, PROXY M CYL D', 'isReverse' => false],
                            '59' => ['value' => 'IX25.4, PROXY CUTTING HOLD U', 'isReverse' => false],
                            '58' => ['value' => 'IX25.5, PROXY CUT CYL U', 'isReverse' => false],
                            '57' => ['value' => 'IX25.6, PROXY CUTTING CYL D', 'isReverse' => false],
                            '86' => ['value' => 'IX25.7, P.S. CUT HOLD D', 'isReverse' => false],
                            '60' => ['value' => 'IX26.0, PROXY HOLD 1 U', 'isReverse' => false],
                            '79' => ['value' => 'IX26.1, PROXY SERVO 1 U', 'isReverse' => false],
                            '78' => ['value' => 'IX26.2, PROXY SERVO 1 D', 'isReverse' => false],
                            '71' => ['value' => 'IX26.3, PROXY PUNCH 1 U', 'isReverse' => false],
                            '70' => ['value' => 'IX26.4, PROXY PUNCH 1 D', 'isReverse' => false],
                            '61' => ['value' => 'IX26.5, PROXY HOLD 2 U', 'isReverse' => false],
                            '81' => ['value' => 'IX26.6, PROXY SERVO 2 U', 'isReverse' => false],
                            '80' => ['value' => 'IX26.7, PROXY SERVO 2 D', 'isReverse' => false],
                            '73' => ['value' => 'IX28.0, PROXY PUNCH 2 U', 'isReverse' => false],
                            '72' => ['value' => 'IX28.1, PROXY PUNCH 2 D', 'isReverse' => false],
                            '62' => ['value' => 'IX28.2, PROXY HOLD 3 U', 'isReverse' => false],
                            '83' => ['value' => 'IX28.3, PROXY SERVO 3 U', 'isReverse' => false],
                            '82' => ['value' => 'IX28.4, PROXY SERVO 3 D', 'isReverse' => false],
                            '75' => ['value' => 'IX28.5, PROXY PUNCH 3 U', 'isReverse' => false],
                            '74' => ['value' => 'IX28.6, PROXY PUNCH 3 D', 'isReverse' => false],
                            '63' => ['value' => 'IX28.7, PROXY HOLD 4 U', 'isReverse' => false],
                            '85' => ['value' => 'IX29.0, PROXY SERVO 4 U', 'isReverse' => false],
                            '84' => ['value' => 'IX29.1, PROXY SERVO 4 D', 'isReverse' => false],
                            '77' => ['value' => 'IX29.2, PROXY PUNCH 4 U', 'isReverse' => false],
                            '76' => ['value' => 'IX29.3, PROXY PUNCH 4 D', 'isReverse' => false],
                            '88' => ['value' => 'IX31.4, RETURN LINE FILTER CHOCK', 'isReverse' => false],
                            '44' => ['value' => 'IX31.5, PRINCHER LOW LUB. PROXY', 'isReverse' => false],
                            '45' => ['value' => 'IX31.6, PRINCHER  LUB PUMP ON', 'isReverse' => false],
                            '46' => ['value' => 'IX31.7, PRINCHER LUB PUMP TRIP', 'isReverse' => false],
                            '381' => ['value' => 'IX32.0, PRINCHER HYD. PUMP ON', 'isReverse' => false],
                            '509' => ['value' => 'IX32.1, PRINCHER HYD. PUMP TRIP', 'isReverse' => false],
                            '506' => ['value' => 'IX32.2, PROXY IN FEED 90', 'isReverse' => false],
                            '510' => ['value' => 'IX32.3, EDGE CLAMP PRESSURE SWITCH', 'isReverse' => false],
                            '511' => ['value' => 'IX32.4, EDGE FIND PHOTO', 'isReverse' => false],
                            '512' => ['value' => 'IX32.5, EDGE FIND FWD LIMIT', 'isReverse' => false],
                            '513' => ['value' => 'IX32.6, EDGE FIND RVS LIMIT', 'isReverse' => false],
                            '495' => ['value' => 'IX32.7, EDGE DECLAMP PROXY', 'isReverse' => false],
                        ];

                        $third = ceil(count($list) / 3);
                        $finalList = [];
                        $finalList[] = array_slice($list, 0, $third, true);
                        $finalList[] = array_slice($list, $third, $third, true);
                        $finalList[] = array_slice($list, $third * 2, null, true);

                        foreach ($finalList as $list) {
                            echo "<div class='col-4'>";
                            foreach ($list as $id => $item) {
                                $name = $item['value'];
                                $onColor = "#82c779";
                                $offColor = "#ff5b57";

                                if ($item['isReverse']) {
                                    $onColor = "#ff5b57";
                                    $offColor = "#82c779";
                                }

                                echo "<div class='mt-1'>
                                    <button class='plc-btn btn btn-sm'
                                        data-ui-type='button'
                                        data-tag-id='$id'
                                        data-behavior='momentary'
                                        data-indicator-id='$id'
                                        data-on-color='$onColor'
                                        data-off-color='$offColor'
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">OUTPUT LIST</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <?php
                        $list = [
                            '142' => ['value' => 'QX0.0, MAIN MOTOR(25 HP)', 'isReverse' => false],
                            '143' => ['value' => 'QX1.0, OIL CIRCULATION MOTOR(3 HP)', 'isReverse' => false],
                            '139' => ['value' => 'QX2.0, COOLER MOTOR(3 HP)', 'isReverse' => false],
                            '140' => ['value' => 'QX3.0, LUBRICATION MOTOR(0.25 HP)', 'isReverse' => false],
                            '137' => ['value' => 'QX5.0, CHAIN FEEDER MOTOR F(1 HP)', 'isReverse' => false],
                            '138' => ['value' => 'QX6.0, CHAIN FEEDER MOTOR R(1 HP)', 'isReverse' => false],
                            '144' => ['value' => 'QX7.0, PRINCHER LUB PUMP(1- PHASE, 0.5A)', 'isReverse' => false],
                            '507' => ['value' => 'QX0.1, PRINCHER HYD. MOTOR ', 'isReverse' => false],
                            '145' => ['value' => 'QX1.1, PUNCH - 1 SERVO  BREAK OUTPUT', 'isReverse' => false],
                            '146' => ['value' => 'QX2.1, PUNCH - 2 SERVO  BREAK OUTPUT', 'isReverse' => false],
                            '147' => ['value' => 'QX3.1, PUNCH - 3 SERVO  BREAK OUTPUT', 'isReverse' => false],
                            '148' => ['value' => 'QX4.1, PUNCH - 4 SERVO  BREAK OUTPUT', 'isReverse' => false],
                            '149' => ['value' => 'QX5.1, PUNCH - 5 SERVO  BREAK OUTPUT', 'isReverse' => false],
                            '150' => ['value' => 'QX6.1, PUNCH - 6 SERVO  BREAK OUTPUT', 'isReverse' => false],
                            '189' => ['value' => 'QX7.1, TL RED', 'isReverse' => false],
                            '190' => ['value' => 'QX8.0, TL YELLOW', 'isReverse' => false],
                            '187' => ['value' => 'QX8.1, TL GREEN', 'isReverse' => false],
                            '188' => ['value' => 'QX8.2, TL HOOTER', 'isReverse' => false],
                            '141' => ['value' => 'QX8.3, INDI AUTO CYCLE ON', 'isReverse' => false],
                            '157' => ['value' => 'QX8.4, SV HIGH PRESSURE', 'isReverse' => false],
                            '168' => ['value' => 'QX8.5, SV LOW PRESSURE', 'isReverse' => false],
                            '178' => ['value' => 'QX8.6, SV PRINCH U', 'isReverse' => false],
                            '176' => ['value' => 'QX8.7, SV PRINCH D', 'isReverse' => false],
                            '175' => ['value' => 'QX9.0, SV PRINCH CLAMP', 'isReverse' => false],
                            '177' => ['value' => 'QX9.1, SV PRINCH DECLAMP', 'isReverse' => false],
                            '166' => ['value' => 'QX9.2, SV IN FEED 0', 'isReverse' => false],
                            '167' => ['value' => 'QX9.3, SV IN FEED 90', 'isReverse' => false],
                            '174' => ['value' => 'QX9.4, SV OUT FEED U', 'isReverse' => false],
                            '173' => ['value' => 'QX9.5, SV OUT FEED D', 'isReverse' => false],
                            '170' => ['value' => 'QX9.6, SV M BODY U', 'isReverse' => false],
                            '169' => ['value' => 'QX9.7, SV M BODY D', 'isReverse' => false],
                            '151' => ['value' => 'QX10.0, SV CASSAT D', 'isReverse' => false],
                            '152' => ['value' => 'QX10.1, SV CASSAT U', 'isReverse' => false],
                            '172' => ['value' => 'QX10.2, SV M CYL U', 'isReverse' => false],
                            '171' => ['value' => 'QX10.3, SV M CYL D', 'isReverse' => false],
                            '154' => ['value' => 'QX10.4, SV CUT CYL U', 'isReverse' => false],
                            '153' => ['value' => 'QX10.5, SV CUT CYL D', 'isReverse' => false],
                            '156' => ['value' => 'QX10.6, SV CUT HOLD U', 'isReverse' => false],
                            '155' => ['value' => 'QX10.7, SV CUT HOLD D', 'isReverse' => false],
                            '159' => ['value' => 'QX11.0, SV HOLD 1 U', 'isReverse' => false],
                            '158' => ['value' => 'QX11.1, SV HOLD 1 D', 'isReverse' => false],
                            '180' => ['value' => 'QX11.2, SV PUNCH 1 U', 'isReverse' => false],
                            '179' => ['value' => 'QX11.3, SV PUNCH 1 D', 'isReverse' => false],
                            '161' => ['value' => 'QX11.4, SV HOLD 2 U', 'isReverse' => false],
                            '160' => ['value' => 'QX11.5, SV HOLD 2 D', 'isReverse' => false],
                            '182' => ['value' => 'QX11.6, SV PUNCH 2 U', 'isReverse' => false],
                            '181' => ['value' => 'QX11.7, SV PUNCH 2 D', 'isReverse' => false],
                            '163' => ['value' => 'QX12.0, SV HOLD 3 U', 'isReverse' => false],
                            '162' => ['value' => 'QX12.1, SV HOLD 3 D', 'isReverse' => false],
                            '184' => ['value' => 'QX12.2, SV PUNCH 3 U', 'isReverse' => false],
                            '183' => ['value' => 'QX12.3, SV PUNCH 3 D', 'isReverse' => false],
                            '165' => ['value' => 'QX12.4, SV HOLD 4 U', 'isReverse' => false],
                            '164' => ['value' => 'QX12.5, SV HOLD 4 D', 'isReverse' => false],
                            '186' => ['value' => 'QX12.6, SV PUNCH 4 U', 'isReverse' => false],
                            '185' => ['value' => 'QX12.7, SV PUNCH 4 D', 'isReverse' => false],
                            '393' => ['value' => 'QX14.1, EDGE FIND CLAMP', 'isReverse' => false],
                            '395' => ['value' => 'QX14.2, EDGE FIND DECLAMP', 'isReverse' => false],
                        ];

                        $third = ceil(count($list) / 3);
                        $finalList = [];
                        $finalList[] = array_slice($list, 0, $third, true);
                        $finalList[] = array_slice($list, $third, $third, true);
                        $finalList[] = array_slice($list, $third * 2, null, true);

                        foreach ($finalList as $list) {
                            echo "<div class='col-4'>";
                            foreach ($list as $id => $item) {
                                $name = $item['value'];
                                $onColor = "#82c779";
                                $offColor = "#ff5b57";

                                if ($item['isReverse']) {
                                    $onColor = "#ff5b57";
                                    $offColor = "#82c779";
                                }

                                echo "<div class='mt-1'>
                                    <button class='plc-btn btn btn-sm'
                                        data-ui-type='button'
                                        data-tag-id='$id'
                                        data-behavior='momentary'
                                        data-indicator-id='$id'
                                        data-on-color='$onColor'
                                        data-off-color='$offColor'
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

<script>
    disAllowedTags = <?php echo json_encode($disAllowedTags); ?>;
</script>