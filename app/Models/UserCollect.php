<?php

namespace App\Models;

use App\Models\Model;

class UserCollect extends Model
{
    protected $table = 'user_collects';

    protected $fillable = [
        'user_id', 'subject_code', 'module_code', 'question_id', 'collect'
    ];
}
