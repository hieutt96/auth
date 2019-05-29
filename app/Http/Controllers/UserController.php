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
use App\Google2faSecret;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FAQRCode\Google2FA;

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

    public function postLoginVerifyCode(Request $request) {

        $request->validate([
            'verify_code' => 'required',
        ]);
        $user = $request->user();
        $google2fa = Google2faSecret::where('user_id', $user->id)->first();
        if(!$google2fa) {

            throw new AppException(AppException::ERR_SYSTEM);
            
        }
        $secret = $google2fa->secret;
        $g2fa = new Google2FA();
        if($g2fa->verifyKey($secret, $request->verify_code)) {

            return $this->_responseJson([
                'user_id' => $user->id,
                'name' => $user->name,
                'access_token' => $request->header('Authorization'),
                'email'=>$user->email,
                'lvl' => $user->lvl,
                'active' => $user->active,
                'created_at' => $user->created_at,
            ]);
        }else {

            throw new AppException(AppException::ERR_GOOGLE2FA_INVAILD);
            
        }
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
            throw new AppException(AppException::ERR_PASSWORD_INVAILD);
            
        }
        $user = User::where('email', $request->email)->first();
        return $this->_responseJson([
            'code' => '00',
        ]);
    }

    public function createGoogle2fa(Request $request) {

        $request->validate([
            'password' => 'required',
        ]);

        $user = $request->user();
        if(!$user) {
            throw new AppException(AppException::ERR_ACCOUNT_NOT_FOUND);
            
        }
        
        if(Hash::check($request->password, $user->password)){
            $google2fa = Google2faSecret::where('user_id', $user->id)->first();
            if($google2fa) {
                $secretKey = $google2fa->secret;
                $url = 'https://chart.googleapis.com/chart?cht=qr&chs=200x200&choe=UTF-8&chld=M|0&chl=otpauth://totp/Mywallet2FA?secret='.$secretKey;
            }else {
                $google2fa = app('pragmarx.google2fa');
                $secretKey = $google2fa->generateSecretKey();
                $url = 'https://chart.googleapis.com/chart?cht=qr&chs=200x200&choe=UTF-8&chld=M|0&chl=otpauth://totp/Mywallet2FA?secret='.$secretKey;

                $google2faSecret = new Google2faSecret;
                $google2faSecret->user_id = $user->id;
                $google2faSecret->stat = 1;
                $google2faSecret->secret = $secretKey;
                $google2faSecret->save();
            }

            return $this->_responseJson([
                'user_id' => $user->id,
                'secret' => $secretKey,
                'url' => $url,
            ]);
            
        }else {
            throw new AppException(AppException::ERR_PASSWORD_INVAILD);
            
        }
        
    }

    public function offGoogle2fa(Request $request) {

        $request->validate([
            'password' => 'required',
        ]);
        $user = $request->user();
        if(!$user) {
            throw new AppException(AppException::ERR_ACCOUNT_NOT_FOUND);
            
        }
        if(Hash::check($request->password, $user->password)) {

            $google2fa = Google2faSecret::where('user_id', $user->id)->first();
            if(!$google2fa) {

                throw new AppException(AppException::ERR_SYSTEM);
                
            }
            $google2fa->stat = 0;
            $google2fa->save();
            return $this->_responseJson([
                'code' => AppException::ERR_NONE,
            ]);
        }else {
            throw new AppException(AppException::ERR_PASSWORD_INVAILD);
        }
    }

    public function detailGoogle2fa(Request $request) {
        
        $user = $request->user();
        if(!$user) {
            throw new AppException(AppException::ERR_ACCOUNT_NOT_FOUND);
            
        }
        $google2fa = Google2faSecret::where('user_id', $user->id)->where('stat', 1)->first();
        if(!$google2fa) {
            return $this->_responseJson([
                'email' => $user->email,
                'phone' => $user->phone,
                'active' => $user->active,
                'address' => $user->address,
                'status' => '00',
                'created_at' => $user->created_at,
            ]);
        }else {

            return $this->_responseJson([
                'user_id' => $user->id,
                'secret' => $google2fa->secret,
                'stat' => $google2fa->stat,
                'email' => $user->email,
                'phone' => $user->phone,
                'active' => $user->active,
                'address' => $user->address,
                'created_at' => $user->created_at,
                'status' => '01',
            ]);
        }
    }

    public function edit(Request $request) {

        $request->validate([
            'address' => 'required',
            'phone' => 'required',
            'social_id' => 'required',
        ]);
        $user = $request->user();
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->social_id = $request->social_id;
        $user->save();

        $google2fa = Google2faSecret::where('user_id', $user->id)->first();
        if($google2fa) {
            $status = '01';
        }else {
            $status = '00';
        }
        return $this->_responseJson([
            'user_id' => $user->id,
            'email' => $user->email,
            'phone' => $user->phone,
            'active' => $user->active,
            'address' => $user->address,
            'social_id' => $user->social_id,
            'status' => $status,
        ]);
    }

    public function getUser(Request $request) {

        $request->validate([
            'email' => 'required',
        ]);
        $user = User::where('email', $request->email)->first();
        if(!$user) {
            throw new AppException(AppException::USER_NOT_EXIST);
            
        }
        return $this->_responseJson([
            'user_id' => $user->id,
        ]);
    }
}   
