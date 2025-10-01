<?php

namespace App\Livewire;

use App\Models\TeamMember;
use App\Models\User;
use Livewire\Component;
use Filament\Notifications\Notification;

class TeamMembersManager extends Component
{
    public $userId;
    public $teamMembers = [];
    public $selectedMembers = [];

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->loadTeamMembers();
    }

    public function loadTeamMembers()
    {
        $user = User::find($this->userId);

        if (!$user || empty($user->org_level_2)) {
            $this->teamMembers = [];
            return;
        }

        // Load team members from database instead of JSON
        $this->teamMembers = TeamMember::where('org_level_2', $user->org_level_2)
            ->where('employee_email', '!=', $user->email) // Exclude the user themselves
            ->orderBy('first_name')
            ->get()
            ->toArray();
    }

    public function createUser($teamMemberId)
    {
        try {
            $teamMember = TeamMember::find($teamMemberId);

            if (!$teamMember) {
                Notification::make()
                    ->danger()
                    ->title('Team member not found')
                    ->send();
                return;
            }

            if ($teamMember->hasAccount()) {
                Notification::make()
                    ->warning()
                    ->title('Account already exists')
                    ->body($teamMember->employee_email . ' already has an account')
                    ->send();
                return;
            }

            $newUser = $teamMember->createUser();

            Notification::make()
                ->success()
                ->title('User created successfully')
                ->body($newUser->name . ' (' . $newUser->email . ') has been created with default password: Welcome2024!')
                ->send();

            $this->loadTeamMembers();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error creating user')
                ->body($e->getMessage())
                ->send();
        }
    }

    public function createSelectedUsers()
    {
        $created = 0;
        $skipped = 0;

        foreach ($this->selectedMembers as $teamMemberId => $selected) {
            if (!$selected) continue;

            $teamMember = TeamMember::find($teamMemberId);

            if (!$teamMember || $teamMember->hasAccount()) {
                $skipped++;
                continue;
            }

            try {
                $teamMember->createUser();
                $created++;
            } catch (\Exception $e) {
                \Log::error('Error creating user from team data: ' . $e->getMessage());
                $skipped++;
            }
        }

        $this->selectedMembers = [];
        $this->loadTeamMembers();

        if ($created > 0) {
            Notification::make()
                ->success()
                ->title("Created {$created} user(s)")
                ->body($skipped > 0 ? "Skipped {$skipped} users (already exist or error)" : null)
                ->send();
        } else {
            Notification::make()
                ->warning()
                ->title('No users created')
                ->body($skipped > 0 ? "All {$skipped} selected users already have accounts or encountered errors" : 'No users were selected')
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.team-members-manager');
    }
}
