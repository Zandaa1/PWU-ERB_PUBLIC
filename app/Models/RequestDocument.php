<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class RequestDocument extends Model
{
    protected $fillable = [
        'ethics_request_id',
        'document_type_id',
        'status',
    ];

    protected $casts = [
        'status' => DocumentStatus::class,
    ];

    public function ethicsRequest()
    {
        return $this->belongsTo(EthicsRequest::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class)->orderBy('version_number');
    }

    public function latestVersion()
    {
        return $this->hasOne(DocumentVersion::class)->latestOfMany('version_number');
    }

    public function isResubmission(): bool
    {
        if ($this->relationLoaded('versions')) {
            $latest = $this->versions->sortBy('version_number')->last();

            return $latest && $latest->version_number > 1;
        }

        $latest = $this->latestVersion;

        return $latest && $latest->version_number > 1;
    }

    protected static function booted(): void
    {
        static::saved(function (RequestDocument $doc) {
            if ($doc->ethicsRequest) {
                Cache::forget("student_dashboard:requests:{$doc->ethicsRequest->student_id}");
                Cache::forget("student_dashboard:popup:{$doc->ethicsRequest->student_id}");
            }
        });

        static::deleted(function (RequestDocument $doc) {
            if ($doc->ethicsRequest) {
                Cache::forget("student_dashboard:requests:{$doc->ethicsRequest->student_id}");
                Cache::forget("student_dashboard:popup:{$doc->ethicsRequest->student_id}");
            }
        });
    }
}
