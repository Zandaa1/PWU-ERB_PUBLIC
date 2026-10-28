<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    protected $fillable = [
        'request_document_id',
        'file_path',
        'version_number',
        'change_notes',
        'uploaded_by',
    ];

    protected $casts = [
        'version_number' => 'integer',
    ];

    public function requestDocument()
    {
        return $this->belongsTo(RequestDocument::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id')->orderBy('created_at');
    }

    public function allComments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at');
    }
}
