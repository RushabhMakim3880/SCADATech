</div>
</div>
<!-- END #app -->

<!-- ================== BEGIN core-js ================== -->
<!-- Tabler Core -->
<!-- <script src="<?php echo base_url("themeAssets"); ?>/2/dist/js/demo-theme.min.js?1692870487"></script> -->
<script src="<?php echo base_url("themeAssets"); ?>/2/dist/js/tabler.min.js?1692870487" defer></script>
<!-- <script src="<?php echo base_url("themeAssets"); ?>/2/dist/js/demo.min.js?1692870487" defer></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/js/all.min.js"></script>

<!-- jquery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- ================== END core-js ================== -->

<?php echo view('templates/footerAssets'); ?>
<div id="installPrompt" class="install-prompt">
    <div class="install-prompt-content">
        <p>Install <b><?php echo getenv("PROJECT_NAME") ?: "This Application"; ?></b> as app for a better experience!</p>
        <!-- <p>Install this as app for a better experience!</p> -->
        <button id="installButton"><i class="fa fa-download"></i> Install</button>
        <button id="dismissButton"><i class="fa fa-times-circle"></i> Dismiss</button>
    </div>
</div>
</body>

</html>