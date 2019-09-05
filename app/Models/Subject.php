<?php

namespace App\Models;

use App\Models\Model;

class Subject extends Model
{
    protected $fillable = [
        'subject_type_id', 'subject_type_name', 'subject_name'
    ];
}
