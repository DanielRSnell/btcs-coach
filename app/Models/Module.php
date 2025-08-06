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
        'goal',
        'slug',
        'type',
        'topics',
        'sample_questions',
        'learning_objectives',
        'expected_outcomes',
        'estimated_duration',
        'difficulty',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'topics' => 'array',
        'sample_questions' => 'array',
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

    /**
     * Get the action items for this module.
     */
    public function actionItems()
    {
        return $this->hasMany(ActionItem::class);
    }
}
