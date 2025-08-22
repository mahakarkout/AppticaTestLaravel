<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppTopPosition extends Model
{
    protected $fillable = [
        'app_id',
        'category_id',
        'position',
        'country',
        'date'
    ];
}
