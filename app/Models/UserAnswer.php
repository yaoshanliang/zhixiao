<?php

namespace App\Models;

use App\Models\Model;

class UserAnswer extends Model
{
    protected $fillable = [
        'user_id', 'subject_code', 'module_code', 'question_id', 'answer', 'status', 'collect'
    ];
}
