    <div class="page-wrapper">
        <!-- Page header -->
        <div class="page-header d-print-none">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <!-- Page pre-title -->
                        <?php if (isset($subtitle)) : ?>
                            <div class="page-pretitle">
                                <?php echo $subtitle; ?>
                            </div>
                        <?php endif; ?>
                        <h2 class="page-title">
                            <?php echo $title ?? ""; ?>
                        </h2>
                    </div>
                    <!-- Page title actions -->
                </div>
            </div>
        </div>
        <!-- Page body -->
        <div class="page-body">
            <div class="container-xl">