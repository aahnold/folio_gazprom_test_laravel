<?php

namespace App;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * Class User
 * @property Carbon $token_updated_at
 */

class User extends Authenticatable
{
    use Notifiable;

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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'token_updated_at' => 'datetime',
    ];

    public function tokenExpired(){
        return $this->token_updated_at &&
            $this->token_updated_at->addMinutes(config('api.token_lifetime')) <= now() ||
            !$this->token_updated_at;
    }

    public function refreshToken(){
        $this->api_token = Str::random(80);
        $this->token_updated_at = now();
        $this->save();
    }

    public function touchToken(){
        $this->token_updated_at = now();
        $this->save();
    }
}
