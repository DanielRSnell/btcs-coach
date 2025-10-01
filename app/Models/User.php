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
        'employee_number',
        'org_level_2',
        'job',
        'job_code',
        'employment_status',
        'pi_behavioral_pattern_id',
        'pi_raw_scores',
        'pi_assessed_at',
        'pi_assessor_name',
        'pi_notes',
        'pi_chart_image',
        'pi_document',
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
     * Get the full URL for the PI chart image.
     */
    public function getPiChartImageUrl(): ?string
    {
        if (!$this->pi_chart_image) {
            return null;
        }

        return \Storage::disk('s3')->url($this->pi_chart_image);
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
        
        \Log::debug('🔍 User getSessions called', [
            'user_id' => $this->id,
            'session_models_count' => $sessionModels->count()
        ]);
        
        if ($sessionModels->isEmpty()) {
            \Log::info('📭 No sessions found for user', ['user_id' => $this->id]);
            return [];
        }
        
        // Convert to the format expected by the frontend
        // Key by session_id (userID) since each chat session has a unique userID
        // This ensures we show ALL sessions, not just one per project_id
        return $sessionModels->keyBy('session_id')->map(function ($session) {
            return [
                'session_id' => $session->session_id, // Voiceflow userID (the actual unique identifier)
                'name' => $session->name, // Custom session name
                'project_id' => $session->project_id, // localStorage key (might be same for multiple sessions)
                'value' => $session->value_data,
                'status' => $session->status,
                'source' => $session->source,
                'created_at' => $session->session_created_at?->toISOString(),
                'updated_at' => $session->session_updated_at?->toISOString(),
            ];
        })->toArray();
        
        \Log::debug('✅ User getSessions result', [
            'user_id' => $this->id,
            'result_count' => count($result),
            'result_keys' => array_keys($result)
        ]);
        
        return $result;
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
            'name' => $sessionModel->name, // Custom session name
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
    public function setSession(string $projectId, array $sessionData, ?string $sessionName = null): void
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
                'name' => $sessionName,
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

    /**
     * Get team members from database by Org Level 2.
     * Returns all team members that share the same Org Level 2 as the current user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teamMembersByOrgLevel()
    {
        return $this->hasMany(\App\Models\TeamMember::class, 'org_level_2', 'org_level_2');
    }

    /**
     * Get team members from team.json by Org Level 2.
     * Returns all team members that share the same Org Level 2 as the current user.
     *
     * @param string|null $orgLevel2 Optional Org Level 2 to search for. If null, uses the user's own Org Level 2.
     * @return array Array of team member data from team.json
     */
    public static function getTeamMembers(?string $orgLevel2 = null): array
    {
        // If no orgLevel2 provided and we're called on an instance, use the user's org level
        if (is_null($orgLevel2) && isset($this)) {
            $orgLevel2 = $this->org_level_2;
        }

        // If still no orgLevel2, return empty array
        if (empty($orgLevel2)) {
            return [];
        }

        // Load team.json if it exists
        $teamJsonPath = public_path('team.json');

        if (!\File::exists($teamJsonPath)) {
            // If team.json doesn't exist, use database instead
            return \App\Models\TeamMember::where('org_level_2', $orgLevel2)
                ->get()
                ->toArray();
        }

        $teamData = json_decode(\File::get($teamJsonPath), true);

        if (!$teamData) {
            \Log::warning('Failed to parse team.json');
            return [];
        }

        // Filter team members by Org Level 2
        $teamMembers = array_filter($teamData, function ($member) use ($orgLevel2) {
            return isset($member['Org Level 2']) &&
                   trim($member['Org Level 2']) === trim($orgLevel2);
        });

        // Re-index array to start from 0
        return array_values($teamMembers);
    }

    /**
     * Get the user's team members based on their Org Level 2 from database.
     *
     * @return array Array of team member data
     */
    public function getMyTeamMembers(): array
    {
        if (empty($this->org_level_2)) {
            return [];
        }

        return \App\Models\TeamMember::where('org_level_2', $this->org_level_2)
            ->get()
            ->toArray();
    }

    /**
     * Get team members with account status information.
     * Checks if each team member has an existing user account.
     *
     * @param string|null $orgLevel2 Optional Org Level 2 to search for
     * @return array Array of team members with 'has_account' and 'user_id' fields
     */
    public static function getTeamMembersWithAccountStatus(?string $orgLevel2 = null): array
    {
        if (empty($orgLevel2)) {
            return [];
        }

        // Get team members from database
        $teamMembers = \App\Models\TeamMember::where('org_level_2', $orgLevel2)->get();

        return $teamMembers->map(function ($member) {
            return [
                'employee_number' => $member->employee_number,
                'employee_name' => $member->employee_name,
                'first_name' => $member->first_name,
                'last_name' => $member->last_name,
                'employee_email' => $member->employee_email,
                'job' => $member->job,
                'job_code' => $member->job_code,
                'org_level_2' => $member->org_level_2,
                'employment_status' => $member->employment_status,
                'has_account' => $member->hasAccount(),
                'user_id' => $member->user_id,
            ];
        })->toArray();
    }

    /**
     * Get the user's team members with account status.
     *
     * @return array Array of team member data with account status
     */
    public function getMyTeamMembersWithAccountStatus(): array
    {
        return self::getTeamMembersWithAccountStatus($this->org_level_2);
    }

    /**
     * Create a user account from team.json data.
     *
     * @param array $teamMemberData Team member data from team.json
     * @param string $defaultPassword Default password for new accounts
     * @return User
     */
    public static function createFromTeamData(array $teamMemberData, string $defaultPassword = 'Welcome2024!'): User
    {
        $email = $teamMemberData['Employee Email'];
        // Parse name from "Last Suffix, First MI" format
        $fullName = $teamMemberData['Employee Name (Last Suffix, First MI)'] ?? '';
        $nameParts = explode(',', $fullName);
        $name = count($nameParts) > 1
            ? trim($nameParts[1]) . ' ' . trim($nameParts[0])  // First Last
            : trim($fullName);

        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($defaultPassword),
            'role' => 'member',
            'employee_number' => (string) $teamMemberData['Employee Number'],
            'org_level_2' => $teamMemberData['Org Level 2'],
            'job' => $teamMemberData['Job'],
            'job_code' => $teamMemberData['Job Code'],
            'employment_status' => $teamMemberData['Employment Status'],
        ]);
    }
}
