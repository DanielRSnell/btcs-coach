<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'coaching_session_id',
        'title',
        'description',
        'priority',
        'status',
        'context',
        'due_date',
        'completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the action item.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the coaching session this action item belongs to.
     */
    public function coachingSession()
    {
        return $this->belongsTo(CoachingSession::class);
    }

    /**
     * Check if the action item is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Mark the action item as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }
}
