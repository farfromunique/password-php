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
	$result['method'] = 'string';
	return $this->renderer->render($response, 'ajax.phtml', $result);
});

$app->post('/a/passphrase', function($request, $response, $args) {
	$req = $request->getParsedBody();
	$parameters = [
		'length' =>  $req['password-length'],
		'count' =>  $req['password-count']
	];

	$rand = new ACWPD\RandomOrgClient($this->get('settings')['randomorg']['api_key']);

	require(__DIR__ . '/wordnik/wordnik/Swagger.php');
	$myAPIKey = $this->get('settings')['wordnik']['api_key'];
	$client = new APIClient($myAPIKey, 'http://api.wordnik.com/v4');
	$result['count'] = $parameters['count'];

	$wordApi = new WordsApi($client);

	// TODO: Cleanup this string!

	$words = $wordApi->getRandomWords(null, null, null, null, true, 5, -1, 1, -1, 8, -1, $parameters['count'] * $parameters['length']);
	srand($rand->getRandomInt(65535,1)[0]); // magic numbers: 65535 -> arbitrarily chosen large number; 1 -> number of results to get; 0 -> only 1 result in array.
	shuffle($words); // Words are returned in an alphabetical order by default
	// combine them
	for ($i=0; $i < $result['count']; $i++) {
		for ($offset=0; $offset < $parameters['length']; $offset++) {
			$phraseBuilder[] = str_replace([" ","-"],"_",$words[($i * $parameters['length']) + $offset]->word);
		}
		$phrases[]['password'] = strtolower(implode("-",$phraseBuilder));
		unset($phraseBuilder);
	}
	$result['passwords'] = $phrases;
	$result['method'] = 'phrase';
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
