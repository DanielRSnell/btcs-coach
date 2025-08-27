<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VoiceflowSession extends Model
{
    use HasFactory;

    protected $table = 'voiceflow_sessions';

    protected $fillable = [
        'user_id',
        'session_id',
        'project_id',
        'value_data',
        'status',
        'source',
        'session_created_at',
        'session_updated_at',
    ];

    protected $casts = [
        'value_data' => 'array',
        'session_created_at' => 'datetime',
        'session_updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns this session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the session is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'ACTIVE';
    }

    /**
     * Update the session's value data and timestamps.
     */
    public function updateValueData(array $valueData): void
    {
        $this->update([
            'value_data' => $valueData,
            'status' => $valueData['status'] ?? $this->status,
            'session_updated_at' => now(),
        ]);
    }

    /**
     * Get the session's data size in bytes.
     */
    public function getDataSize(): int
    {
        return strlen(json_encode($this->value_data));
    }

    /**
     * Scope for active sessions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'ACTIVE');
    }

    /**
     * Scope for sessions by source.
     */
    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Scope for recent sessions (ordered by session_updated_at).
     */
    public function scopeRecent($query)
    {
        return $query->orderByDesc('session_updated_at')
                    ->orderByDesc('updated_at');
    }
}