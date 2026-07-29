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
                            '114' => ['value' => 'X1, I_PB_ESTOP_1_OPERATING_PENAL', 'isReverse' => false],
                            '115' => ['value' => 'X2, I_PB_ESTOP_2_PRINCHER_REMOTE', 'isReverse' => false],
                            '116' => ['value' => 'X3, I_PB_ESTOP_3_OUTFEED_REMOTE', 'isReverse' => false],
                            '121' => ['value' => 'X4, I_PHASE_PROTECTOR_GIC', 'isReverse' => false],
                            '107' => ['value' => 'X5, I_MAIN_MOTOR_RUN', 'isReverse' => false],
                            '108' => ['value' => 'X6, I_MAIN_MOTOR_TRIP', 'isReverse' => false],
                            '99'  => ['value' => 'X7, I_CHAIN_FEEDER_MOTOR_FWD_RUN', 'isReverse' => false],
                            '100' => ['value' => 'IX24.0, I_CHAIN_FEEDER_MOTOR_REV_RUN', 'isReverse' => false],
                            '97'  => ['value' => 'IX24.1, I_CHAIN_FEEDER_MOTOR_1_TRIP', 'isReverse' => false],
                            '98'  => ['value' => 'IX24.2, I_CHAIN_FEEDER_MOTOR_2_TRIP', 'isReverse' => false],
                            '109' => ['value' => 'IX24.3, I_OIL_CIRCULATION_MOTOR_RUN', 'isReverse' => false],
                            '110' => ['value' => 'IX24.4, I_OIL_CIRCULATION_MOTOR_TRIP', 'isReverse' => false],
                            '101' => ['value' => 'IX24.5, I_COOLER_FAN_RUN', 'isReverse' => false],
                            '102' => ['value' => 'IX24.6, I_COOLER_FAN_TRIP', 'isReverse' => false],
                            '105' => ['value' => 'IX24.7, I_LUBRICATION_MOTOR_RUN', 'isReverse' => false],
                            '106' => ['value' => 'IX25.0, I_LUBRICATION_MOTOR_TRIP', 'isReverse' => false],
                            '126' => ['value' => 'IX25.1, I_PRINCHER_LUB_PUMP_RUN', 'isReverse' => false],
                            '127' => ['value' => 'IX25.2, I_PRINCHER_LUB_PUMP_TRIP', 'isReverse' => false],
                            '124' => ['value' => 'IX25.3, I_PRINCHER_HYD_MOTOR_RUN', 'isReverse' => false],
                            '125' => ['value' => 'IX25.4, I_PRINCHER_HYD_MOTOR_TRIP', 'isReverse' => false],
                            '117' => ['value' => 'IX25.5, I_PB_HYD_MOTOR_ON_OFF', 'isReverse' => false],
                            '193' => ['value' => 'IX25.6, I_SS_A_M', 'isReverse' => false],
                            '119' => ['value' => 'IX25.7, I_PB_PRINCHER_REFERENCE', 'isReverse' => false],
                            '112' => ['value' => 'IX26.0, I_PB_AUTO_START_STOP', 'isReverse' => false],
                            '120' => ['value' => 'IX26.1, I_PB_UP_FWD', 'isReverse' => false],
                            '113' => ['value' => 'IX26.2, I_PB_DOWN_REV', 'isReverse' => false],
                            '111' => ['value' => 'IX26.3, I_PB_ALARM_RESET', 'isReverse' => false],
                            '154' => ['value' => 'IX26.4, I_PROXY_LOW_LUB_HEAD', 'isReverse' => false],
                            '192' => ['value' => 'IX26.5, I_RETURN_LINE_FILTER_CHOCK', 'isReverse' => false],
                            '141' => ['value' => 'IX26.6, I_PROXY_CHAIN_FEEDER1', 'isReverse' => false],
                            '142' => ['value' => 'IX26.7, I_PROXY_CHAIN_FEEDER2', 'isReverse' => false],
                            '129' => ['value' => 'IX27.0, I_PROXY_1_IN_FEED_0_DEGREE', 'isReverse' => false],
                            '131' => ['value' => 'IX27.1, I_PROXY_2_IN_FEED_0_DEGREE', 'isReverse' => false],
                            '130' => ['value' => 'IX27.2, I_PROXY_1_IN_FEED_90', 'isReverse' => false],
                            '132' => ['value' => 'IX27.3, I_PROXY_2_IN_FEED_90', 'isReverse' => false],
                            '133' => ['value' => 'IX27.4, I_PROXY_6_METER', 'isReverse' => false],
                            '135' => ['value' => 'IX27.5, I_PROXY_ANGLE_SLOW', 'isReverse' => false],
                            '134' => ['value' => 'IX27.6, I_PROXY_ANGLE_REF', 'isReverse' => false],
                            '123' => ['value' => 'IX27.7, I_PRINCHER_FWD_HARD_OT', 'isReverse' => false],
                            '165' => ['value' => 'IX28.0, I_PROXY_PRINCHER_U', 'isReverse' => false],
                            '162' => ['value' => 'IX28.1, I_PROXY_PRINCHER_D', 'isReverse' => false],
                            '164' => ['value' => 'IX28.2, I_PROXY_PRINCHER_STRICK', 'isReverse' => false],
                            '155' => ['value' => 'IX28.3, I_PROXY_LOW_LUB_PRINCHER', 'isReverse' => false],
                            '163' => ['value' => 'IX28.4, I_PROXY_PRINCHER_REF', 'isReverse' => false],
                            '128' => ['value' => 'IX28.5, I_PRINCHER_REV_HARD_OT', 'isReverse' => false],
                            '194' => ['value' => 'IX28.6, I_SS_PRINCHER_CLAMP', 'isReverse' => false],
                            '195' => ['value' => 'IX28.7, I_SS_PRINCHER_DECLAMP', 'isReverse' => false],
                            '118' => ['value' => 'IX29.0, I_PB_OUTFEED_U', 'isReverse' => false],
                            '161' => ['value' => 'IX29.1, I_PROXY_OUTFEED_U', 'isReverse' => false],
                            '160' => ['value' => 'IX29.2, I_PROXY_OUTFEED_D', 'isReverse' => false],
                            '136' => ['value' => 'IX29.3, I_PROXY_BARRIER', 'isReverse' => false],
                            '157' => ['value' => 'IX29.4, I_PROXY_MARKING_BODY_U', 'isReverse' => false],
                            '156' => ['value' => 'IX29.5, I_PROXY_MARKING_BODY_D', 'isReverse' => false],
                            '137' => ['value' => 'IX29.6, I_PROXY_CASSATE_1', 'isReverse' => false],
                            '138' => ['value' => 'IX29.7, I_PROXY_CASSATE_2', 'isReverse' => false],
                            '139' => ['value' => 'IX30.0, I_PROXY_CASSATE_3', 'isReverse' => false],
                            '140' => ['value' => 'IX30.1, I_PROXY_CASSATE_4', 'isReverse' => false],
                            '159' => ['value' => 'IX30.2, I_PROXY_MARKING_CYL_U', 'isReverse' => false],
                            '158' => ['value' => 'IX30.3, I_PROXY_MARKING_CYL_D', 'isReverse' => false],
                            '145' => ['value' => 'IX30.4, I_PROXY_CUTHOLD_U', 'isReverse' => false],
                            '144' => ['value' => 'IX30.5, I_PROXY_CUTCYL_U', 'isReverse' => false],
                            '143' => ['value' => 'IX30.6, I_PROXY_CUTCYL_D', 'isReverse' => false],
                            '190' => ['value' => 'IX30.7, I_PS_CUTHOLD_D', 'isReverse' => false],
                            '167' => ['value' => 'IX31.0, I_PROXY_PUNCH_1_U', 'isReverse' => false],
                            '166' => ['value' => 'IX31.1, I_PROXY_PUNCH_1_D', 'isReverse' => false],
                            '148' => ['value' => 'IX31.2, I_PROXY_HOLD_1_U', 'isReverse' => false],
                            '179' => ['value' => 'IX31.3, I_PROXY_Y1_U', 'isReverse' => false],
                            '178' => ['value' => 'IX31.4, I_PROXY_Y1_D', 'isReverse' => false],
                            '169' => ['value' => 'IX31.5, I_PROXY_PUNCH_2_U', 'isReverse' => false],
                            '168' => ['value' => 'IX31.6, I_PROXY_PUNCH_2_D', 'isReverse' => false],
                            '149' => ['value' => 'IX31.7, I_PROXY_HOLD_2_U', 'isReverse' => false],
                            '181' => ['value' => 'IX32.0, I_PROXY_Y2_U', 'isReverse' => false],
                            '180' => ['value' => 'IX32.1, I_PROXY_Y2_D', 'isReverse' => false],
                            '171' => ['value' => 'IX32.2, I_PROXY_PUNCH_3_U', 'isReverse' => false],
                            '170' => ['value' => 'IX32.3, I_PROXY_PUNCH_3_D', 'isReverse' => false],
                            '150' => ['value' => 'IX32.4, I_PROXY_HOLD_3_U', 'isReverse' => false],
                            '183' => ['value' => 'IX32.5, I_PROXY_Y3_U', 'isReverse' => false],
                            '182' => ['value' => 'IX32.6, I_PROXY_Y3_D', 'isReverse' => false],
                            '173' => ['value' => 'IX32.7, I_PROXY_PUNCH_4_U', 'isReverse' => false],
                            '172' => ['value' => 'IX33.0, I_PROXY_PUNCH_4_D', 'isReverse' => false],
                            '151' => ['value' => 'IX33.1, I_PROXY_HOLD_4_U', 'isReverse' => false],
                            '185' => ['value' => 'IX33.2, I_PROXY_Y4_U', 'isReverse' => false],
                            '184' => ['value' => 'IX33.3, I_PROXY_Y4_D', 'isReverse' => false],
                            '175' => ['value' => 'IX33.4, I_PROXY_PUNCH_5_U', 'isReverse' => false],
                            '174' => ['value' => 'IX33.5, I_PROXY_PUNCH_5_D', 'isReverse' => false],
                            '152' => ['value' => 'IX33.6, I_PROXY_HOLD_5_U', 'isReverse' => false],
                            '187' => ['value' => 'IX33.7, I_PROXY_Y5_U', 'isReverse' => false],
                            '186' => ['value' => 'IX34.0, I_PROXY_Y5_D', 'isReverse' => false],
                            '189' => ['value' => 'IX34.1, I_PROXY_Y6_U', 'isReverse' => false],
                            '188' => ['value' => 'IX34.2, I_PROXY_Y6_D', 'isReverse' => false],
                            '153' => ['value' => 'IX34.3, I_PROXY_HOLD_6_U', 'isReverse' => false],
                            '177' => ['value' => 'IX34.4, I_PROXY_PUNCH_6_U', 'isReverse' => false],
                            '176' => ['value' => 'IX34.5, I_PROXY_PUNCH_6_D', 'isReverse' => false],
                            '191' => ['value' => 'IX34.6, I_PS_EDGE_CLAMP', 'isReverse' => false],
                            '104' => ['value' => 'IX34.7, I_EDGE_FINDER_ANGLE_PHOTO', 'isReverse' => false],
                            '146' => ['value' => 'IX35.0, I_PROXY_EDGE_FINDER_FWD', 'isReverse' => false],
                            '147' => ['value' => 'IX35.1, I_PROXY_EDGE_FINDER_REV', 'isReverse' => false],
                            '103' => ['value' => 'IX35.2, I_EDGE_DECLAMP_PROXY', 'isReverse' => false],
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