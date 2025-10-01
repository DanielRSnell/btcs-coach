<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model
{
    protected $fillable = [
        'employee_number',
        'employee_name',
        'first_name',
        'last_name',
        'employee_email',
        'job',
        'job_code',
        'org_level_2',
        'employment_status',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasAccount(): bool
    {
        return $this->user_id !== null;
    }

    public function getNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function createUser(string $defaultPassword = 'Welcome2024!'): User
    {
        // Convert back to team.json format for createFromTeamData
        $teamData = [
            'Employee Name (Last Suffix, First MI)' => $this->employee_name,
            'Employee Email' => $this->employee_email,
            'Employee Number' => $this->employee_number,
            'Job' => $this->job,
            'Job Code' => $this->job_code,
            'Org Level 2' => $this->org_level_2,
            'Employment Status' => $this->employment_status,
        ];

        $user = User::createFromTeamData($teamData, $defaultPassword);

        // Link the team member to the user
        $this->user_id = $user->id;
        $this->save();

        return $user;
    }
}
