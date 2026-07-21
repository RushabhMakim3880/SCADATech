<?php

use App\Libraries\Auth;
use App\Libraries\UserPermissionLib;

$config = config("AppConfig");

?>

<body>
    <!-- BEGIN #loader -->
    <div id="loader" class="app-loader">
        <span class="spinner"></span>
    </div>
    <!-- END #loader -->

    <?php
    if (isset($config->menuPosition) and $config->menuPosition == "Top") {
    ?>
        <div id="app" class="app app-header-fixed app-sidebar-fixed app-without-sidebar app-with-top-menu has-scroll">
        <?php
    } else {
        ?>
            <div id="app" class="app app-header-fixed app-sidebar-fixed <?= @$sidebarMinified ? 'app-sidebar-minified' : 'app-with-wide-sidebar'; ?>">
            <?php
        }
            ?>


            <!-- BEGIN #app -->

            <!-- BEGIN #header -->
            <div id="header" class="app-header" data-bs-theme="dark">
                <!-- BEGIN navbar-header -->
                <div class="navbar-header">
                    <img src="<?php echo getLogoUrl(); ?>" class="navbar-brand-img mx-auto" alt="main_logo" style="max-height: 50px;">

                    <button type="button" class="navbar-mobile-toggler" data-toggle="app-sidebar-mobile">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <!-- END navbar-header -->

                <!-- BEGIN header-nav -->
                <div class="navbar-nav">
                    <div class="navbar-item navbar-form">
                        <!-- <form action="" method="POST" name="search"> -->
                        <?php
                        if (isset($config->isGlobalSearch) && $config->isGlobalSearch == '1') {
                        ?>
                            <div class="form-group">
                                <input type="text" class="form-control search-dropdown-toggle" placeholder="Search Here...." id="search_bar" data-bs-toggle="dropdown" />
                                <i class="fa fa-search position-absolute end-0 top-50 translate-middle-y" style="font-size: 15px; color: #999999; margin-right: 14px;"></i>

                                <!-- <button type="submit" class="btn btn-search search_result"><i class="fa fa-search"></i></button> -->
                                <div class="dropdown-menu search_result media-list dropdown-menu-end" style="max-height:500px;overflow-y: overlay; width: 300px;">

                                </div>

                            </div>
                        <?php
                        }
                        ?>
                        <!-- </form> -->
                    </div>
                    <!-- <div class="navbar-item dropdown">
                    <a href="#" data-bs-toggle="dropdown" class="navbar-link dropdown-toggle fs-14px">
                        <i class="fa fa-bell"></i>
                        <span class="badge">0</span>
                    </a>
                    <div class="dropdown-menu media-list dropdown-menu-end">
                        <div class="dropdown-header">NOTIFICATIONS (0)</div>
                        <div class="text-center w-300px py-3">
                            No notification found
                        </div>
                    </div>
                </div> -->
                    <div class="navbar-item navbar-user dropdown">
                        <a href="#" class="navbar-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                            <img src="<?= base_url('assets/img/user.png'); ?>" alt="user" class="rounded-circle userProfileImage" />
                            <span class="d-none d-md-inline userProfileName">-</span> <b class="caret ms-10px"></b>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end me-1">

                            <a href="<?= base_url('users/editUser/' . setkey(Auth::user()->userId, 'userMaster')) ?>" class="dropdown-item">
                                <i class="fas fa-user-edit me-1"></i> Edit Profile
                            </a>

                            <!-- <a href="javascript:;" class="dropdown-item d-flex align-items-center">
                                Inbox
                                <span class="badge bg-danger rounded-pill ms-auto pb-4px">2</span>
                            </a>
                            <a href="javascript:;" class="dropdown-item">Calendar</a>
                            <a href="javascript:;" class="dropdown-item">Setting</a>
                            <div class="dropdown-divider"></div> -->
                            <a href="javascript:;" class="dropdown-item appLogOut"> <i class="fa fa-cog me-1"></i>Log Out</a>
                        </div>
                    </div>
                </div>
                <!-- END header-nav -->
            </div>
            <!-- END #header -->


            <?php
            if (isset($config->menuPosition) and $config->menuPosition == "Top") {
            ?>
                <div id="top-menu" class="app-top-menu" data-bs-theme="dark">
                    <div class="menu">
                        <?php echo view('templates/1/layouts/menu'); ?>

                        <div class="menu-item menu-control menu-control-start">
                            <a href="javascript:;" class="menu-link" data-toggle="app-top-menu-prev"><i class="fa fa-angle-left"></i></a>
                        </div>
                        <div class="menu-item menu-control menu-control-end">
                            <a href="javascript:;" class="menu-link" data-toggle="app-top-menu-next"><i class="fa fa-angle-right"></i></a>
                        </div>
                    </div>
                </div>
            <?php
            } else {
            ?>
                <!-- BEGIN #sidebar -->
                <div id="sidebar" class="app-sidebar" data-bs-theme="dark">
                    <!-- BEGIN scrollbar -->
                    <div class="app-sidebar-content" data-scrollbar="true" data-height="100%">
                        <!-- BEGIN menu -->

                        <div class="sidebar-search">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search menu..." id="menuSearch" data-theme="1">
                            </div>
                        </div>
                        <div class="menu">
                            <?php echo view('templates/1/layouts/menu'); ?>
                            <!-- BEGIN minify-button -->
                            <div class="menu-item d-flex">
                                <a href="javascript:;" class="app-sidebar-minify-btn ms-auto d-flex align-items-center text-decoration-none" data-toggle="app-sidebar-minify"><i class="fa fa-angle-double-left"></i></a>
                            </div>
                            <!-- END minify-button -->
                        </div>
                        <!-- END menu -->
                    </div>
                    <!-- END scrollbar -->
                </div>
                <div class="app-sidebar-bg" data-bs-theme="dark"></div>
                <div class="app-sidebar-mobile-backdrop"><a href="#" data-dismiss="app-sidebar-mobile" class="stretched-link"></a></div>
                <!-- END #sidebar -->
            <?php
            }
            ?>