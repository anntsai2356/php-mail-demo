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

use Exception;
use Google\Client;

class OAuth2CallBack extends GoogleClientService
{
    /**
     * @param string $authCode Authorization code from URL.
     * @return void
     */
    public function saveToken($authCode): void
    {
        $client = new Client();
        $client->setAuthConfig($this->credentialFilePATH);

        // Exchange authorization code for an access token.
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
        $client->setAccessToken($accessToken);

        // Check to see if there was an error.
        if (array_key_exists('error', $accessToken)) {
            throw new Exception(join(', ', $accessToken));
        }

        $this->saveTokenIntoFile($client->getAccessToken());
    }
}

// Get authorization code from URL.
if (isset($_GET['code'])) {
    $authCode = $_GET['code'];
} else {
    throw new Exception("Undefined parameter in URL: code.", 1);
}

$oAuth2CallBack = new OAuth2CallBack();
$oAuth2CallBack->saveToken($authCode);
