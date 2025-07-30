<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'module_id',
        'title',
        'description',
        'type',
        'points',
        'badge_icon',
        'badge_color',
        'criteria',
        'progress_percentage',
        'is_unlocked',
        'unlocked_at',
    ];

    protected $casts = [
        'criteria' => 'array',
        'progress_percentage' => 'decimal:2',
        'is_unlocked' => 'boolean',
        'unlocked_at' => 'datetime',
    ];

    /**
     * Get the user that owns the achievement.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the module this achievement belongs to.
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Check if the achievement is unlocked.
     */
    public function isUnlocked(): bool
    {
        return $this->is_unlocked;
    }

    /**
     * Unlock the achievement.
     */
    public function unlock(): void
    {
        $this->update([
            'is_unlocked' => true,
            'unlocked_at' => now(),
            'progress_percentage' => 100.00,
        ]);
    }

    /**
     * Update progress towards the achievement.
     */
    public function updateProgress(float $percentage): void
    {
        $this->update([
            'progress_percentage' => min(100.00, max(0.00, $percentage)),
        ]);

        if ($percentage >= 100.00 && !$this->is_unlocked) {
            $this->unlock();
        }
    }
}
