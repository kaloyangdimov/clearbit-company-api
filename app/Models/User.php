<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword as AuthCanResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class User extends Authenticatable implements AuthCanResetPassword
{
    use HasFactory, Notifiable, CanResetPassword;

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'token',
        'token_valid_to'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'token_valid_to' => 'datetime'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */

    public function createToken()
    {
        $this->token = hash('sha256', Uuid::uuid4()->toString());
    }

    public function handleToken()
    {
        $this->createToken();
        $this->token_valid_to = Carbon::now()->add('days', 1)->toDateTime();
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}
