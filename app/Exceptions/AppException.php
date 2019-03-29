<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppException extends Exception
{
    const ERR_NONE = 0;

	const ERR_ACCOUNT_NOT_FOUND = 1;
	
	const ERR_SYSTEM = 3;

    protected $code;

    protected $message = [];

	public function __construct($code = Response::HTTP_INTERNAL_SERVER_ERROR, $message = '', $data = [])
	{
		if (!$message) {
			$message = trans('exception.'.$code, $data);
		}

		if (!$code) {
			$code = Response::HTTP_NOT_FOUND;
		}

		$this->code = $code;
		$this->message = $message ?: 'Server Exception';

		parent::__construct($message, $code);
	}

	public function render(Request $request) {

		$json = [
			'code' => $this->code,
			'message' => [$this->message],
			'data' => null,
		];

		return new JsonResponse($json);
	}

	public function report() {
		Log::emergency($this->message);
	}

}
