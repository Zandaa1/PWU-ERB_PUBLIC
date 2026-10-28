<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'document_version_id',
        'commenter_id',
        'parent_id',
        'comment_text',
        'resolved',
    ];

    protected $casts = [
        'resolved' => 'boolean',
    ];

    public function documentVersion()
    {
        return $this->belongsTo(DocumentVersion::class);
    }

    public function commenter()
    {
        return $this->belongsTo(User::class, 'commenter_id');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->orderBy('created_at');
    }
}
