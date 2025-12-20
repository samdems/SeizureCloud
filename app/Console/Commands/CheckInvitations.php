<?php

namespace App\Console\Commands;

use App\Models\UserInvitation;
use Illuminate\Console\Command;

class CheckInvitations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invitations:check {--email= : Filter by email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check pending and recent invitations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');

        $query = UserInvitation::with('inviter');

        if ($email) {
            $query->where('email', 'like', "%{$email}%");
        }

        $invitations = $query->orderBy('created_at', 'desc')->get();

        if ($invitations->isEmpty()) {
            $this->info('No invitations found.');
            return;
        }

        $this->info("Found {$invitations->count()} invitation(s):");
        $this->newLine();

        $headers = ['ID', 'Email', 'From', 'Status', 'Created', 'Expires', 'Token (Preview)'];
        $rows = [];

        foreach ($invitations as $invitation) {
            $rows[] = [
                $invitation->id,
                $invitation->email,
                $invitation->inviter->name ?? 'Unknown',
                ucfirst($invitation->status),
                $invitation->created_at->format('M j, Y g:i A'),
                $invitation->invitation_expires_at->format('M j, Y g:i A') .
                    ($invitation->invitation_expires_at->isPast() ? ' (EXPIRED)' : ''),
                substr($invitation->token, 0, 10) . '...'
            ];
        }

        $this->table($headers, $rows);

        // Show preview URLs for local environment
        if (app()->environment('local')) {
            $this->newLine();
            $this->info('Preview URLs (local only):');
            foreach ($invitations->take(3) as $invitation) {
                $previewUrl = route('invitations.preview', $invitation->token);
                $acceptUrl = route('invitation.show', $invitation->token);
                $this->line("  {$invitation->email}: {$previewUrl}");
                $this->line("  Accept URL: {$acceptUrl}");
                $this->newLine();
            }
        }
    }
}
