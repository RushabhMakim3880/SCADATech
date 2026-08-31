<!-- BEGIN #content -->
<div id="content" class="app-content">
    <!-- BEGIN breadcrumb -->
    <!-- <ol class="breadcrumb float-xl-end">
        <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:;">Library</a></li>
        <li class="breadcrumb-item active">Data</li>
    </ol> -->
    <!-- END breadcrumb -->
    <!-- BEGIN page-header -->
    <?php if (isset($title) and $title != ""): ?>
        <h1 class="page-header">
            <?php
            echo $title ?? "";
            if (isset($subtitle)) {
                echo "<small>$subtitle</small>";
            }
            ?>
        </h1>
        <!-- END page-header -->

    <?php endif; ?>

    <div id="notificationContainer"></div>