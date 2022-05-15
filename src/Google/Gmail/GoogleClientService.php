<?php

namespace PhpMailDemo\Google\Gmail;

set_time_limit(300);
ini_set("memory_limit", "20480M");
ini_set('log_errors', 1);
ini_set('display_errors', 0);
date_default_timezone_set('Asia/Taipei');

$baseDirectory = dirname(__DIR__, 3);

require_once $baseDirectory . '/vendor/autoload.php';

use Exception;
use Google\Client;
use Google\Service\Gmail;

class GoogleClientService
{
    protected const CLIENT_SCOPE = [
        Gmail::GMAIL_SEND,
    ];
    protected const CONFIG_FOLDER_PATH  = 'config';
    protected const CREDENTIALS_FILE_NAME = 'google_oauth_credentials.json';
    protected const TOKEN_FILE_NAME = 'gmail_api_token.json';

    protected $credentialFilePATH;
    protected $tokenFilePath;

    public function __construct()
    {
        $this->credentialFilePATH = $GLOBALS['baseDirectory'] . '/' . self::CONFIG_FOLDER_PATH . '/' . self::CREDENTIALS_FILE_NAME;
        if (!file_exists($this->credentialFilePATH)) {
            throw new Exception("The credentials file doesn't exist. Please obtain an OAuth client ID from GCP.", 1);
        }

        $this->tokenFilePath = $GLOBALS['baseDirectory'] . '/' . self::CONFIG_FOLDER_PATH . '/' . self::TOKEN_FILE_NAME;

        if (!file_exists($this->tokenFilePath)) {
            throw new Exception("The token file doesn't exist. Please run TokenGenerator to get a new one.", 1);
        }
    }

    /**
     * @param array $content Token information. 
     * @return void
     */
    protected function saveTokenIntoFile($content): void
    {
        $file = fopen($this->tokenFilePath, "w+");
        fclose($file);
        file_put_contents($this->tokenFilePath, json_encode($content));

        echo "Save token into file." . PHP_EOL;
    }

    /**
     * @param Client The unauthorized client object.
     * @return Client The unauthorized client object.
     */
    protected function refreshAccessToken($client): Client
    {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        $this->saveTokenIntoFile($client->getAccessToken());

        echo 'Refresh the access token.' . PHP_EOL;

        return $client;
    }

    /**
     * Returns an unauthorized API client.
     * 
     * @return Client the unauthorized client object
     */
    protected function getClientBaseConfig(): Client
    {
        $client = new Client();
        $client->setApplicationName('Gmail API for BDReport');
        $client->setScopes(self::CLIENT_SCOPE);
        $client->setAuthConfig($this->credentialFilePATH);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        return $client;
    }

    /**
     * Returns an authorized API client.
     * 
     * @return Client the authorized client object
     */
    public function getClient(): Client
    {
        $client = $this->getClientBaseConfig();

        // Load previously authorized token from a file, if it exists.
        $accessToken = json_decode(file_get_contents($this->tokenFilePath), true);
        $client->setAccessToken($accessToken);

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if (!$client->getRefreshToken()) {
                throw new Exception('Can not get refresh token. Please run TokenGenerator to get a new one.', 1);
            }

            $client = $this->refreshAccessToken($client);
        }

        return $client;
    }
}
