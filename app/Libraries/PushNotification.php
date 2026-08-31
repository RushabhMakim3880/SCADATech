<?php

namespace App\Libraries;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class PushNotification
{
    private $auth;

    public function __construct()
    {
        $this->auth = [
            'VAPID' => [
                'subject' => 'mailto:' . getenv('webPushSenderEmail'),
                'publicKey' => getenv('webPushPublicKey'),
                'privateKey' => getenv('webPushPrivateKey'),
            ],
        ];
    }

    public function send($subscriptions, $message)
    {
        $webPush = new WebPush($this->auth);

        foreach ($subscriptions as $sub) {
            $subscription = Subscription::create([
                'endpoint' => $sub['endpoint'],
                'keys' => [
                    'p256dh' => $sub['publicKey'],
                    'auth' => $sub['authToken'],
                ],
            ]);

            $report = $webPush->sendOneNotification($subscription, json_encode($message));

            // debug($report->getReason());
            // debug($report->getResponse());
            // debug($report->isSubscriptionExpired());

            if (!$report->isSuccess()) {
                // Mark subscription as invalid in your database (optional)
                $db = db_connect();
                $db->table('pushNotification')->where('endpoint', $sub['endpoint'])->update(['isValid' => false]);
            }
        }

        $webPush->flush();
    }
}
