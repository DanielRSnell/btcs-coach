<div class="space-y-4">
    @if($userId)
        @livewire('team-members-manager', ['userId' => $userId], key('team-manager-' . $userId))
    @else
        <div class="text-sm text-gray-500 dark:text-gray-400">
            No team members (User not saved yet)
        </div>
    @endif
</div>
