<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Exception;

class UpdateUserPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * --dry-run : simulate changes without saving to the database
     */

    #php artisan users:update-passwords
    protected $signature = 'users:update-passwords {--dry-run : Simulate changes without saving them}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update passwords: lab users -> ci_respo_lab, non-lab users -> username';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $this->info('Starting password update process' . ($dryRun ? ' (simulation mode)' : '') . '...');

        $total = User::count();
        $this->info("Total users found: {$total}");

        $processed = 0;
        $updated = 0;
        $skipped = 0;
        $errors = 0;

        // Process users in chunks to avoid memory issues
        User::with('laboratorio')->chunk(200, function ($users) use (&$processed, &$updated, &$skipped, &$errors, $dryRun) {
            foreach ($users as $user) {
                $processed++;

                // Determine new password
                $newPasswordPlain = null;
                if ($user->laboratorio) {
                    $lab = $user->laboratorio;
                    $newPasswordPlain = $lab->ci_respo_lab ?? null;
                } else {
                    $newPasswordPlain = $user->username ?? null;
                }

                if (empty($newPasswordPlain)) {
                    $this->warn("User #{$user->id} ({$user->email} / {$user->username}): no valid value for password. Skipped.");
                    $skipped++;
                    continue;
                }

                try {
                    if ($dryRun) {
                        $this->line("DRY RUN - User ID {$user->id}: password would be set to '{$newPasswordPlain}'");
                        $updated++;
                    } else {
                        $user->password = $newPasswordPlain;
                        $user->save();
                        $this->info("User ID {$user->id}: password updated successfully.");
                        $updated++;
                    }
                } catch (Exception $e) {
                    $this->error("Error updating user ID {$user->id}: " . $e->getMessage());
                    $errors++;
                }
            }
        });

        $this->line('--------------------------------');
        $this->info("Processed: {$processed}");
        $this->info("Updated: {$updated}");
        $this->info("Skipped: {$skipped}");
        $this->info("Errors: {$errors}");
        $this->info('Password update process completed.');

        return 0;
    }
}
