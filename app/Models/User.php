<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'title',
        'is_chairperson',
        'is_developer',
        'specialty_domains',
        'is_active',
        'is_archived',
        'archived_at',
    ];

    protected $attributes = [
        'is_active' => true,
        'is_chairperson' => false,
        'is_developer' => false,
        'is_archived' => false,
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_chairperson' => 'boolean',
            'is_developer' => 'boolean',
            'specialty_domains' => 'array',
            'is_active' => 'boolean',
            'is_archived' => 'boolean',
            'archived_at' => 'datetime',
        ];
    }

    public function reviewerAssignments(): HasMany
    {
        return $this->hasMany(ReviewerAssignment::class);
    }

    public function ethicsRequests()
    {
        return $this->hasMany(EthicsRequest::class, 'student_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'commenter_id');
    }

    public function appNotifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->name ?? 'User'));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $initials .= strtoupper(substr($w, 0, 1));
        }

        return $initials ?: 'U';
    }
}
