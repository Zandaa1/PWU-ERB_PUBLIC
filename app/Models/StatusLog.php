<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusLog extends Model
{
    protected $fillable = [
        'ethics_request_id',
        'old_status',
        'new_status',
        'changed_by',
        'notes',
    ];

    public function ethicsRequest()
    {
        return $this->belongsTo(EthicsRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
