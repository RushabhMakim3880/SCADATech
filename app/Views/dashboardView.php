<?php
$config = config('AppConfig');
?>
<?php echo view('templates/' . $config->theme . '/layouts/viewOpener', ['title' => 'Dashboard Designer']); ?>

<style type="text/css">
    .grid-stack-item-content {
        /* background-color: #fff; */
    }
</style>

<div class="grid-stack mt-3">

</div>

<script>
    var myDashboardId = <?php echo $dashboardId; ?>;
</script>

<?php echo view('templates/' . $config->theme . '/layouts/viewCloser') ?>