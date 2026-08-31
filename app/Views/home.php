<?php

use App\Libraries\Auth;
?>

<!-- BEGIN #content -->
<div id="content" class="app-content">
    <!-- BEGIN breadcrumb -->
    <ol class="breadcrumb float-xl-end">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:;">Library</a></li>
        <li class="breadcrumb-item active">Data</li>
    </ol>
    <!-- END breadcrumb -->
    <!-- BEGIN page-header -->
    <h1 class="page-header">Page Header <small>header small text goes here...</small></h1>
    <!-- END page-header -->

    <!-- BEGIN panel -->
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <h4 class="panel-title">Panel Title here</h4>
            <div class="panel-heading-btn">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-default" data-toggle="panel-expand"><i class="fa fa-expand"></i></a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-success" data-toggle="panel-reload"><i class="fa fa-redo"></i></a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-warning" data-toggle="panel-collapse"><i class="fa fa-minus"></i></a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-danger" data-toggle="panel-remove"><i class="fa fa-times"></i></a>
            </div>
        </div>
        <div class="panel-body">

            <button class="btn btn-primary reloadView">Reload</button>

            <button id="enablePushButton" class="btn btn-primary">Enable Notifications</button>

            <button class="btn btn-primary pwaShareBtn" data-title="this is sample title" data-text="This is simple sample text" data-url="https://www.google.com">📤 Share This Page</button>

            <button class="btn btn-success pwaShareFileBtn"
                data-url="<?php echo getFaviconUrl(); ?>"
                data-filename="sample.png"
                data-type="image/png">
                📤 Share Image
            </button>

            <input type="file" accept="image/*" capture="environment" id="captureImageInput">
            <img id="previewImage" style="max-width: 100%; margin-top: 10px;">



            <input type="file" accept="video/*,audio/*" capture id="captureMediaInput">
            <video id="previewVideo" controls style="max-width: 100%; margin-top: 10px;"></video>
            <audio id="previewAudio" controls style="margin-top: 10px;"></audio>


            <button id="getLocationBtn" class="btn btn-info">📍 Get My Location</button>
            <p id="locationOutput" style="margin-top: 10px;"></p>


            <button id="webAuthRegister" class="btn btn-info">Biomatric Registration</button>
            <input type="hidden" id="webAuthUserId" value="<?php echo Auth::user()->userId; ?>">

            <button id="loginWithBiometricBtn" class="btn btn-primary">
                🔐 Login with Face ID / Fingerprint
            </button>

            <!-- add Barcode library to make this work -->
            <button class="btn btn-primary scanQR" data-continue="0">Scan Once</button>
            <button class="btn btn-secondary scanQR" data-continue="1">Scan Continuously</button>

        </div>
    </div>



    <h1>NativeChannel Actions</h1>

    <button onclick="getContacts()">Get Contacts</button><br>
    <button onclick="getCallLogs()">Get Call Logs</button><br>
    <button onclick="getDeviceInfo()">Get Device Info</button><br>
    <button onclick="getPermissionList()">Get Permission List</button><br>
    <button onclick="getContactPermission()">Request Contact Permission</button><br>
    <button onclick="getPhonePermission()">Request Phone Permission</button><br>
    <button onclick="getIgnoreBatteryOptimizationPermission()">Request Ignore Battery Optimization</button><br>
    <button onclick="getSystemAlertWindowPermission()">Request System Alert Window</button><br>
    <button onclick="sendTokens()">Send Tokens</button><br>

    <h2>Call Popup Control</h2>
    <p id="callPopupStatus">Status: Loading...</p>
    <button onclick="enableCallPopup()">Enable Call Popup</button>
    <button onclick="disableCallPopup()">Disable Call Popup</button>
    <button onclick="getCallPopupStatus()">Check Status</button><br>

    <pre id="output"></pre>

</div>
<!-- END #content -->