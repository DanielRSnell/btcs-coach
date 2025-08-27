<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Models\Contracts\FilamentUser;

class User extends Authenticatable implements FilamentUser
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
        $canAccess = $this->isAdmin();
        \Log::info("User {$this->email} attempting panel access. Role: {$this->role}. Can access: " . ($canAccess ? 'YES' : 'NO'));
        return $canAccess;
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

    /**
     * Relationship: Get all Voiceflow sessions for the user.
     */
    public function voiceflowSessions(): HasMany
    {
        return $this->hasMany(\App\Models\VoiceflowSession::class);
    }

    /**
     * Get all Voiceflow sessions for the user.
     */
    public function getSessions(): ?array
    {
        $sessionModels = $this->voiceflowSessions()->orderBy('session_updated_at', 'desc')->get();
        
        if ($sessionModels->isEmpty()) {
            return [];
        }
        
        // Convert to the format expected by the frontend
        // Key by session_id (userID) since each chat session has a unique userID
        // This ensures we show ALL sessions, not just one per project_id
        return $sessionModels->keyBy('session_id')->map(function ($session) {
            return [
                'session_id' => $session->session_id, // Voiceflow userID (the actual unique identifier)
                'project_id' => $session->project_id, // localStorage key (might be same for multiple sessions)
                'value' => $session->value_data,
                'status' => $session->status,
                'source' => $session->source,
                'created_at' => $session->session_created_at?->toISOString(),
                'updated_at' => $session->session_updated_at?->toISOString(),
            ];
        })->toArray();
    }

    /**
     * Get a specific Voiceflow session by project ID and optionally userID.
     * 
     * @param string $projectId The Voiceflow project ID (localStorage key)
     * @param string|null $voiceflowUserID Optional Voiceflow userID for more precise lookup
     */
    public function getSession(string $projectId, ?string $voiceflowUserID = null): ?array
    {
        $query = $this->voiceflowSessions()->where('project_id', $projectId);
        
        if ($voiceflowUserID) {
            $query->where('session_id', $voiceflowUserID);
        }
        
        $sessionModel = $query->first();
        
        if (!$sessionModel) {
            return null;
        }
        
        return [
            'session_id' => $sessionModel->session_id, // This is the Voiceflow userID
            'project_id' => $sessionModel->project_id, // This is the localStorage key
            'value' => $sessionModel->value_data,
            'status' => $sessionModel->status,
            'source' => $sessionModel->source,
            'created_at' => $sessionModel->session_created_at?->toISOString(),
            'updated_at' => $sessionModel->session_updated_at?->toISOString(),
        ];
    }

    /**
     * Set or update a Voiceflow session.
     * 
     * @param string $projectId The Voiceflow project ID (localStorage key like '686331bc96acfa1dd62f6fd5')
     * @param array $sessionData The session data containing userID and other info
     */
    public function setSession(string $projectId, array $sessionData): void
    {
        $valueData = $sessionData['last_turn'] ?? $sessionData;
        
        // Extract the userID from the value data - this is the actual session identifier in Voiceflow
        $voiceflowUserID = $valueData['userID'] ?? null;
        
        if (!$voiceflowUserID) {
            throw new \InvalidArgumentException('Session data must contain userID from Voiceflow');
        }
        
        // Use the database relationship - session_id is now the Voiceflow userID, project_id is the localStorage key
        $this->voiceflowSessions()->updateOrCreate(
            ['session_id' => $voiceflowUserID, 'project_id' => $projectId],
            [
                'value_data' => $valueData,
                'status' => $valueData['status'] ?? 'ACTIVE',
                'source' => $sessionData['source'] ?? 'unknown',
                'session_created_at' => isset($sessionData['created_at']) ? \Carbon\Carbon::parse($sessionData['created_at']) : now(),
                'session_updated_at' => now(),
            ]
        );
    }

    /**
     * Update the value data for a specific session (when localStorage changes).
     * 
     * @param string $projectId The Voiceflow project ID (localStorage key)
     * @param array $valueData The updated localStorage value data
     */
    public function updateSessionLastTurn(string $projectId, array $valueData): void
    {
        // Extract the userID from the updated value data
        $voiceflowUserID = $valueData['userID'] ?? null;
        
        if (!$voiceflowUserID) {
            // If no userID in the new data, try to find the session by project_id alone
            $session = $this->voiceflowSessions()->where('project_id', $projectId)->first();
        } else {
            // Find by both userID and project_id for accuracy
            $session = $this->voiceflowSessions()->where('session_id', $voiceflowUserID)->where('project_id', $projectId)->first();
        }
        
        if ($session) {
            $session->updateValueData($valueData);
        }
    }

    /**
     * Remove a Voiceflow session.
     * 
     * @param string $projectId The Voiceflow project ID (localStorage key)
     * @param string|null $voiceflowUserID Optional Voiceflow userID for more precise deletion
     */
    public function removeSession(string $projectId, ?string $voiceflowUserID = null): void
    {
        $query = $this->voiceflowSessions()->where('project_id', $projectId);
        
        if ($voiceflowUserID) {
            $query->where('session_id', $voiceflowUserID);
        }
        
        $query->delete();
    }

    /**
     * Check if user has any active sessions.
     */
    public function hasActiveSessions(): bool
    {
        // Use the database relationship
        return $this->voiceflowSessions()->exists();
    }

    /**
     * Get the most recent session.
     */
    public function getMostRecentSession(): ?array
    {
        // Use the database relationship with proper ordering
        $sessionModel = $this->voiceflowSessions()->recent()->first();
        
        if ($sessionModel) {
            return [
                'session_id' => $sessionModel->session_id,
                'value' => $sessionModel->value_data,
                'status' => $sessionModel->status,
                'source' => $sessionModel->source,
                'created_at' => $sessionModel->session_created_at?->toISOString(),
                'updated_at' => $sessionModel->session_updated_at?->toISOString(),
            ];
        }
        
        return null;
    }
}
