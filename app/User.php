<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    // use Notifiable,HasApiTokens;
    use Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','role','wh_id', 'username'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function user_role(){
        return $this->belongsTo(UserRole::class,'role','id');
    }

    public function warehouse(){
        return $this->belongsTo(WareHouse::class,'wh_id','id');
    }

    public function routeNotificationForSlack($notification)
    {
        //Test Hook
        // return 'https://hooks.slack.com/services/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';
        
        //Live Hook
        return 'https://hooks.slack.com/services/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';

        return env('SLACK_WEBHOOK');
    }
    public function accessTokens()
    {
        return $this->hasMany('App\OauthAccessToken');
    }
}
