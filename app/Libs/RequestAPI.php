<?php 

namespace App\Libs;

use App\Exceptions\AppException;
use App\Libs\Config;
use GuzzleHttp\Client;

class RequestAPI {


	public static function requestLedger($method, $uri, $options = []) {
		$client = new Client(['base_uri' =>Config::LEDGER_DOMAIN, 'timeout' => 20.0]);
		
		$rs = $client->request($method, $uri, $options);
		if($rs->getStatusCode() != 200){
			throw new AppExcetion(AppExcetion::ERR_SYSTEM);
		}
		$body = json_decode($rs->getBody()->getContents());

		if(!isset($body->code) || $body->code != AppException::ERR_NONE) {
			$body->message = (array) $body->message;
			$msg = '';
			foreach($body->message as $message) {
				if(is_array($message)) {
					$msg .= implode(',', $message);
				}else {
					$msg .= $message;
				}
			}
			throw new AppException($body->code, $msg);
			
		}
		return $body;
	}
}