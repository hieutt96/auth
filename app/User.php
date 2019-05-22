<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Libs\RequestAPI;
use App\Exceptions\AppException;
use Illuminate\Http\Request;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = ['notification_unread', 'balance'];

    public function routeNotificationForMail($notification)
    {
        return $this->email;
    }

    public function getNotificationUnreadAttribute() {

        return count($this->unreadNotifications);
    }

    public function getBalanceAttribute() {

        $res = RequestAPI::requestLedger('GET', '/api/balance',[
            'query' => [
                'user_id' => $this->id,
            ],
        ]);
        if($res->code != AppException::ERR_NONE) {

            throw new AppException(AppException::ERR_SYSTEM);
            
        }
        return $res->data->balance;
    }
}
