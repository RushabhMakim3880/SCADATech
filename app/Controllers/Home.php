<?php

namespace App\Controllers;

use App\Libraries\AssetManager;
use App\Libraries\PushNotification;
use OTPHP\TOTP;
use App\Libraries\UserPermissionLib;

class Home extends BaseController
{
    public function index(): string
    {
        $config = new \Config\AppConfig();

        $data["view"] = 'home';
        $data["pageTitle"] = "Home";

        // Load custom CSS and JS files
        // AssetManager::addCss('assets/css/chat.css');
        AssetManager::addJs('assets/js/home.js');

        // Load predefined libraries
        AssetManager::loadLibrary('echarts');

        return view('viewLoader', $data);
    }

    public function dashboardDesigner()
    {
        if (!UserPermissionLib::userCanDo("superSaasAdmin", 'dynamicDashboard')) {
            return redirect()->to('');
        }

        $data["view"] = 'dashboardDesigner';
        $data["pageTitle"] = "Design Dynamic Dashboard";

        // Load custom CSS and JS files
        // AssetManager::addCss('assets/css/app.css');
        AssetManager::addJs('assets/js/dashboardDesigner.js');

        // Load predefined libraries
        AssetManager::loadLibrary('echarts');
        AssetManager::loadLibrary('GridStack');

        return view('viewLoader', $data);
    }

    public function viewDashboard($dashboardId = 1)
    {
        $data["view"] = 'dashboardView';
        $data["pageTitle"] = "Design Dynamic Dashboard";

        $data['dashboardId'] = $dashboardId;

        // Load custom CSS and JS files
        // AssetManager::addCss('assets/css/app.css');
        AssetManager::addJs('assets/js/dashboardView.js');

        // Load predefined libraries
        AssetManager::loadLibrary('echarts');
        AssetManager::loadLibrary('GridStack');

        return view('viewLoader', $data);
    }

    public function sendNotification()
    {
        $pushNotification = new PushNotification();

        $db = db_connect();
        $subscriptions = $db->table('pushNotification')->where('isValid', true)->get()->getResultArray();

        $message = [
            'title' => 'Hello 2!',
            'body' => 'This is a test notification. 2',
            'icon' => '/assets/img/defaultFavicon.png',
            'badge' => '/assets/img/defaultLogo.png',
            'image' => 'https://placehold.co/600x400/EEE/31343C',
            'url' => '/samples/manageSampleNew',
        ];

        $a = $pushNotification->send($subscriptions, $message);
        echo "<hr>";
        var_dump($a);
    }

    public function saveUserSubscription()
    {
        $subscription = json_decode(file_get_contents('php://input'), true);

        if (!$subscription || !isset($subscription['endpoint'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid subscription data'])->setStatusCode(400);
        }

        // Prepare the subscription data
        $subscriptionData = [
            'tenantId' => $this->user->tenantId,
            'userId' => $this->user->userId,
            'endpoint' => $subscription['endpoint'],
            'publicKey' => $subscription['keys']['p256dh'],
            'authToken' => $subscription['keys']['auth'],
            'isValid' => true,
            'updatedAt' => date('Y-m-d H:i:s'),
        ];

        $db = db_connect();
        $builder = $db->table("pushNotification");

        // Check if the subscription already exists
        $existingSubscription = $builder->where('endpoint', $subscription['endpoint'])->get()->getRowArray();

        if ($existingSubscription) {
            // Update the existing subscription
            $builder->where('wpId', $existingSubscription['wpId'])->update($subscriptionData);
        } else {
            // Add `createdAt` field for new subscriptions
            $subscriptionData['createdAt'] = date('Y-m-d H:i:s');
            // Insert the new subscription
            $builder->insert($subscriptionData);
        }

        return $this->response->setJSON(['status' => 'success']);
    }

    public function sendTestEmail()
    {
        die("Disabled");

        $emailService = new \App\Libraries\EmailService();

        $to = 'mindstien@gmail.com';
        $subject = 'Test Email via AWS SES';
        $message = '<p>This is a test email sent using AWS SES via CodeIgniter 4.</p>';
        $attachments = [
            ROOTPATH . 'public/assets/img/defaultLogo.png', // Path to the first file
            ROOTPATH . 'public/assets/img/defaultFavicon.png', // Path to the second file
        ];


        if ($emailService->send($to, $subject, $message, $attachments)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Email sent successfully']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to send email']);
        }
    }

    public function sseStream()
    {
        // Set SSE-related headers
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');

        // Disable output buffering for immediate data push
        while (ob_get_level() > 0) {
            ob_end_flush();
        }
        // Disable script time limit to allow long-running connection
        set_time_limit(0);

        // Example: send a data event every 2 seconds
        while (true) {

            $data = [
                'time' => date('Y-m-d H:i:s'),
                'message' => 'Hello from CodeIgniter 4!'
            ];
            // Format data for SSE
            echo "data: " . json_encode($data) . "\n\n";

            // Flush the output buffer to ensure data is sent immediately
            flush();

            // Pause before sending the next event
            sleep(2);
        }
    }


    /**  je project ma dashboard na hoy tema default dashboard set karva  **/
    public function blankDefaultDashboard()
    {
        $data["view"] = 'blankDefaultDashboard';
        $data["pageTitle"] = "Home";
        return view('viewLoader', $data);
    }
    /**  je project ma dashboard na hoy tema default dashboard set karva  **/
    
}
