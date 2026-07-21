<?php

namespace App\Controllers;

use App\Libraries\Captcha; // Import library
use App\Libraries\TempLinkService;
use App\Libraries\DynamicPassword;

class Tools extends BaseController
{
    public function captcha()
    {
        $captcha = new Captcha(); // create an instance of Library
        $captcha->generate();
    }

    public function everyMinuteCrone()
    {
        if (!is_cli()) {
            return $this->response->setStatusCode(403, 'Forbidden - CLI Access Only');
        }

        // Get the current hour and minute
        $currentHour = date('H');
        $currentMinute = date('i');

        $device = "/dev/null";
        // $device = WRITEPATH . "logs/cron.log";

        //run every minute
        exec(getenv("phpPath") . " " . FCPATH . "index.php projecttools everyMinuteCrone  > $device &");
        exec(getenv("phpPath") . " " . FCPATH . "index.php tools sendAutoAlertEmails  > $device &");

        // run at midnight for cleanup.
        if ($currentHour === '00' && $currentMinute === '00') {
            exec(getenv("phpPath") . " " . FCPATH . "index.php tools cleanUpCron  > $device &");
        }

        // run at evening 7 pm.
        if ($currentHour === '19' && $currentMinute === '00') {
        }

        // run every 6 minutes
        if ($currentMinute % 6 === 0) {
        }

        // run every 5 minutes
        if ($currentMinute % 5 === 0) {
            // It's 5 minutes, run the scheduled tasks
        }
    }

    public function cleanUpCron()
    {
        if (!is_cli()) {
            return $this->response->setStatusCode(403, 'Forbidden - CLI Access Only');
        }

        $db = db_connect();

        // clean up refreshTokens table.
        $db->table('refreshTokens')->where('expiresAt <', date('Y-m-d H:i:s'))->delete();

        //clean up tokenReuseLogs table.
        $db->table('tokenReuseLogs')->where('createdAt <', date('Y-m-d H:i:s', strtotime("-1 Month")))->delete();
    }

