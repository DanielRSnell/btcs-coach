<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CoachingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'module_id',
        'session_id',
        'topic',
        'summary',
        'voiceflow_data',
        'duration',
        'interactions',
        'status',
        'satisfaction_score',
        'department',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'voiceflow_data' => 'array',
        'satisfaction_score' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user for this coaching session.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the module for this coaching session.
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get the action items created from this session.
     */
    public function actionItems()
    {
        return $this->hasMany(ActionItem::class);
    }

    /**
     * Check if the session is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Mark the session as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }
}
