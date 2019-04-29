<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Psr\Http\Message\ServerRequestInterface;
use Auth;
use App\Events\MessagePusher;
use Carbon\Carbon;
use App\Exceptions\AppException;
use Validator;
use App\Events\SendEmailRegister;

class UserController extends Controller
{
    const TOKEN_EXPIRED = 15;

    public function postRegister(Request $request){
        $user = User::where('email', $request->email)->first();
        if($user) {
            throw new AppException(AppException::EMAIL_EXIST);
            
        }
    	$request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:6',
        ],[
            'email.required' => 'Bạn chưa điền Email',
            'email.unique' => 'Email đã tồn tại trên hệ thống',
            'password.required' => 'Bạn chưa nhập password',
            'password.min' => 'Password phải lớn hơn 6 kí tự',
        ]);
    	$user = new User();
    	$user->name = $request->name;
    	$user->email = $request->email;
    	$user->password = bcrypt($request->password);
        $user->lvl = \App\Models\User::LVL_INIT;
    	$user->save();
        event(new SendEmailRegister($user));
    	return $this->_responseJson([
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'created_at' => $user->created_at,
            'verify_by' => $user->verify_by,
            'lvl' => $user->lvl,
        ]);
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
        $user = User::where('email', $request->email)->first();
        if(!$user) {
            throw new AppException(AppException::ACCOUNT_NO_EXIST);
            
        }
        $credentials = ['email' => $request->email, 'password' => $request->password, 'active' => 1];
        if(!Auth::attempt($credentials)){
            throw new AppException(AppException::ACCOUNT_NOT_ACTIVE);
            
        }
        $user = $request->user();
        $tokenResult = $user->createToken('Hieutt');
        // dd($tokenResult);
        // $token = $tokenResult->token;
        
        // $token->expires_at = Carbon::now()->addMinutes(self::TOKEN_EXPIRED);
        // dd($token->expires_at);
        // $token->save();
        return $this->_responseJson([
            'user_id' => $user->id,
            'name' => $user->name,
            'access_token' => $tokenResult->accessToken,
            'email'=>$user->email,
            'lvl' => $user->lvl,
            'active' => $user->active,
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

    public function active(Request $request) {

        $user = \App\User::find($request->user_id);
        if(!$user) {
            throw new AppException(AppException::ERR_ACCOUNT_NOT_FOUND);
            
        }
        $user->active = User::IS_ACTIVE;
        $user->lvl = User::LVL_ACTIVE;
        $user->save();

        $tokenResult = $user->createToken('Hieutt');
        // dd($tokenResult);
        $token = $tokenResult->token;
        
        $token->expires_at = Carbon::now()->addMinutes(self::TOKEN_EXPIRED);
        // dd($token->expires_at);
        $token->save();
        return $this->_responseJson([
            'user_id' => $user->id,
            'name' => $user->name,
            'access_token' => $tokenResult->accessToken,
            'email'=>$user->email,
            'lvl' => $user->lvl,
            'active' => $user->active,
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            'created_at' => $user->created_at,
        ]);
    }

    public function checkExists(Request $request) {

        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();
        if(!$user) {
            throw new AppException(AppException::USER_NOT_EXIST);
            
        }
        return $this->_responseJson([
            'user_id' => $user->id,
            'name' => $user->name,
            'email'=>$request->email,
            'lvl' => $user->lvl,
            'active' => $user->active,
            'created_at' => $user->created_at,
        ]);
    }

    public function checkPassword(Request $request) {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = ['email' => $request->email, 'password' => $request->password, 'active' => 1];
        if(!Auth::attempt($credentials)){
            throw new AppException(AppException::USER_NOT_EXIST);
            
        }
        $user = User::where('email', $request->email)->first();
        return $this->_responseJson([
            'code' => '00',
        ]);
    }
}   
