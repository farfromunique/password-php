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

	public function getPassword($data) {
		$throttle = new \ACWPD\RequestThrottler();
		for ($i=0; $i < $data['count']; $i++) { 
			$n = (int)$data['length'];

			$payload = $this->api_key;
			$payload['n'] = $n;
			$payload['min'] = $data['min'];
			$payload['max'] = $data['max'];
			$payload['replacement'] = true;
			$payload['base'] = 10;
			for ($j=$data['min']; $j < $data['max']; $j++) {
				if (! in_array($j, $data['exclude'])) {
					$chars[] = chr($j);
				}
			}

			$payload['min'] = 1;
			$payload['max'] = (count($chars) - count($data['exclude']));
			$this->client->query(1, 'generateIntegers', $payload);
			$res = $this->client->send()['result'];
			$passletters = array();
			foreach ($res['random']['data'] as $key => $value) {
				$passletters[] = $chars[$value];
			}

			$out['password'] = \implode('', $passletters);
			$out['bitsLeft'] = $res['bitsLeft'];
			$out['reqLeft'] = $res['requestsLeft'];

			$return[] = $out;
			$throttle->delayPerAdvisory($res['advisoryDelay']);
		}
		return $return;
		
	}

	public function converASCIItoInteger($char) {
		if (\is_array($char)) {
			foreach ($char as $key => $value) {
				$out[] = ord($value);
			}
		} else {
			$out = ord($char);
		}
		return $out;
	}
}
