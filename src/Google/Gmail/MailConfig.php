<?php

namespace PhpMailDemo\Google\Gmail;

class MailConfig
{
    public $config = [];

    public function __construct()
    {
        $this->config = [];
    }

    public function setSenderName($senderName)
    {
        return $this->config['sender_name'] = $senderName;
    }

    public function setSenderEmail($senderEmail)
    {
        return $this->config['sender_email'] = $senderEmail;
    }

    public function setRecipients($recipients)
    {
        return $this->config['recipients'] = $recipients;
    }

    public function setCcRecipients($ccRecipients)
    {
        return $this->config['cc_recipients'] = $ccRecipients;
    }

    public function setSubject($subject)
    {
        return $this->config['subject'] = $subject;
    }

    public function setHtmlBody($htmlBody)
    {
        return $this->config['html_body'] = $htmlBody;
    }

    public function setAttachmentPath($attachmentPath)
    {
        return $this->config['attachment_path'] = $attachmentPath;
    }

    public function setAltBody($altBody)
    {
        return $this->config['alt_body'] = $altBody;
    }
}
