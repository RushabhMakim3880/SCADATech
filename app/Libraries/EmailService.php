<?php

namespace App\Libraries;

class EmailService
{
    private $email;

    public function __construct()
    {
        $this->email = \Config\Services::email();

        // Load SMTP settings from the .env file
        $this->email->initialize([
            'protocol'  => 'smtp',
            'SMTPHost'  => getenv('email.SMTPHost'),
            'SMTPUser'  => getenv('email.SMTPUser'),
            'SMTPPass'  => getenv('email.SMTPPass'),
            'SMTPPort'  => (int)getenv('email.SMTPPort'),
            'SMTPAuth'  => getenv('email.SMTPAuth'),
            'SMTPCrypto' => getenv('email.SMTPCrypto'),
            // 'SMTPTimeout' => 5,
            'mailType'  => 'html',
            'charset'   => 'utf-8',
        ]);

        $this->email->setFrom(getenv('email.fromEmail'), getenv('email.fromName'));
    }

    public function send($to, $subject, $message, $attachments = [])
    {
        $this->email->setTo($to);
        $this->email->setSubject($subject);
        $this->email->setMessage($message);

        // Attach files if provided
        if (!empty($attachments)) {
            foreach ($attachments as $file) {
                if (file_exists($file)) {
                    $this->email->attach($file);
                } else {
                    log_message('error', "Attachment file does not exist: $file");
                }
            }
        }

        if (!$this->email->send()) {
            // Log or handle the error
            log_message('error', $this->email->printDebugger(['headers']));
            return false;
        }

        return true;
    }
}
