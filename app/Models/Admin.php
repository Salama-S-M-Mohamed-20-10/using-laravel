<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
//all models exends Model but any model you use with it authentication such as login and register he extends Authenticatable instead of Model
class Admin extends Authenticatable
{

    use Notifiable; // special for notify
    protected $table = 'admins'; // if this name such as model and adding only s is not must to write it
    protected $fillable = [
        'name', 'email', 'password','photo','created_at','updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', // not every time will enter password such as remind me
    ];
}
