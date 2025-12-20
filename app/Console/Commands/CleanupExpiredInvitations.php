<?php

namespace App\Console\Commands;

use App\Models\UserInvitation;
use Illuminate\Console\Command;

class CleanupExpiredInvitations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invitations:cleanup
                            {--dry-run : Show what would be cleaned up without making changes}
                            {--days=30 : Delete invitations older than this many days (default: 30)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired and old invitations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $olderThanDays = (int) $this->option('days');

        $this->info("Cleaning up expired invitations" . ($dryRun ? ' (dry run)' : ''));
        $this->newLine();

        // Mark pending invitations as expired if they're past their expiration date
        $expiredQuery = UserInvitation::where('status', 'pending')
            ->where('invitation_expires_at', '<', now());

        $expiredCount = $expiredQuery->count();

        if ($expiredCount > 0) {
            $this->info("Found {$expiredCount} pending invitations that have expired:");

            $expiredInvitations = $expiredQuery->get();
            foreach ($expiredInvitations as $invitation) {
                $this->line("  - {$invitation->email} (invited by {$invitation->inviter->name}, expired {$invitation->invitation_expires_at->diffForHumans()})");
            }

            if (!$dryRun) {
                $expiredQuery->update(['status' => 'expired']);
                $this->info("✓ Marked {$expiredCount} invitations as expired");
            } else {
                $this->comment("Would mark {$expiredCount} invitations as expired");
            }
        } else {
            $this->info("No pending invitations found that have expired");
        }

        $this->newLine();

        // Delete old invitations (accepted, cancelled, or expired) older than specified days
        $oldInvitationsQuery = UserInvitation::whereIn('status', ['accepted', 'cancelled', 'expired'])
            ->where('updated_at', '<', now()->subDays($olderThanDays));

        $oldCount = $oldInvitationsQuery->count();

        if ($oldCount > 0) {
            $this->info("Found {$oldCount} old invitations (older than {$olderThanDays} days):");

            $oldInvitations = $oldInvitationsQuery->get();
            foreach ($oldInvitations->take(10) as $invitation) {
                $this->line("  - {$invitation->email} ({$invitation->status}, {$invitation->updated_at->diffForHumans()})");
            }

            if ($oldInvitations->count() > 10) {
                $remaining = $oldInvitations->count() - 10;
                $this->line("  ... and {$remaining} more");
            }

            if (!$dryRun) {
                $deletedCount = $oldInvitationsQuery->delete();
                $this->info("✓ Deleted {$deletedCount} old invitations");
            } else {
                $this->comment("Would delete {$oldCount} old invitations");
            }
        } else {
            $this->info("No old invitations found to delete");
        }

        $this->newLine();

        // Summary
        $totalPendingInvitations = UserInvitation::where('status', 'pending')->count();
        $totalValidInvitations = UserInvitation::where('status', 'pending')
            ->where('invitation_expires_at', '>', now())
            ->count();

        $this->info("Summary:");
        $this->line("  Total pending invitations: {$totalPendingInvitations}");
        $this->line("  Valid (non-expired) pending: {$totalValidInvitations}");

        if ($totalPendingInvitations > $totalValidInvitations) {
            $expiredPending = $totalPendingInvitations - $totalValidInvitations;
            $this->warn("  Expired but still pending: {$expiredPending}");
            $this->comment("  Run this command without --dry-run to clean them up");
        }

        if ($dryRun) {
            $this->newLine();
            $this->comment("This was a dry run. No changes were made.");
            $this->comment("Run without --dry-run to actually perform the cleanup.");
        }

        return Command::SUCCESS;
    }
}
