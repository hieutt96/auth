<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Psr\Http\Message\ServerRequestInterface;
use Auth;
use Carbon\Carbon;
use App\Exceptions\AppException;
use Validator;

class UserController extends Controller
{
    public function postRegister(Request $request){
    	
    	$user = new User();
    	$user->name = $request->name;
    	$user->email = $request->email;
    	$user->password = bcrypt($request->password);
    	$user->save();
        // $form_params = [
        //     'a' => 1,
        //     'b' => 2
        // ];
    	return response()->json($user);
    }

    public function getUsers(Request $request){
    	$users = User::all();
    	return response()->json($users);
    }

    public function postLogin(Request $request) {

        $request->validate([
            'email'=>'required',
            'password'=>'required',
        ],[
            'email.required' => 'Bạn chưa nhập email',
            'password.required' => 'Bạn chưa nhập password',
        ]);
        $credentials = ['email' => $request->email, 'password' => $request->password];
        if(!Auth::attempt($credentials)){
            return response()->json(['message' => 'Sai thông tin đăng nhập !']);
        }
        // dd($request->user());
        $user = $request->user();
        $tokenResult = $user->createToken('Hieutt');
        // dd($tokenResult);
        $token = $tokenResult->token;
        
        $token->expires_at = Carbon::now()->addMinutes(15);
        // dd($token->expires_at);
        $token->save();
        return $this->_responseJson([
            'user_id' => $user->id,
            'name' => $user->name,
            'access_token' => $tokenResult->accessToken,
            'email'=>$user->email,
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            'created_at' => $user->created_at,
        ]);
    }

    public function getList(Request $request) {

        $user = $request->user();

        if(!$user) {
            throw new AppException(AppException::ERR_ACCOUNT_NOT_FOUND);
        }

        $users = User::all();

        return $this->responseJson($users);
    }

    public function detail(Request $request) {

        $user = $request->user();

        if(!$user) {
            throw new AppException(AppException::ERR_ACCOUNT_NOT_FOUND);
        }
        return $this->_responseJson($user);
    }
}   
