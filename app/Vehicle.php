<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    public $guarded = [];
    public $casts = [
        'raw' => 'object',
        'raw_dot' => 'object',
        'registration_date' => 'datetime',
        'inspection_date' => 'datetime'
    ];
}
