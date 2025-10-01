<?php

namespace App\Console\Commands;

use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Console\Command;

class SyncTeamMembers extends Command
{
    protected $signature = 'team:sync {--fresh : Clear existing team members before syncing}';

    protected $description = 'Sync team members from team.json into the database';

    public function handle()
    {
        $this->info('Starting team members sync...');

        if ($this->option('fresh')) {
            $this->warn('Clearing existing team members...');
            TeamMember::truncate();
        }

        $teamJsonPath = public_path('team.json');

        if (!\File::exists($teamJsonPath)) {
            $this->error('team.json file not found at ' . $teamJsonPath);
            return 1;
        }

        $teamData = json_decode(\File::get($teamJsonPath), true);

        if (!$teamData) {
            $this->error('Could not parse team.json file');
            return 1;
        }

        $this->info('Found ' . count($teamData) . ' team members in JSON file');

        $progressBar = $this->output->createProgressBar(count($teamData));
        $progressBar->start();

        $created = 0;
        $updated = 0;
        $errors = 0;

        foreach ($teamData as $member) {
            try {
                // Parse name from "Last, First" format
                $fullName = $member['Employee Name (Last Suffix, First MI)'] ?? '';
                $nameParts = explode(',', $fullName);
                $firstName = count($nameParts) > 1 ? trim($nameParts[1]) : trim($fullName);
                $lastName = count($nameParts) > 1 ? trim($nameParts[0]) : '';

                // Check if user exists with this email
                $user = User::where('email', $member['Employee Email'])->first();

                $wasCreated = !TeamMember::where('employee_email', $member['Employee Email'])->exists();

                TeamMember::updateOrCreate(
                    ['employee_email' => $member['Employee Email']],
                    [
                        'employee_number' => $member['Employee Number'],
                        'employee_name' => $fullName,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'job' => $member['Job'] ?? '',
                        'job_code' => $member['Job Code'] ?? null,
                        'org_level_2' => $member['Org Level 2'] ?? null,
                        'employment_status' => $member['Employment Status'] ?? 'Active',
                        'user_id' => $user?->id,
                    ]
                );

                if ($wasCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            } catch (\Exception $e) {
                $this->error("\nError syncing member {$member['Employee Email']}: " . $e->getMessage());
                $errors++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✓ Sync completed!");
        $this->table(
            ['Status', 'Count'],
            [
                ['Created', $created],
                ['Updated', $updated],
                ['Errors', $errors],
            ]
        );

        return 0;
    }
}
