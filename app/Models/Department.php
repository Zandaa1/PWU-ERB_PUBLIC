<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

/**
 * Academic Department / School model for student ethics review categorization.
 *
 * Invariants:
 * - 14 institutional core colleges/departments are permanently locked from deletion.
 * - Raw attribute arrays (`getAttributes()`) are stored in cache (`departments:active`) and reconstructed
 *   via `hydrate()` to prevent `__PHP_Incomplete_Class` unserialization errors across long-running workers.
 * - Cache is automatically invalidated upon model save/delete lifecycle hooks.
 */
class Department extends Model
{
    public const CORE_DEPARTMENTS = [
        'Arts & Sciences',
        'Business Education',
        'Education',
        'Fine Arts & Design',
        'Food Science & Technology',
        'Hospitality Management',
        'International Relations & Diplomacy',
        'Medical Technology',
        'Music',
        'Nursing',
        'Nutrition',
        'Pharmacy',
        'Social Work',
        'Tourism',
    ];

    protected $fillable = ['name', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('departments:active'));
        static::deleted(fn () => Cache::forget('departments:active'));
    }

    /**
     * Determine if this department is an official institutional core school (locked).
     */
    public function isCore(): bool
    {
        return in_array($this->name, self::CORE_DEPARTMENTS, true);
    }

    /**
     * Retrieve all active departments with multi-process safe cache hydration.
     *
     * @return Collection<int, static>
     */
    public static function getActive(): Collection
    {
        $attributes = Cache::remember('departments:active', now()->addDay(), function () {
            return static::where('is_active', true)->orderBy('name')->get()->map(fn ($d) => $d->getAttributes())->all();
        });

        if (! is_array($attributes)) {
            Cache::forget('departments:active');
            $attributes = static::where('is_active', true)->orderBy('name')->get()->map(fn ($d) => $d->getAttributes())->all();
        }

        return static::hydrate($attributes);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function ethicsRequests(): HasMany
    {
        return $this->hasMany(EthicsRequest::class);
    }
}
