<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PiBehavioralPattern extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'behavioral_drives',
        'strengths',
        'challenges',
        'work_style',
        'communication_style',
        'leadership_style',
        'ideal_work_environment',
        'motivation_factors',
        'stress_factors',
        'compatible_patterns',
        'is_active',
    ];

    protected $casts = [
        'behavioral_drives' => 'array',
        'compatible_patterns' => 'array',
        'is_active' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'pi_behavioral_pattern_id');
    }


    public function getCompatiblePatternCodes(): array
    {
        return $this->compatible_patterns ?? [];
    }

    public function isCompatibleWith(string $patternCode): bool
    {
        return in_array($patternCode, $this->getCompatiblePatternCodes());
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}