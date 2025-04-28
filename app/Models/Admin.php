<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Notifications\Notifiable;


class Admin extends Model implements Authenticatable
{
    use AuthenticatableTrait;
    use CanResetPassword;
    use Notifiable;

    protected $guard = 'admin';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'mobile',
        'password',
    ];

    // Hide password when returning data
    protected $hidden = [
        'password',
    ];
}
