<?php

namespace ACWPD;

class RandomOrgClient extends \Datto\JsonRpc\Http\Client
{
	private $api_key;
	private $url;
	private $client;

	public function __construct(string $key)
	{
		$this->api_key = $key;
		$this->url = 'https://api.random.org/json-rpc/1/invoke';
		$this->client = new \Datto\JsonRpc\Http\Client($this->url);
	}

	public function getUsage()
	{
		$this->client->query(0, 'getUsage', $this->api_key);
		return $this->client->send();
	}

	public function getPassword($data)
	{
		$min = 1; // get numbers between 1 and X, inclusive. If it were exclusive, this would be 0

		$dictionary = $this->generateDictionary($data['exclude']);

		for ($i = 0; $i < (int)$data['count']; $i++) {

			$payload = [
				'apiKey' => $this->api_key,
				'n' => (int)$data['length'],
				'min' => $min,
				'max' => count($dictionary),
				'replacement' => true,
				'base' => 10
			];

			$this->client->query($i, 'generateIntegers', $payload);
			$result = $this->client->send();
			if (isset($result['error'])) {
				$message = 'Error Processing Request: ' . $result['error']['message'];
				if (! $result['error']['message'] === null) {
					$message .= '; Parameter: ' . $result['error']['data'];
					$message .= '; Content: ' . $payload[$result['error']['data']];
				}
				throw new \Exception($message, $result['error']['code']);
			} elseif (isset($result['result'])) {
				$res = $result['result'];

				$passletters = array();
				foreach ($res['random']['data'] as $key => $value) {
					$passletters[] = $dictionary[$value];
				}

				$out['password'] = implode('', $passletters);
				$out['bitsLeft'] = $res['bitsLeft'];
				$out['reqLeft'] = $res['requestsLeft'];

				$return[] = $out;
				RequestThrottler::delayPerAdvisory($res['advisoryDelay']);
			} else {
				$err = \var_export($result);
				throw new \Exception("JSON RPC response contained neither 'error' nor 'result'. Response was: " . $err, 0);
			}
		}
		return $return;
	}

	private function generateDictionary(array $exclude): array
	{
		$ASCIIStart = 33; // Lowest-value ASCII character that is printable (!) (Space is 32)
		$ASCIIEnd = 126; // Highest-value ASCII character that is printable (})
		$highestIndex = 0; // Highest Index used in dictionary
		$dictionary = [];

		for ($i=$ASCIIStart; $i < $ASCIIEnd; $i++) {
			if (! in_array(chr($i), $exclude)) {
				$highestIndex++;
				$dictionary[$highestIndex] = chr($i);
			}
		}

		return $dictionary;
	}
}
