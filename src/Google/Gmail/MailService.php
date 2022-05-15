<?php

namespace PhpMailDemo\Google\Gmail;

set_time_limit(300);
ini_set("memory_limit", "20480M");
ini_set('log_errors', 1);
ini_set('display_errors', 0);
date_default_timezone_set('Asia/Taipei');

$baseDirectory = dirname(__DIR__, 3);

require_once $baseDirectory . '/vendor/autoload.php';
require_once $baseDirectory . '/src/Google/Gmail/GoogleClientService.php';

use PhpMailDemo\Google\Gmail\GoogleClientService;
use Exception;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;
use PHPMailer\PHPMailer\PHPMailer;

class MailService
{
    /**
     * @var Gmail $gmailService Google\Service\Gmail an authorized Gmail API service instance.
     */
    protected $gmailService;

    public function __construct()
    {
        $googleClientService = new GoogleClientService();
        $client = $googleClientService->getClient();

        $this->gmailService = new Gmail($client);
    }

    /**
     * The Gmail API requires MIME email messages compliant with RFC 2822 and encoded as base64url strings.
     * 
     * The method for building MIME is using PHPMailer.
     * 
     * @param array $mailConfig
     * @return Message
     */
    public function createMessage($mailConfig): Message
    {
        if (count($mailConfig) === 0) {
            throw new Exception("Error mail config. No any information.", 1);
        }

        if (!isset($mailConfig['sender_email'], $mailConfig['recipients'], $mailConfig['subject'], $mailConfig['html_body'])) {
            throw new Exception("Error mail config. It must has 'sender_email', 'recipients', 'subject' and 'html_body'.", 1);
        }

        $mail = new PHPMailer();
        $mail->CharSet = "UTF-8"; // Default is ascii

        # Sender
        $senderName = isset($mailConfig['sender_name']) ? $mailConfig['sender_name'] : '';
        $mail->setFrom($mailConfig['sender_email'], $senderName);

        # Recipients
        // to
        foreach ($mailConfig['recipients'] as $recipient) {
            $mail->addAddress($recipient);
        }
        // cc
        if (isset($mailConfig['cc_recipients'])) {
            foreach ($mailConfig['cc_recipients'] as $CCRecipient) {
                $mail->addCC($CCRecipient);
            }
        }

        # Attachments
        if (isset($mailConfig['attachment_path'])) {
            $mail->addAttachment($mailConfig['attachment_path'], basename($mailConfig['attachment_path']));
        }

        # Content
        $mail->Subject = $mailConfig['subject'];
        $mail->Body = $mailConfig['html_body'];
        if (isset($mailConfig['alt_body'])) {
            $mail->AltBody = $mailConfig['alt_body'];
        }

        $mail->isHTML(true); // Set email format to HTML
        $mail->preSend();

        # Transform to MIME message
        $mime = $mail->getSentMIMEMessage();
        $mime = rtrim(strtr(base64_encode($mime), '+/', '-_'), '=');

        # Set message
        $message = new Message();
        $message->setRaw($mime);

        return $message;
    }

    /**
     * @param string $userId The user's email address. The special value "me" can be used to indicate the authenticated user.
     * @param Message $message 
     * @return null|Message
     */
    public function sendMail($userId, $message)
    {
        try {
            $message = $this->gmailService->users_messages->send($userId, $message);
            print 'Message with ID: ' . $message->getId() . ' sent.';

            return $message;
        } catch (Exception $e) {
            print 'An error occurred: ' . $e->getMessage();
        }

        return null;
    }
}
