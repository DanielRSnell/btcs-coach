<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\VoiceflowSession;
use Illuminate\Console\Command;

class MigrateVoiceflowSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voiceflow:migrate-sessions {--dry-run : Run without actually migrating data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing user session JSON data to the voiceflow_sessions table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Starting Voiceflow session migration...');
        
        // Find users with session data
        $usersWithSessions = User::whereNotNull('sessions')->get();
        
        $this->info("Found {$usersWithSessions->count()} users with session data");
        
        $migrated = 0;
        $skipped = 0;
        $errors = 0;
        
        foreach ($usersWithSessions as $user) {
            $this->info("Processing user: {$user->name} ({$user->email})");
            
            $sessions = $user->sessions ?? [];
            
            foreach ($sessions as $sessionId => $sessionData) {
                try {
                    // Check if session already exists in the new table
                    $existingSession = VoiceflowSession::where('user_id', $user->id)
                        ->where('session_id', $sessionId)
                        ->first();
                        
                    if ($existingSession) {
                        $this->warn("  Skipping session {$sessionId} - already exists in voiceflow_sessions table");
                        $skipped++;
                        continue;
                    }
                    
                    // Determine session data structure (old vs new format)
                    $valueData = null;
                    $status = 'ACTIVE';
                    $source = 'migrated_from_json';
                    $sessionCreatedAt = null;
                    $sessionUpdatedAt = null;
                    
                    // Handle new format (with 'value' field)
                    if (isset($sessionData['value'])) {
                        $valueData = $sessionData['value'];
                        $status = $sessionData['value']['status'] ?? 'ACTIVE';
                        $source = $sessionData['source'] ?? 'migrated_from_json';
                        $sessionCreatedAt = isset($sessionData['created_at']) ? \Carbon\Carbon::parse($sessionData['created_at']) : null;
                        $sessionUpdatedAt = isset($sessionData['updated_at']) ? \Carbon\Carbon::parse($sessionData['updated_at']) : null;
                    }
                    // Handle old format (with 'last_turn' field)
                    elseif (isset($sessionData['last_turn'])) {
                        $valueData = $sessionData['last_turn'];
                        $status = $sessionData['last_turn']['status'] ?? 'ACTIVE';
                        $source = 'migrated_from_legacy_json';
                        $sessionCreatedAt = isset($sessionData['created_at']) ? \Carbon\Carbon::parse($sessionData['created_at']) : null;
                        $sessionUpdatedAt = isset($sessionData['updated_at']) ? \Carbon\Carbon::parse($sessionData['updated_at']) : null;
                    }
                    // Handle direct data format
                    else {
                        $valueData = $sessionData;
                        $status = $sessionData['status'] ?? 'ACTIVE';
                    }
                    
                    if (!$dryRun) {
                        VoiceflowSession::create([
                            'user_id' => $user->id,
                            'session_id' => $sessionId,
                            'value_data' => $valueData,
                            'status' => $status,
                            'source' => $source,
                            'session_created_at' => $sessionCreatedAt,
                            'session_updated_at' => $sessionUpdatedAt,
                        ]);
                    }
                    
                    $this->info("  ✓ Migrated session: {$sessionId} (Status: {$status}, Source: {$source})");
                    $migrated++;
                    
                } catch (\Exception $e) {
                    $this->error("  ✗ Error migrating session {$sessionId}: " . $e->getMessage());
                    $errors++;
                }
            }
        }
        
        $this->info("\nMigration Summary:");
        $this->info("  Migrated: {$migrated}");
        $this->info("  Skipped: {$skipped}");
        $this->info("  Errors: {$errors}");
        
        if ($dryRun) {
            $this->warn("\nDRY RUN - No data was actually migrated.");
            $this->info("Run without --dry-run to perform the migration.");
        } else {
            $this->info("\nMigration completed successfully!");
        }
    }
}
