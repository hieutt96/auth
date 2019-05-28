<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notification;

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
                'id' => $notification->id,
    		];
    	}
    	return $this->_responseJson($notifications, count($notifications));
    }

    public function detail(Request $request) {

        $request->validate([
            'id' => 'required',
        ]);
        $notification = Notification::find($request->id);
        $notification->update(['read_at' => now()]);

        if($notification) {

            return $this->_responseJson([
                'content' => json_decode($notification->data, true)['data'],
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at,
            ]);
        }else {

            throw new AppException(AppException::ERR_NOTIFICATION_NOT_FOUND);
            
        }
    }
}
