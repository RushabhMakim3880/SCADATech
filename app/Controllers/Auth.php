<?php

namespace App\Controllers;

use App\Libraries\AssetManager;
use App\Libraries\Auth as myAuth;

class Auth extends BaseController
{
    public function login()
    {
        // If user is already authenticated, redirect to home page
        if (myAuth::check() && myAuth::verifySingleSignOn()) {
            return redirect()->to(base_url());
        }

        $data = [];
        $data["view"] = "auth/login";
        $data["title"] = "Login";

        AssetManager::addJs('assets/js/auth.js');
        // AssetManager::loadLibrary('gritter');

        $config = config('AppConfig');

        return view("templates/" . $config->theme . "/layouts/login", $data);
    }

    public function logout()
    {
        myAuth::logout();

        // Redirect to login page after logout
        return redirect()->to(base_url('auth/login'));
    }
}
