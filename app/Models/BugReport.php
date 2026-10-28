<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BugReport extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'subject',
        'description',
        'page_url',
        'user_agent',
        'diagnostics',
        'status',
    ];

    protected $casts = [
        'diagnostics' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
