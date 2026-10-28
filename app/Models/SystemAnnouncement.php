<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * SystemAnnouncement model for portal-wide maintenance and operational broadcasts.
 *
 * Invariants:
 * - Rendered across login (`/`), dashboard, and developer panels.
 * - Safely hydrated from raw attributes in cache (`system_announcement:active`) to prevent multi-process class desynchronization.
 * - Zero emoji institutional styling enforced by design system.
 */
class SystemAnnouncement extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('system_announcement:active'));
        static::deleted(fn () => Cache::forget('system_announcement:active'));
    }

    /**
     * Retrieve current active portal announcement with safe cache hydration.
     */
    public static function getActive(): ?static
    {
        if (! Schema::hasTable('system_announcements')) {
            return null;
        }

        $attributes = Cache::remember('system_announcement:active', now()->addHours(6), function () {
            $announcement = static::where('is_active', true)->latest()->first();

            return $announcement ? $announcement->getAttributes() : null;
        });

        if (! is_array($attributes)) {
            if ($attributes !== null) {
                Cache::forget('system_announcement:active');
            }

            return null;
        }

        $model = new static;
        $model->setRawAttributes($attributes, true);
        $model->exists = true;

        return $model;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
