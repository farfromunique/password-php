<?php

require __DIR__ . '/../vendor/autoload.php';
use Datto\JsonRpc\Http\Client;

// Routes
$app->get('/', function ($request, $response) {
	return $this->renderer->render($response, 'index.phtml');
});

$app->post('/a/password', function ($request, $response, $args) {
	
	$req = $request->getParsedBody();
	$parameters = [
		'length' =>  $req['password-length'],
		'exclude' =>  str_split($req['exclude']),
		'count' =>  $req['password-count']
	];

	$client = new ACWPD\RandomOrgClient($this->get('settings')['randomorg']['api_key']);
	$result['passwords'] = $client->getpassword($parameters);
	$result['count'] = $parameters['count'];
	
	return $this->renderer->render($response, 'ajax.phtml', $result);
});