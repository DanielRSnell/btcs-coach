<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'slug',
        'type',
        'topics',
        'learning_objectives',
        'estimated_duration',
        'difficulty',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'topics' => 'array',
    ];

    /**
     * Get the users assigned to this module.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'module_user')
            ->withPivot(['assigned_at', 'completed_at', 'progress_data'])
            ->withTimestamps();
    }

    /**
     * Get the coaching sessions for this module.
     */
    public function coachingSessions()
    {
        return $this->hasMany(CoachingSession::class);
    }

    /**
     * Get the achievements related to this module.
     */
    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }
}
