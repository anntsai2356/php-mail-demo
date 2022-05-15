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

class TokenGenerator extends GoogleClientService
{
    protected const TOKEN_TEMPLATE = [
        "access_token" => '',
        "expires_in" => 0,
        "scope" => '',
        "token_type" => '',
        "created" => 0,
        "refresh_token" => ''
    ];

    /**
     * The token file stores the user's access and refresh tokens, and is
     * created automatically when the authorization flow completes for the first
     * time.
     * 
     * @return void
     */
    public function generate(): void
    {
        $client = $this->getClientBaseConfig();

        // Initialize tokens into a file.
        $this->saveTokenIntoFile(self::TOKEN_TEMPLATE);
        echo "Created initial token file." . PHP_EOL;

        // Request authorization from the user.
        // Get and exchange authorization code for tokens.
        $authUrl = $client->createAuthUrl();
        printf("Open the following link in your browser:\n%s\n", $authUrl);
    }
}

// Get the API client and construct the service object.
$tokenGenerator = new TokenGenerator();
$tokenGenerator->generate();
