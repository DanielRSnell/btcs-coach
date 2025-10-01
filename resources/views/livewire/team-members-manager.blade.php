<div class="space-y-4">
    @if(empty($teamMembers))
        <div class="text-sm text-gray-500 dark:text-gray-400">
            No team members found. User needs an Org Level 2 assigned.
        </div>
    @else
        <div class="space-y-2">
            <div class="flex items-center justify-between mb-3">
                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Team Members ({{ count($teamMembers) }})
                </div>
                @if(collect($teamMembers)->whereNull('user_id')->count() > 0)
                    <button
                        wire:click="createSelectedUsers"
                        type="button"
                        class="text-xs px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white rounded-md transition-colors"
                    >
                        Create Selected Users
                    </button>
                @endif
            </div>

            <div class="space-y-2 max-h-96 overflow-y-auto">
                @foreach($teamMembers as $member)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                        @if(!$member['user_id'])
                            <input
                                type="checkbox"
                                wire:model="selectedMembers.{{ $member['id'] }}"
                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700"
                            />
                        @else
                            <div class="w-4"></div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-sm text-gray-900 dark:text-gray-100">
                                    {{ $member['first_name'] }} {{ $member['last_name'] }}
                                </span>
                                @if($member['user_id'])
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-success-100 text-success-800 dark:bg-success-800 dark:text-success-100">
                                        ✓ Has Account
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-warning-100 text-warning-800 dark:bg-warning-800 dark:text-warning-100">
                                        No Account
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ $member['employee_email'] ?? 'No email' }} • {{ $member['job'] ?? 'No job title' }}
                            </div>
                        </div>

                        @if(!$member['user_id'])
                            <button
                                wire:click="createUser({{ $member['id'] }})"
                                type="button"
                                class="text-xs px-2.5 py-1 bg-primary-600 hover:bg-primary-700 text-white rounded transition-colors whitespace-nowrap"
                            >
                                Create User
                            </button>
                        @else
                            <a
                                href="{{ route('filament.admin.resources.users.edit', ['record' => $member['user_id']]) }}"
                                target="_blank"
                                class="text-xs px-2.5 py-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded transition-colors whitespace-nowrap"
                            >
                                View User
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
