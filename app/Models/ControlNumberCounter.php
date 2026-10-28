<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ControlNumberCounter extends Model
{
    protected $fillable = [
        'scope',
        'last_sequence',
    ];

    protected $casts = [
        'last_sequence' => 'integer',
    ];
}
