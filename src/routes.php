<?php

require __DIR__ . '/../vendor/autoload.php';
use Datto\JsonRpc\Http\Client;

// Routes
$app->get('/', function ($request, $response) {
    return $this->renderer->render($response, 'index.phtml');
});

$app->get('/a/password', function ($request, $response, $args) {
    
    $client = new ACWPD\RandomOrgClient($this->get('settings')['randomorg']['api_key']);
    $result['bitsLeft'] = $client->getUsage()['result']['bitsLeft'];
    $result['password'] = $client->getpassword(['min'=>33,'max'=>126],[34,36,39,58,59,60,62,73,79,96,105,108,111,124],10);

    return $this->renderer->render($response, 'ajax.phtml', $result);
});