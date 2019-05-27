<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TxnController extends Controller
{
    public function list(Request $request) {

    	$user = $request->user();
    	$notifications = [];
    	foreach($user->notifications as $notification) {

    		$notifications [] = [
    			'data' => $notification->data,
    			'read_at' => $notification->read_at,
    			'created_at' => $notification->created_at,
    		];
    	}
    	return $this->_responseJson($notifications, count($notifications));
    }
}
