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
        throw new AppException(AppException::ERR_ACCOUNT_NOT_FOUND);
    	$form_params = json_decode($request->form_params, true);
    	$user = new User();
    	$user->name = $form_params['name'];
    	$user->email = $form_params['email'];
    	$user->password = bcrypt($form_params['password']);
    	$user->save();
        // $user = User::all();
        // $form_params = [
        //     'a' => 1,
        //     'b' => 2
        // ];
    	return response()->json($form_params);
    }

    public function getUsers(Request $request){
    	$users = User::all();
    	return response()->json($users);
    }

    function _responseJson($data, $count = 0)
    {
        return response()->json([
            'code'=>AppException::ERR_NONE,
            'message'=>[],
            'count'=>$count,
            'data'=>$data,
        ]);
    }

    public function postLogin(Request $request) {

        $request->validate([
            'email'=>'required',
            'password'=>'required',
        ]);
        $credentials = ['email' => $request->email, 'password' => $request->password];
        if(!Auth::attempt($credentials)){
            dd('fail');
            return response()->json(['message' => 'Sai thong tin']);
        }
        // dd($request->user());
        $user = $request->user();
        $tokenResult = $user->createToken('Hieutt');
        // dd($tokenResult);
        $token = $tokenResult->token;
        
        $token->expires_at = Carbon::now()->addMinutes(10);
        // dd($token->expires_at);
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'email'=>$user->email,
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
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
        return $this->responseJson($user);
    }
}   