    public function sendAutoAlertEmails()
    {
        if (!is_cli()) {
            return $this->response->setStatusCode(403, 'Forbidden - CLI Access Only');
        }

        $this->db = db_connect();

        // Fetch emails that are queued and have not been sent
        $alerts = $this->db->query("SELECT * FROM emailQueue WHERE isSent = 0 AND attempts <= 3")->getResult();

        foreach ($alerts as $alert) {
            // Explode the 'to' field to get individual email addresses
            $toEmails = explode(',', $alert->recipientEmail);
            $validEmails = [];
            $bouncedEmails = [];
            // debug($toEmails);
            // die;
            // Check each email address against the bouncedEmailsMaster table
            foreach ($toEmails as $email) {

                $email = trim($email); // Trim any whitespace

                //validate email address
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

                    $validEmails[] = $email; // Collect valid emails

                } else {
                    error_log("Invalid email address : $email");
                }
            }

            // If there are valid email addresses, send the email
            if (!empty($validEmails)) {
                $emailService = new \App\Libraries\EmailService();

                // Attempt to send the email
                if ($emailService->send(implode(',', $validEmails), $alert->subject, $alert->body)) {
                    // debug($this->db);
                    // die;
                    // Mark the email as sent in the database
                    $this->db->table("emailQueue")->where("emailId", $alert->emailId)->update([
                        "isSent" => 1,
                        "sentAt" => date("Y-m-d H:i:s"),
                        "remarks" => !empty($bouncedEmails) ? implode(', ', $bouncedEmails) . ' marked as invalid' : null
                    ]);
                } else {
                    // Log the email sending failure
                    // debug($emailLib->printDebugger(['headers']));

                    // Increment the attempts counter
                    $currentAttempts = $alert->attempts;
                    $newAttempts = $currentAttempts + 1;
                    // debug($this->db);
                    // die;
                    $this->db->table("emailQueue")->where("emailId", $alert->emailId)->update([
                        "attempts" => $newAttempts
                    ]);
                    // debug($alert->attempts);
                    // die;
                    if ($alert->attempts >= 3) {
                        // debug($alert->attempts);
                        // die;
                        $this->db->table("emailQueue")->where("emailId", $alert->emailId)->update([
                            "isSent" => 9
                        ]);
                    }
                }

                // Clear the email library instance for the next iteration
                // $emailLib->clear();
            } else {
                // If no valid emails, update the status and remarks in the emailQueue
                $this->db->table("emailQueue")->where("emailId", $alert->emailId)->update([
                    "isSent" => 9,
                    "sentAt" => date("Y-m-d H:i:s"),
                    "remarks" => implode(', ', $bouncedEmails) . ' marked as invalid',
                    // "updatedAt" => date("Y-m-d H:i:s")
                ]);
            }
        }
    }

    public function sendTestEmail()
    {
        $config = config("AppConfig");

        $data = [];
        $data["title"] = "Welcome To Leadman";
        $data["subTitle"] = "Dear ";
        $data["body"] = "<p>Kindly find your LeadMan CRM Login Details as below:</p>";

        $data["body"] .= "<p>Login URL: " . base_url() . "</p>";

        $data["body"] .= "<p>Warm Regards,<br>";
        $data["body"] .= "<b>LeadMan Team.</b></p>";

        $emailBody = view('emails/template' . $config->emailTemplate, $data);

        $emailData = [
            'subject'    => "Welcome To Leadman",
            'body'       => $emailBody,
            'recipientEmail'         => "mindstien@gmail.com",
            'attempts'   => 0,  // Initially setting attempts to 0
            'isSent'     => 0,  // Initially setting isSent to 0
            'remarks'    => "",
            'userId'     => 1,  // Assuming admin is a general user, if you have a specific userId for admin, set it here
            'sentAt'     => '0000-00-00',  // Initially setting sentAt to null
            'createdAt'  => date("Y-m-d H:i:s")
        ];
        $db = db_connect();
        // Insert into the emailQueue table
        $db->table('emailQueue')->insert($emailData);
    }

    // sample code to generate temporary link for protected public access
    public function tempLink()
    {
        $service = new TempLinkService();

        // pay load to save and receive at target controller like passing id or any other data.
        $payload = ['customerId' => 5];

        // target controller to redirect when custom visits the valid link before expiry
        $targetUrl = '/tools/tempController';

        // expiry time in minutes
        $expiryMinutes = 5; // 5 minutes

        //generate link and use as required
        $link = $service->generateLink($targetUrl, $expiryMinutes, $payload);

        return "Temporary Link: <a href='{$link}'>{$link}</a>";
    }

    public function tempController($token = null)
    {
        // receive token from url
        if (is_null($token)) {
            die('Invalid link');
        }

        $service = new TempLinkService();

        // pass token to library to receive the payload stored during temp link generation.
        $payload = $service->getPayload($token);

        //validate payload
        if (is_null($payload)) {
            die('Invalid link');
        }

        // use the payload data as required.
        var_dump($payload);
    }

    public function testing()
    {
        $test = getFriendlyDeviceName("6puNZk0BHSE85La0jLV11A==");
        debug($test);
    }

    public function testDynamicPassword()
    {
        $dp = new DynamicPassword();

        // Show available tokens
        debug($dp->getTokenList());

        // Define pattern
        $pattern = 'chirag{{Y2}}{{(M1N + H + 5) /3}}@Patel{{DNW + DNM}}';

        // Generate dynamic password
        $password = $dp->generate($pattern);
        echo "Generated Password: " . $password . PHP_EOL;

        // Validate
        $input = $password; // simulate user input
        $isValid = $dp->validate($input, $pattern);
        echo "Is Valid? " . ($isValid ? 'Yes' : 'No') . PHP_EOL;
    }
}
