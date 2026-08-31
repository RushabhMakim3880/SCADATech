<?php
$config = config('AppConfig');
echo view('templates/' . $config->theme . '/layouts/header');
echo view('templates/' . $config->theme . '/layouts/sidebar');
echo view($view);
echo view('templates/' . $config->theme . '/layouts/footer');
