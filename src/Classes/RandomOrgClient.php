<?php 

namespace ACWPD;

class RandomOrgClient extends \Datto\JsonRpc\Http\Client {
	private $api_key;
	private $url;
	private $client;

	public function __construct(string $key) {
		$this->api_key = ["apiKey" => $key];
		$this->url = 'https://api.random.org/json-rpc/1/invoke';
		$this->client = new \Datto\JsonRpc\Http\Client($this->url);
	}

	public function getUsage() {
		$this->client->query(0, 'getUsage', $this->api_key);
		return $this->client->send();
	}

	public function getPassword (
		array $include = ['min' => 33, 'max' => 126],
		array $exclude = [34,36,39,58,59,60,62,73,79,96,105,108,111,124],
		int $length) {
			$n = (int)$length * 1.1;

			$payload = $this->api_key;
			$payload['n'] = $n;
			$payload['min'] = $include['min'];
			$payload['max'] = $include['max'];
			$payload['replacement'] = true;
			$payload['base'] = 10;

			$this->client->query(1, 'generateIntegers', $payload);
			$data = $this->client->send();

			$passletters = array();
			foreach ($data['result']['random']['data'] as $key => $value) {
				if( (! \in_array($value,$exclude)) && count($passletters) < $length) {
					$passletters[] = chr($value);
				}
			}

			$password = \implode('',$passletters);

			return $password;
			
	}
	
}
