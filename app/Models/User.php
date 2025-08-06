<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'pi_behavioral_pattern_id',
        'pi_raw_scores',
        'pi_assessed_at',
        'pi_assessor_name',
        'pi_notes',
        'pi_profile',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'pi_raw_scores' => 'array',
            'pi_assessed_at' => 'datetime',
            'pi_profile' => 'array',
        ];
    }

    /**
     * Get the coaching sessions for the user.
     */
    public function coachingSessions()
    {
        return $this->hasMany(CoachingSession::class);
    }

    /**
     * Get the achievements for the user.
     */
    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }

    /**
     * Get the action items for the user.
     */
    public function actionItems()
    {
        return $this->hasMany(ActionItem::class);
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user can access Filament admin panel.
     */
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $this->isAdmin();
    }

    /**
     * Check if user is a member.
     */
    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    /**
     * Get the modules accessible to this user.
     */
    public function accessibleModules()
    {
        return $this->belongsToMany(Module::class, 'module_user');
    }

    /**
     * Get the PI behavioral pattern for this user.
     */
    public function piBehavioralPattern()
    {
        return $this->belongsTo(PiBehavioralPattern::class);
    }

    /**
     * Check if user has completed PI assessment.
     */
    public function hasPiAssessment(): bool
    {
        return !is_null($this->pi_behavioral_pattern_id);
    }

    /**
     * Get user's individual PI scores.
     */
    public function getPiScores(): ?array
    {
        return $this->pi_raw_scores;
    }

    /**
     * Get user's dominance score.
     */
    public function getDominanceScore(): ?int
    {
        return $this->pi_raw_scores['dominance'] ?? null;
    }

    /**
     * Get user's extraversion score.
     */
    public function getExtraversionScore(): ?int
    {
        return $this->pi_raw_scores['extraversion'] ?? null;
    }

    /**
     * Get user's patience score.
     */
    public function getPatienceScore(): ?int
    {
        return $this->pi_raw_scores['patience'] ?? null;
    }

    /**
     * Get user's formality score.
     */
    public function getFormalityScore(): ?int
    {
        return $this->pi_raw_scores['formality'] ?? null;
    }

    /**
     * Get user's PI profile data.
     */
    public function getPiProfile(): ?array
    {
        return $this->pi_profile;
    }

    /**
     * Check if user has completed PI profile.
     */
    public function hasPiProfile(): bool
    {
        return !is_null($this->pi_profile);
    }

    /**
     * Get specific section of PI profile.
     */
    public function getPiProfileSection(string $section): ?array
    {
        return $this->pi_profile[$section] ?? null;
    }
}
