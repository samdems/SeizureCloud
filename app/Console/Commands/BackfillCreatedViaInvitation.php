<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Console\Command;

class BackfillCreatedViaInvitation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:backfill-invitation-flag {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill the created_via_invitation flag for existing users who were created through invitations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $this->info('Backfilling created_via_invitation flag for existing users...');
        $this->newLine();

        // Find users who have accepted invitations but don't have the flag set
        $usersCreatedThroughInvitations = User::whereHas('sentInvitations', function ($query) {
            // This gets users who have invitations, but we want users who ACCEPTED invitations
            // So we need a different approach
        })->get();

        // Better approach: Find users who were the accepted_user_id in invitations
        $acceptedInvitations = UserInvitation::where('status', 'accepted')
            ->whereNotNull('accepted_user_id')
            ->with('acceptedUser')
            ->get();

        if ($acceptedInvitations->isEmpty()) {
            $this->info('No users found who were created through invitations.');
            return;
        }

        $this->info("Found {$acceptedInvitations->count()} invitation(s) that were accepted.");
        $this->newLine();

        $updatedCount = 0;
        $alreadyMarkedCount = 0;
        $userNotFoundCount = 0;

        foreach ($acceptedInvitations as $invitation) {
            $user = $invitation->acceptedUser;

            if (!$user) {
                $userNotFoundCount++;
                $this->warn("User not found for invitation ID: {$invitation->id}");
                continue;
            }

            if ($user->created_via_invitation) {
                $alreadyMarkedCount++;
                $this->line("  ✓ {$user->email} - already marked as created via invitation");
                continue;
            }

            if ($dryRun) {
                $this->line("  → Would mark {$user->email} as created via invitation (Invitation ID: {$invitation->id})");
                $updatedCount++;
            } else {
                $user->update(['created_via_invitation' => true]);
                $this->line("  ✓ Marked {$user->email} as created via invitation (Invitation ID: {$invitation->id})");
                $updatedCount++;
            }
        }

        $this->newLine();
        $this->info('Summary:');

        if ($dryRun) {
            $this->line("  → {$updatedCount} user(s) would be updated");
        } else {
            $this->line("  ✓ {$updatedCount} user(s) updated successfully");
        }

        $this->line("  - {$alreadyMarkedCount} user(s) already marked correctly");

        if ($userNotFoundCount > 0) {
            $this->line("  ⚠ {$userNotFoundCount} invitation(s) had missing users");
        }

        if ($dryRun && $updatedCount > 0) {
            $this->newLine();
            $this->comment('Run without --dry-run to actually update the database.');
        }

        if (!$dryRun && $updatedCount > 0) {
            $this->newLine();
            $this->info('✅ Backfill completed successfully!');
        }
    }
}
