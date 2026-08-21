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

    .ipc-nav-container {
        pointer-events: auto;
        max-width: 100%;
        overflow-x: auto;
        white-space: nowrap;
    }

    .ipc-nav-btn {
        font-size: 13.5px !important;
        font-weight: 500;
        min-width: 95px;
        padding: 5px 10px !important;
        line-height: 1.2;
        white-space: nowrap;
    }

    .ipc-nav-btn i {
        font-size: 1.25rem;
        margin-bottom: 2px;
    }
</style>
<!-- BEGIN #content -->
<div id="content" class="app-content">

    <div id="infoStrip" style="position: fixed; top: 40px; left: 0px; z-index: 1000; font-size: 12px;">
        <div class="d-flex align-items-center gap-2 bg-white bg-opacity-75 rounded px-2 py-1 shadow-sm">
            <span><strong>ID:</strong> <?= getenv("scadaId"); ?></span>
            <span class="text-muted">|</span>
            <span id="liveClock" style="font-family: monospace;">--:--:--</span>
            <span class="text-muted">|</span>
            [<span class="userProfileName"></span>]
            <button class="btn btn-sm btn-outline-secondary py-0 px-1" onclick="toggleNotificationSound()"
                title="Toggle notification sound">
                <i id="soundToggleIcon" class="fa fa-volume-up"></i>
            </button>
            <button class="btn btn-sm btn-outline-primary py-0 px-1" onclick="showSystemInfoModal()"
                title="System Information">
                <i class="fa fa-info-circle"></i>
            </button>
        </div>
    </div>

    <div class="">
        <img src="<?= base_url('assets/img/hptlogo2.png') ?>" alt="OpMaster Image" class="centerLogo"
            style="max-height: 500px; width: auto;">
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

        <div class="position-fixed bottom-0 w-100 text-center pb-2" style="pointer-events: none;left:0px; z-index: 1050;">
            <div class="d-inline-flex flex-nowrap gap-2 justify-content-center ipc-nav-container px-2">
                <button data-route="homing" class="btn btn-primary loadRoutes ipc-nav-btn">
                    <i class="fa fa-home-alt"></i> <br>
                    Homing
                </button>
                <!-- <button data-route="companySetting" class="btn btn-primary loadRoutes ipc-nav-btn">
                    <i class="fa fa-sliders"></i> <br>
                    Screen Setting
                </button> -->
                <button data-route="machineParameters" class="btn btn-primary loadRoutes ipc-nav-btn">
                    <i class="fa fa-cogs"></i> <br>
                    Settings
                </button>

                <button data-route="manualControl" class="btn btn-primary loadRoutes ipc-nav-btn">
                    <i class="fa fa-user-gear"></i> <br>
                    Manual
                </button>
                <button data-route="programPrepare" class="btn btn-primary loadRoutes ipc-nav-btn">
                    <i class="fa fa-file"></i> <br>
                    Program
                </button>
                <button data-route="autoControl" class="btn btn-primary loadRoutes ipc-nav-btn">
                    <i class="fa fa-gears"></i> <br>
                    Auto
                </button>
                <a class="btn btn-primary ipc-nav-btn" data-bs-toggle="modal" href="#inputList">
                    <i class="fa fa-rotate-270 fa-arrow-right-to-bracket"></i> <br>
                    Input
                </a>
                <a class="btn btn-primary ipc-nav-btn" data-bs-toggle="modal" href="#outputList">
                    <i class="fa fa-rotate-90 fa-arrow-right-from-bracket"></i> <br>
                    Output
                </a>
                <a class="btn btn-primary ipc-nav-btn" data-bs-toggle="modal" href="#debugLog">
                    <i class="fa fa-list"></i> <br>
                    Log
                </a>

                <!-- <button data-route="log" class="btn btn-primary loadRoutes ipc-nav-btn">
                    <i class="fa fa-list"></i> <br>
                    Log
                </button> -->
                <!-- <button class="btn btn-primary appLogOut ipc-nav-btn">
                    <i class="fa fa-sign-out"></i> <br>
                    Log Out
                </button> -->

                <button class="btn btn-primary exitBtn nativeChannelBtns ipc-nav-btn">
                    <i class="fa fa-times"></i> <br>
                    Exit
                </button>

                <div class="dropdown nativeChannelBtns">
                    <button class="btn btn-primary dropdown-toggle ipc-nav-btn" data-bs-toggle="dropdown">
                        <i class="fa fa-list"></i> <br>
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
                <!-- <button class="btn btn-primary shutdownBtn nativeChannelBtns ipc-nav-btn">
                <i class="fa fa-power-off"></i> <br>
                Shutdown
            </button> -->

                <!-- restartBtn -->
                <!-- <button class="btn btn-primary restartBtn nativeChannelBtns ipc-nav-btn">
                <i class="fa fa-rotate"></i> <br>
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
                            '206' => ['value' => 'P1_SAFETY_STOP', 'isReverse' => true],
                            '207' => ['value' => 'P2_SAFETY_STOP', 'isReverse' => true],
                            '208' => ['value' => 'P3_SAFETY_STOP', 'isReverse' => true],
                            '209' => ['value' => 'P4_SAFETY_STOP', 'isReverse' => true],
                            '210' => ['value' => 'P5_SAFETY_STOP', 'isReverse' => true],
                            '211' => ['value' => 'P6_SAFETY_STOP', 'isReverse' => true],
                            '167' => ['value' => 'I_PROXY_PUNCH_1_U', 'isReverse' => false],
                            '169' => ['value' => 'I_PROXY_PUNCH_2_U', 'isReverse' => false],
                            '171' => ['value' => 'I_PROXY_PUNCH_3_U', 'isReverse' => false],
                            '173' => ['value' => 'I_PROXY_PUNCH_4_U', 'isReverse' => false],
                            '175' => ['value' => 'I_PROXY_PUNCH_5_U', 'isReverse' => false],
                            '177' => ['value' => 'I_PROXY_PUNCH_6_U', 'isReverse' => false],
                            '159' => ['value' => 'I_PROXY_MARKING_CYL_U', 'isReverse' => false], //ok
                            '137' => ['value' => 'I_PROXY_CASSATE_1', 'isReverse' => false],
                            '144' => ['value' => 'I_PROXY_CUTCYL_U', 'isReverse' => false],
                            '145' => ['value' => 'I_PROXY_CUTHOLD_U', 'isReverse' => false],
                            '157' => ['value' => 'I_PROXY_MARKING_BODY_U', 'isReverse' => false],
                            '205' => ['value' => 'MARKING_SAFETY_STOP', 'isReverse' => true],
                            '162' => ['value' => 'I_PROXY_PRINCHER_D', 'isReverse' => false],
                            '107' => ['value' => 'I_MAIN_MOTOR_RUN', 'isReverse' => false],
                            '200' => ['value' => 'IN_FEED_90_OK', 'isReverse' => false],
                            // '503' => ['value' => 'MARKING_CMD', 'isReverse' => true],
                            // '504' => ['value' => 'CUTTING_CMD', 'isReverse' => true],
                            '212' => ['value' => 'PRINCHER_CLAMP_PRESSURE_OK', 'isReverse' => false],
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
                            '114' => ['value' => 'X1, I_PB_ESTOP_1_OPERATING_PENAL', 'isReverse' => false],
                            '115' => ['value' => 'X2, I_PB_ESTOP_2_PRINCHER_REMOTE', 'isReverse' => false],
                            '116' => ['value' => 'X3, I_PB_ESTOP_3_OUTFEED_REMOTE', 'isReverse' => false],
                            '121' => ['value' => 'X4, I_PHASE_PROTECTOR_GIC', 'isReverse' => false],
                            '107' => ['value' => 'X5, I_MAIN_MOTOR_RUN', 'isReverse' => false],
                            '108' => ['value' => 'X6, I_MAIN_MOTOR_TRIP', 'isReverse' => false],
                            '99' => ['value' => 'X7, I_CHAIN_FEEDER_MOTOR_FWD_RUN', 'isReverse' => false],
                            '100' => ['value' => 'IX24.0, I_CHAIN_FEEDER_MOTOR_REV_RUN', 'isReverse' => false],
                            '97' => ['value' => 'IX24.1, I_CHAIN_FEEDER_MOTOR_1_TRIP', 'isReverse' => false],
                            '98' => ['value' => 'IX24.2, I_CHAIN_FEEDER_MOTOR_2_TRIP', 'isReverse' => false],
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

                        $half = ceil(count($list) / 2);
                        $finalList = [];
                        $finalList[] = array_slice($list, 0, $half, true);
                        $finalList[] = array_slice($list, $half, null, true);

                        foreach ($finalList as $list) {
                            echo "<div class='col-6'>";
                            foreach ($list as $id => $item) {
                                $name = $item['value'];
                                $onColor = "#82c779";
                                $offColor = "#ff5b57";

                                if ($item['isReverse']) {
                                    $onColor = "#ff5b57";
                                    $offColor = "#82c779";
                                }

                                echo "<div class='mt-1 d-flex align-items-center text-nowrap'>
                                    <button class='plc-btn btn btn-sm flex-shrink-0 me-1'
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
                            '220' => ['value' => 'Y0, Q_MAIN_HYD_MOTOR', 'isReverse' => false],
                            '221' => ['value' => 'Y1, Q_OIL_CIRCULATION_MOTOR', 'isReverse' => false],
                            '216' => ['value' => 'Y2, Q_COOLER_MOTOR', 'isReverse' => false],
                            '218' => ['value' => 'Y3, Q_HEAD_LUBRICATION_MOTOR', 'isReverse' => false],
                            '214' => ['value' => 'Y4, Q_CHAIN_FEEDER_1_AND_2_FWD', 'isReverse' => false],
                            '215' => ['value' => 'Y5, Q_CHAIN_FEEDER_1_AND_2_REV', 'isReverse' => false],
                            '223' => ['value' => 'Y6, Q_PRINCHER_LUB_MOTOR', 'isReverse' => false],
                            '222' => ['value' => 'Y7, Q_PRINCHER_HYD_MOTOR', 'isReverse' => false],
                            '274' => ['value' => 'QX0.0, Q_Y1_SERVO_BREAK', 'isReverse' => false],
                            '275' => ['value' => 'QX0.1, Q_Y2_SERVO_BREAK', 'isReverse' => false],
                            '276' => ['value' => 'QX0.2, Q_Y3_SERVO_BREAK', 'isReverse' => false],
                            '277' => ['value' => 'QX0.3, Q_Y4_SERVO_BREAK', 'isReverse' => false],
                            '272' => ['value' => 'QX0.4, Q_TL_RED', 'isReverse' => false],
                            '273' => ['value' => 'QX0.5, Q_TL_YELLOW', 'isReverse' => false],
                            '270' => ['value' => 'QX0.6, Q_TL_GREEN', 'isReverse' => false],
                            '271' => ['value' => 'QX0.7, Q_TL_HOOTER', 'isReverse' => false],
                            '219' => ['value' => 'QX1.0, Q_INDI_AUTO_CYCLE_ON', 'isReverse' => false],
                            '232' => ['value' => 'QX1.1, Q_SV_HIGH_PRESSURE', 'isReverse' => false],
                            '247' => ['value' => 'QX1.2, Q_SV_LOW_PRESSURE', 'isReverse' => false],
                            '245' => ['value' => 'QX1.3, Q_SV_IN_FEED_0', 'isReverse' => false],
                            '246' => ['value' => 'QX1.4, Q_SV_IN_FEED_90', 'isReverse' => false],
                            '257' => ['value' => 'QX1.5, Q_SV_PRINCH_U', 'isReverse' => false],
                            '255' => ['value' => 'QX1.6, Q_SV_PRINCH_D', 'isReverse' => false],
                            '254' => ['value' => 'QX1.7, Q_SV_PRINCH_CLAMP', 'isReverse' => false],
                            '256' => ['value' => 'QX2.0, Q_SV_PRINCH_DECLAMP', 'isReverse' => false],
                            '253' => ['value' => 'QX2.1, Q_SV_OUT_FEED_U', 'isReverse' => false],
                            '252' => ['value' => 'QX2.2, Q_SV_OUT_FEED_D', 'isReverse' => false],
                            '249' => ['value' => 'QX2.3, Q_SV_MARKING_BODY_U', 'isReverse' => false],
                            '248' => ['value' => 'QX2.4, Q_SV_MARKING_BODY_D', 'isReverse' => false],
                            '224' => ['value' => 'QX2.5, Q_SV_CASSAT_D', 'isReverse' => false],
                            '225' => ['value' => 'QX2.6, Q_SV_CASSAT_U', 'isReverse' => false],
                            '251' => ['value' => 'QX2.7, Q_SV_MARKING_CYL_U', 'isReverse' => false],
                            '250' => ['value' => 'QX3.0, Q_SV_MARKING_CYL_D', 'isReverse' => false],
                            '227' => ['value' => 'QX3.1, Q_SV_CUT_CYL_U', 'isReverse' => false],
                            '226' => ['value' => 'QX3.2, Q_SV_CUT_CYL_D', 'isReverse' => false],
                            '229' => ['value' => 'QX3.3, Q_SV_CUT_HOLD_U', 'isReverse' => false],
                            '228' => ['value' => 'QX3.4, Q_SV_CUT_HOLD_D', 'isReverse' => false],
                            '234' => ['value' => 'QX3.5, Q_SV_HOLD_1_U', 'isReverse' => false],
                            '233' => ['value' => 'QX3.6, Q_SV_HOLD_1_D', 'isReverse' => false],
                            '259' => ['value' => 'QX3.7, Q_SV_PUNCH_1_U', 'isReverse' => false],
                            '258' => ['value' => 'QX4.0, Q_SV_PUNCH_1_D', 'isReverse' => false],
                            '236' => ['value' => 'QX4.1, Q_SV_HOLD_2_U', 'isReverse' => false],
                            '235' => ['value' => 'QX4.2, Q_SV_HOLD_2_D', 'isReverse' => false],
                            '261' => ['value' => 'QX4.3, Q_SV_PUNCH_2_U', 'isReverse' => false],
                            '260' => ['value' => 'QX4.4, Q_SV_PUNCH_2_D', 'isReverse' => false],
                            '238' => ['value' => 'QX4.5, Q_SV_HOLD_3_U', 'isReverse' => false],
                            '237' => ['value' => 'QX4.6, Q_SV_HOLD_3_D', 'isReverse' => false],
                            '263' => ['value' => 'QX4.7, Q_SV_PUNCH_3_U', 'isReverse' => false],
                            '262' => ['value' => 'QX5.0, Q_SV_PUNCH_3_D', 'isReverse' => false],
                            '240' => ['value' => 'QX5.1, Q_SV_HOLD_4_U', 'isReverse' => false],
                            '239' => ['value' => 'QX5.2, Q_SV_HOLD_4_D', 'isReverse' => false],
                            '265' => ['value' => 'QX5.3, Q_SV_PUNCH_4_U', 'isReverse' => false],
                            '264' => ['value' => 'QX5.4, Q_SV_PUNCH_4_D', 'isReverse' => false],
                            '242' => ['value' => 'QX5.5, Q_SV_HOLD_5_U', 'isReverse' => false],
                            '241' => ['value' => 'QX5.6, Q_SV_HOLD_5_D', 'isReverse' => false],
                            '267' => ['value' => 'QX5.7, Q_SV_PUNCH_5_U', 'isReverse' => false],
                            '266' => ['value' => 'QX6.0, Q_SV_PUNCH_5_D', 'isReverse' => false],
                            '244' => ['value' => 'QX6.1, Q_SV_HOLD_6_U', 'isReverse' => false],
                            '243' => ['value' => 'QX6.2, Q_SV_HOLD_6_D', 'isReverse' => false],
                            '269' => ['value' => 'QX6.3, Q_SV_PUNCH_6_U', 'isReverse' => false],
                            '268' => ['value' => 'QX6.4, Q_SV_PUNCH_6_D', 'isReverse' => false],
                            '278' => ['value' => 'QX6.5, Q_Y5_SERVO_BREAK', 'isReverse' => false],
                            '279' => ['value' => 'QX6.6, Q_Y6_SERVO_BREAK', 'isReverse' => false],
                            '217' => ['value' => 'QX6.7, Q_EDGE_FIND_SERVO_BREAK_OUTPUT', 'isReverse' => false],
                            '230' => ['value' => 'QX7.0, Q_SV_EDGE_FIND_CLAMP', 'isReverse' => false],
                            '231' => ['value' => 'QX7.1, Q_SV_EDGE_FIND_DECLAMP', 'isReverse' => false],
                        ];

                        $half = ceil(count($list) / 2);
                        $finalList = [];
                        $finalList[] = array_slice($list, 0, $half, true);
                        $finalList[] = array_slice($list, $half, null, true);

                        foreach ($finalList as $list) {
                            echo "<div class='col-6'>";
                            foreach ($list as $id => $item) {
                                $name = $item['value'];
                                $onColor = "#82c779";
                                $offColor = "#ff5b57";

                                if ($item['isReverse']) {
                                    $onColor = "#ff5b57";
                                    $offColor = "#82c779";
                                }

                                echo "<div class='mt-1 d-flex align-items-center text-nowrap'>
                                    <button class='plc-btn btn btn-sm flex-shrink-0 me-1'
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

<script>
    disAllowedTags = <?php echo json_encode($disAllowedTags); ?>;

    // ---- Live Clock with configurable format ----
    // Format tokens: {YYYY} {MM} {DD} {DAY} {MMM} {MMMM} {hh} {mm} {ss} {A}
    // Change this string to adjust display format
    var clockFormat = '{DAY}, {DD} {MMM} {YYYY} · {hh}:{mm}:{ss} {A}';

    (function updateClock() {
        const el = document.getElementById('liveClock');
        if (el) {
            const now = new Date();
            const pad = n => String(n).padStart(2, '0');
            const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const monthsFull = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            let h = now.getHours();
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12; // 12hr format

            const output = clockFormat
                .replace('{YYYY}', now.getFullYear())
                .replace('{MM}', pad(now.getMonth() + 1))
                .replace('{DD}', pad(now.getDate()))
                .replace('{DAY}', days[now.getDay()])
                .replace('{MMMM}', monthsFull[now.getMonth()])
                .replace('{MMM}', months[now.getMonth()])
                .replace('{hh}', pad(h))
                .replace('{mm}', pad(now.getMinutes()))
                .replace('{ss}', pad(now.getSeconds()))
                .replace('{A}', ampm);

            el.textContent = output;
        }
        setTimeout(updateClock, 1000);
    })();

    // Set initial sound icon based on saved preference
    document.addEventListener('DOMContentLoaded', function () {
        const saved = localStorage.getItem('notificationSoundEnabled');
        const icon = document.getElementById('soundToggleIcon');
        if (icon && saved === '0') {
            icon.className = 'fa fa-volume-mute';
        }
    });
</script>