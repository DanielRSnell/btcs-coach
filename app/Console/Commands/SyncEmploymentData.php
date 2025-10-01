<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncEmploymentData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employment:sync {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync employment information from team.json to user profiles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting employment data sync...');

        // Load team.json
        $teamJsonPath = public_path('team.json');

        if (!File::exists($teamJsonPath)) {
            $this->error('❌ team.json file not found at: ' . $teamJsonPath);
            return Command::FAILURE;
        }

        $teamData = json_decode(File::get($teamJsonPath), true);

        if (!$teamData) {
            $this->error('❌ Failed to parse team.json');
            return Command::FAILURE;
        }

        $this->info("📋 Loaded " . count($teamData) . " team members from team.json");

        // Index team data by email for quick lookup
        $teamByEmail = [];
        foreach ($teamData as $member) {
            $email = strtolower(trim($member['Employee Email'] ?? ''));
            if ($email) {
                $teamByEmail[$email] = $member;
            }
        }

        // Get all users
        $users = User::all();
        $this->info("👥 Found " . $users->count() . " users in database");

        $updated = 0;
        $notFound = 0;
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be saved');
        }

        foreach ($users as $user) {
            $email = strtolower(trim($user->email));

            if (isset($teamByEmail[$email])) {
                $member = $teamByEmail[$email];

                $this->line("✓ Found match for: {$user->email}");
                $this->line("  → Employee #: {$member['Employee Number']}");
                $this->line("  → Org Level 2: {$member['Org Level 2']}");
                $this->line("  → Job: {$member['Job']}");

                if (!$dryRun) {
                    $user->update([
                        'employee_number' => (string) $member['Employee Number'],
                        'org_level_2' => $member['Org Level 2'],
                        'job' => $member['Job'],
                        'job_code' => $member['Job Code'],
                        'employment_status' => $member['Employment Status'],
                    ]);
                }

                $updated++;
            } else {
                $this->warn("⚠ No match found for: {$user->email}");
                $notFound++;
            }
        }

        $this->newLine();
        $this->info("📊 Summary:");
        $this->info("  ✓ Updated: {$updated} users");
        $this->info("  ⚠ Not found: {$notFound} users");

        if ($dryRun) {
            $this->warn('🔍 This was a dry run - no changes were saved');
            $this->info('Run without --dry-run to save changes');
        } else {
            $this->info('✅ Employment data sync complete!');
        }

        return Command::SUCCESS;
    }
}
