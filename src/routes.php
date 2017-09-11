<?php

require __DIR__ . '/../vendor/autoload.php';
use Datto\JsonRpc\Http\Client;

// Routes
$app->get('/', function ($request, $response) {
	return $this->renderer->render($response, 'index.phtml');
});

$app->post('/a/password', function ($request, $response, $args) {
	$req = $request->getParsedBody();
	$range = explode(':', $req['character-range']);
	$parameters = [
		'min' =>  $range[0],
		'max' =>  $range[1],
		'length' =>  $req['password-length'],
		'exclude' =>  str_split($req['exclude']),
		'count' =>  $req['password-count']
	];

	$client = new ACWPD\RandomOrgClient($this->get('settings')['randomorg']['api_key']);
	$result['passwords'] = $client->getpassword($parameters);
	$result['count'] = $parameters['count'];
	

	return $this->renderer->render($response, 'ajax.phtml', $result);
});

$app->get('/discussion-of-passwords', function ($request, $response) {
	$data['title'] = 'A Discussion of Passwords';
	$text = file_get_contents(__DIR__ . '/articles/discussion-of-passwords.md');
	$html = \Michelf\Markdown::defaultTransform($text);
	$data['body'] = $html;
	return $this->renderer->render($response, 'article.phtml', $data);
});

$app->get('/digression-about-attacks', function ($request, $response) {
	$data['title'] = 'A Discussion of Passwords';
	$text = file_get_contents(__DIR__ . '/articles/digression-about-attacks.md');
	$html = \Michelf\Markdown::defaultTransform($text);
	$data['body'] = $html;
	return $this->renderer->render($response, 'article.phtml', $data);
});
