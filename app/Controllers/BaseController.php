<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Libraries\AssetManager;
use App\Libraries\UserPermissionLib;
use App\Libraries\Auth;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;
    // protected $jwtData;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        $config = config('AppConfig');

        $this->clientType = $this->request->getCookie('clientType') ?? 'desktop';
        AssetManager::$clientType = $this->clientType;
        AssetManager::autoSyncIfDev();

        if (Auth::check()) {
            //initialize user permissions
            $this->user = Auth::user();
            UserPermissionLib::setUser($this->user);
            AssetManager::loadLibrary('DataTables');
            AssetManager::addJs("Modules/ItemRecipeMaster/addItemrecipemaster.js");
        }

        // Load custom CSS
        AssetManager::addCss('assets/css/app.css');

        if (AssetManager::$clientType === 'mobile') {
            AssetManager::addCss('assets/css/pwaTheme.css');
        }


        AssetManager::addCss('assets/css/project.css');

        // Load custom JS
        AssetManager::addJs('assets/js/app.js');
        AssetManager::addJs('assets/js/project.js');
        AssetManager::addJs('assets/js/apiHandler.js');
        AssetManager::addJs('assets/js/webAuth.js');
        AssetManager::addJs('assets/js/notificationWrapper.js');

        // Load libraries
        AssetManager::loadLibrary($config->notificationLibrary);
        AssetManager::loadLibrary("DatePicker");
        AssetManager::loadLibrary("LocationPicker");
        AssetManager::loadLibrary("HtmlEditor");
        AssetManager::loadLibrary('Select2');
        AssetManager::loadLibrary("Tippy");
        AssetManager::loadLibrary('Barcode');

        // load chat module if enabled.
        if (@$config->chatModuleEnabled) {
            AssetManager::addCss('assets/css/chat.css');
            AssetManager::addJs('assets/js/chat.js');
        }
    }
}
