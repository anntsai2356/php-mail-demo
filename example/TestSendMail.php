<?php

namespace PhpMailDemo;

set_time_limit(300);
ini_set("memory_limit", "20480M");
ini_set('log_errors', 1);
ini_set('display_errors', 0);
date_default_timezone_set('Asia/Taipei');

$baseDirectory = dirname(__DIR__, 1);

require_once $baseDirectory . '/vendor/autoload.php';
require_once $baseDirectory . '/src/Google/Gmail/MailConfig.php';
require_once $baseDirectory . '/src/Google/Gmail/MailService.php';

use PhpMailDemo\Google\Gmail\MailConfig;
use PhpMailDemo\Google\Gmail\MailService;

class TestSendMail
{
    /**
     * @var string SENDER_EMAIL Sender email address
     */
    public const SENDER_EMAIL = 'your_sender_email';
    /**
     * @var string SENDER_NAME Sender email name
     */
    public const SENDER_NAME = 'your_sender_name';
    /**
     * @var array RECIPIENTS Recipients email address
     */
    public const RECIPIENTS = [
        // 'example@email.com',
    ];
    /**
     * @var array CC_RECIPIENTS  CC recipients email address
     */
    public const CC_RECIPIENTS = [
        // 'example@email.com',
    ];
    /**
     * @var string SUBJECT  Email subject
     */
    public const SUBJECT = '[Notify] Test mail';
    /**
     * @var string ALT_BODY Email alternative text
     */
    public const ALT_BODY = 'This is the plain text version of the email content.';
    /**
     * @var string ATTACHMENT_PATH Email attachment
     */
    public const ATTACHMENT_PATH = '/test/attachment_file.example';
    /**
     * @var string HTML_BODY Email text
     */
    public const HTML_BODY = '<p>This is a test mail.</p>';

    /**
     * It can construct your email content with HTML.
     * 
     * @return string HTML body
     */
    public function getHtmlBody(): string
    {
        $result = '';

        $content = [];
        $content[] = "<p>This is a test mail.</p>";

        $result = implode("", $content);

        return $result;
    }
}

$testMailer = new TestSendMail();

$mailConfig = new MailConfig();
$mailConfig->setSenderEmail(TestSendMail::SENDER_EMAIL);
$mailConfig->setSenderName(TestSendMail::SENDER_NAME);
$mailConfig->setRecipients(TestSendMail::RECIPIENTS);
$mailConfig->setSubject(TestSendMail::SUBJECT);
$mailConfig->setAltBody(TestSendMail::ALT_BODY);
// $mailConfig->setAttachmentPath(TestSendMail::ATTACHMENT_PATH);
$mailConfig->setHtmlBody($testMailer->getHtmlBody());

$mailService = new MailService();
$message = $mailService->createMessage($mailConfig->config);
$mailService->sendMail(TestSendMail::SENDER_EMAIL, $message);
