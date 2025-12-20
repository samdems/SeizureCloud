<?php

namespace App\Services;

use App\Models\EmailLog;
use App\Models\User;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class EmailLoggingService
{
    /**
     * Log an email being sent
     */
    public function logEmail(array $data): EmailLog
    {
        try {
            return EmailLog::logEmail([
                'user_id' => $data['user_id'] ?? null,
                'recipient_email' => $data['recipient_email'],
                'recipient_name' => $data['recipient_name'] ?? null,
                'subject' => $data['subject'],
                'body' => $data['body'] ?? null,
                'email_type' => $data['email_type'] ?? 'notification',
                'status' => 'pending',
                'provider' => config('mail.default'),
                'metadata' => $data['metadata'] ?? [],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log email', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Log an email verification attempt
     */
    public function logEmailVerification(User $user): EmailLog
    {
        return $this->logEmail([
            'user_id' => $user->id,
            'recipient_email' => $user->email,
            'recipient_name' => $user->name,
            'subject' => 'Verify Email Address',
            'email_type' => 'verification',
            'metadata' => [
                'verification_type' => 'email_verification',
            ],
        ]);
    }

    /**
     * Log a password reset email
     */
    public function logPasswordReset(string $email, string $name = null): EmailLog
    {
        $user = User::where('email', $email)->first();

        return $this->logEmail([
            'user_id' => $user?->id,
            'recipient_email' => $email,
            'recipient_name' => $name,
            'subject' => 'Reset Password',
            'email_type' => 'password_reset',
            'metadata' => [
                'reset_type' => 'password_reset',
            ],
        ]);
    }

    /**
     * Log a user invitation email
     */
    public function logUserInvitation(string $email, User $inviter, string $invitationType = 'trusted_contact'): EmailLog
    {
        return $this->logEmail([
            'user_id' => null, // No user ID since they don't exist yet
            'recipient_email' => $email,
            'recipient_name' => null,
            'subject' => 'Invitation to Epilepsy Diary',
            'email_type' => 'invitation',
            'metadata' => [
                'inviter_id' => $inviter->id,
                'inviter_name' => $inviter->name,
                'invitation_type' => $invitationType,
            ],
        ]);
    }

    /**
     * Log a medication reminder email
     */
    public function logMedicationReminder(User $user, array $medications): EmailLog
    {
        return $this->logEmail([
            'user_id' => $user->id,
            'recipient_email' => $user->email,
            'recipient_name' => $user->name,
            'subject' => 'Medication Reminder',
            'email_type' => 'medication_reminder',
            'metadata' => [
                'medication_count' => count($medications),
                'medications' => array_map(function($med) {
                    return [
                        'name' => $med['name'] ?? 'Unknown',
                        'time' => $med['time'] ?? null,
                    ];
                }, $medications),
            ],
        ]);
    }

    /**
     * Log a seizure alert email
     */
    public function logSeizureAlert(User $user, array $seizureData, array $recipients = []): EmailLog
    {
        // Log for the user themselves
        $emailLog = $this->logEmail([
            'user_id' => $user->id,
            'recipient_email' => $user->email,
            'recipient_name' => $user->name,
            'subject' => 'Emergency Seizure Alert',
            'email_type' => 'emergency',
            'metadata' => [
                'alert_type' => 'seizure_emergency',
                'seizure_data' => $seizureData,
                'recipients' => $recipients,
            ],
        ]);

        // Log for trusted contacts
        foreach ($recipients as $recipient) {
            $this->logEmail([
                'user_id' => $user->id,
                'recipient_email' => $recipient['email'],
                'recipient_name' => $recipient['name'] ?? null,
                'subject' => "Emergency Alert for {$user->name}",
                'email_type' => 'emergency',
                'metadata' => [
                    'alert_type' => 'seizure_emergency',
                    'patient_name' => $user->name,
                    'seizure_data' => $seizureData,
                ],
            ]);
        }

        return $emailLog;
    }

    /**
     * Log a general notification email
     */
    public function logNotification(User $user, string $subject, string $type = 'notification', array $metadata = []): EmailLog
    {
        return $this->logEmail([
            'user_id' => $user->id,
            'recipient_email' => $user->email,
            'recipient_name' => $user->name,
            'subject' => $subject,
            'email_type' => $type,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Mark an email log as sent
     */
    public function markAsSent(EmailLog $emailLog, string $messageId = null): void
    {
        try {
            $emailLog->markAsSent($messageId);
        } catch (\Exception $e) {
            Log::error('Failed to mark email as sent', [
                'email_log_id' => $emailLog->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Mark an email log as failed
     */
    public function markAsFailed(EmailLog $emailLog, string $errorMessage = null): void
    {
        try {
            $emailLog->markAsFailed($errorMessage);
        } catch (\Exception $e) {
            Log::error('Failed to mark email as failed', [
                'email_log_id' => $emailLog->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get email statistics for a user
     */
    public function getUserEmailStats(User $user): array
    {
        $emailLogs = $user->emailLogs();

        return [
            'total_emails' => $emailLogs->count(),
            'sent_emails' => $emailLogs->withStatus('sent')->count(),
            'failed_emails' => $emailLogs->withStatus('failed')->count(),
            'pending_emails' => $emailLogs->withStatus('pending')->count(),
            'bounced_emails' => $emailLogs->withStatus('bounced')->count(),
            'recent_emails' => $emailLogs->recent(7)->count(),
            'verification_emails' => $emailLogs->ofType('verification')->count(),
            'notification_emails' => $emailLogs->ofType('notification')->count(),
            'emergency_emails' => $emailLogs->ofType('emergency')->count(),
        ];
    }

    /**
     * Get system-wide email statistics
     */
    public function getSystemEmailStats(): array
    {
        return [
            'total_emails' => EmailLog::count(),
            'sent_emails' => EmailLog::withStatus('sent')->count(),
            'failed_emails' => EmailLog::withStatus('failed')->count(),
            'pending_emails' => EmailLog::withStatus('pending')->count(),
            'bounced_emails' => EmailLog::withStatus('bounced')->count(),
            'recent_emails' => EmailLog::recent(7)->count(),
            'today_emails' => EmailLog::whereDate('created_at', today())->count(),
            'this_week_emails' => EmailLog::where('created_at', '>=', now()->startOfWeek())->count(),
            'this_month_emails' => EmailLog::where('created_at', '>=', now()->startOfMonth())->count(),
        ];
    }

    /**
     * Get email type breakdown
     */
    public function getEmailTypeBreakdown(): array
    {
        return EmailLog::selectRaw('email_type, COUNT(*) as count')
            ->groupBy('email_type')
            ->pluck('count', 'email_type')
            ->toArray();
    }

    /**
     * Clean up old email logs
     */
    public function cleanupOldLogs(int $daysToKeep = 90): int
    {
        try {
            $cutoffDate = now()->subDays($daysToKeep);

            $deletedCount = EmailLog::where('created_at', '<', $cutoffDate)
                ->where('status', '!=', 'failed') // Keep failed emails longer for debugging
                ->delete();

            Log::info("Cleaned up {$deletedCount} old email logs older than {$daysToKeep} days");

            return $deletedCount;
        } catch (\Exception $e) {
            Log::error('Failed to cleanup old email logs', [
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    /**
     * Register email event listeners
     */
    public function registerEventListeners(): void
    {
        // Listen for email sending events
        Event::listen(MessageSending::class, function ($event) {
            // This would be called when an email is about to be sent
            // We could extract email details and create a log entry here
        });

        // Listen for email sent events
        Event::listen(MessageSent::class, function ($event) {
            // This would be called when an email has been sent successfully
            // We could update the log entry status here
        });
    }
}
