<?php

namespace App\Models;

use App\Models\Model;

class User extends Model
{
    protected $fillable = [
        'account', 'realname', 'email', 'phone', 'password', 'status',
        'openid', 'nickname', 'headimgurl', 'weapp_openid', 'weapp_nickname', 'weapp_avatar', 'subject_id', 'subject_name'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
