<?php

namespace App\Filament\Widgets;

use App\Models\VoiceflowSession;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class VoiceflowSessionsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Get basic session statistics
        $totalSessions = VoiceflowSession::count();
        $activeSessions = VoiceflowSession::where('status', 'ACTIVE')->count();
        $completedSessions = VoiceflowSession::where('status', 'COMPLETED')->count();

        // Get user statistics
        $totalUsers = User::count();
        $usersWithSessions = User::whereHas('voiceflowSessions')->count();

        // Get recent activity (last 24 hours)
        $recentActivity = VoiceflowSession::where('updated_at', '>=', Carbon::now()->subDay())->count();

        // Get session distribution by source
        $sourceStats = VoiceflowSession::selectRaw('source, count(*) as count')
            ->groupBy('source')
            ->pluck('count', 'source')
            ->toArray();

        $topSource = collect($sourceStats)->sortDesc()->keys()->first() ?: 'None';

        return [
            Stat::make('Total Sessions', number_format($totalSessions))
                ->description('All coaching conversations')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary'),

            Stat::make('Active Sessions', number_format($activeSessions))
                ->description('Currently ongoing')
                ->descriptionIcon('heroicon-m-play')
                ->color($activeSessions > 0 ? 'success' : 'gray'),

            Stat::make('Completed Sessions', number_format($completedSessions))
                ->description('Finished conversations')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),

            Stat::make('Active Users', number_format($usersWithSessions))
                ->description("of {$totalUsers} total users")
                ->descriptionIcon('heroicon-m-users')
                ->color($usersWithSessions > 0 ? 'success' : 'warning'),

            Stat::make('Recent Activity', number_format($recentActivity))
                ->description('Sessions updated (24h)')
                ->descriptionIcon('heroicon-m-clock')
                ->color($recentActivity > 0 ? 'success' : 'gray'),

            Stat::make('Top Source', $topSource)
                ->description('Primary session origin')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),
        ];
    }

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $pollingInterval = '30s';

    public function getDisplayName(): string
    {
        return 'Voiceflow Sessions Overview';
    }

    public static function canView(): bool
    {
        // Show to all authenticated admin users
        return auth()->check() && auth()->user()?->role === 'admin';
    }
}