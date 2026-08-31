<!-- BEGIN scroll to top btn -->
<a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top" data-toggle="scroll-to-top"><i class="fa fa-angle-up"></i></a>
<!-- END scroll to top btn -->
</div>
<!-- END #app -->

<!-- ================== BEGIN core-js ================== -->
<script src="<?php echo base_url("themeAssets"); ?>/1/js/vendor.min.js"></script>
<script src="<?php echo base_url("themeAssets"); ?>/1/js/app.min.js"></script>
<!-- ================== END core-js ================== -->

<?php echo view('templates/footerAssets');

$config = config('AppConfig');
?>
<!-- Install Prompt Banner -->
<div id="installPrompt" class="text-center" style="display:none; z-index: 9999; position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); background: #fff; padding: 15px 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.2); width: 90%; transition: all 0.3s ease;">
    <div class="install-default text-xl-center">
        <h2 style="font-weight: 100;">Install this app for a better experience.</h2>
        <br>
        <button id="installButton" class="btn btn-sm btn-success"><i class="fa fa-download"></i> Install</button>
    </div>
    <div class="install-ios" style="display: none;">
        <h2 style="font-weight: 100;">📲 Tap <span style="font-size: 18px;">📤</span> then <strong>“Add to Home Screen”</strong> to install this app.</h2>
    </div>
    <button id="dismissButton" class="btn btn-sm btn-light" style="margin-top: 8px;"><i class="fa fa-times"></i> Dismiss</button>
</div>

<!-- Push Prompt for iOS -->
<div id="pushPrompt" class="text-center" style="display:none; z-index: 9999; position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); background: #fff; padding: 15px 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.2); width: 90%; transition: all 0.3s ease;">
    <h2 style="font-weight: 100;">🔔 Enable push notifications for updates.</h2>
    <button id="enablePushButton" class="btn btn-sm btn-primary mt-2">Enable Notifications</button>
    <br>
    <button id="dismissPushPromptButton" class="btn btn-sm btn-secondary mt-2">Dismiss</button>
</div>

</body>

</html>