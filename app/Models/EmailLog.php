<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'recipient_email',
        'recipient_name',
        'subject',
        'body',
        'email_type',
        'status',
        'provider',
        'provider_message_id',
        'error_message',
        'sent_at',
        'delivered_at',
        'opened_at',
        'clicked_at',
        'bounced_at',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'bounced_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the email log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the email was successfully sent
     */
    public function wasSent(): bool
    {
        return $this->status === 'sent';
    }

    /**
     * Check if the email failed to send
     */
    public function failed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if the email was delivered
     */
    public function wasDelivered(): bool
    {
        return !is_null($this->delivered_at);
    }

    /**
     * Check if the email was opened
     */
    public function wasOpened(): bool
    {
        return !is_null($this->opened_at);
    }

    /**
     * Check if any links in the email were clicked
     */
    public function wasClicked(): bool
    {
        return !is_null($this->clicked_at);
    }

    /**
     * Check if the email bounced
     */
    public function bounced(): bool
    {
        return !is_null($this->bounced_at) || $this->status === 'bounced';
    }

    /**
     * Get the status badge color for UI display
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'sent' => 'badge-success',
            'pending' => 'badge-warning',
            'failed' => 'badge-error',
            'bounced' => 'badge-error',
            default => 'badge-outline'
        };
    }

    /**
     * Get the email type badge color for UI display
     */
    public function getTypeBadgeClass(): string
    {
        return match($this->email_type) {
            'verification' => 'badge-info',
            'password_reset' => 'badge-warning',
            'notification' => 'badge-primary',
            'invitation' => 'badge-secondary',
            'emergency' => 'badge-error',
            'reminder' => 'badge-accent',
            default => 'badge-outline'
        };
    }

    /**
     * Get formatted email type for display
     */
    public function getFormattedEmailType(): string
    {
        return match($this->email_type) {
            'verification' => 'Email Verification',
            'password_reset' => 'Password Reset',
            'notification' => 'Notification',
            'invitation' => 'User Invitation',
            'emergency' => 'Emergency Alert',
            'reminder' => 'Reminder',
            'medication_reminder' => 'Medication Reminder',
            'seizure_alert' => 'Seizure Alert',
            default => ucfirst(str_replace('_', ' ', $this->email_type ?? 'Unknown'))
        };
    }

    /**
     * Get the delivery status with timing
     */
    public function getDeliveryStatus(): array
    {
        $status = [
            'sent' => $this->wasSent(),
            'delivered' => $this->wasDelivered(),
            'opened' => $this->wasOpened(),
            'clicked' => $this->wasClicked(),
            'bounced' => $this->bounced(),
            'failed' => $this->failed(),
        ];

        $timeline = [];

        if ($this->sent_at) {
            $timeline[] = ['event' => 'Sent', 'time' => $this->sent_at];
        }

        if ($this->delivered_at) {
            $timeline[] = ['event' => 'Delivered', 'time' => $this->delivered_at];
        }

        if ($this->opened_at) {
            $timeline[] = ['event' => 'Opened', 'time' => $this->opened_at];
        }

        if ($this->clicked_at) {
            $timeline[] = ['event' => 'Clicked', 'time' => $this->clicked_at];
        }

        if ($this->bounced_at) {
            $timeline[] = ['event' => 'Bounced', 'time' => $this->bounced_at];
        }

        return [
            'status' => $status,
            'timeline' => $timeline,
        ];
    }

    /**
     * Scope to filter by email type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('email_type', $type);
    }

    /**
     * Scope to filter by status
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get recent emails
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to get emails for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get emails by recipient email
     */
    public function scopeForEmail($query, string $email)
    {
        return $query->where('recipient_email', $email);
    }

    /**
     * Create a new email log entry
     */
    public static function logEmail(array $data): self
    {
        return self::create([
            'user_id' => $data['user_id'] ?? null,
            'recipient_email' => $data['recipient_email'],
            'recipient_name' => $data['recipient_name'] ?? null,
            'subject' => $data['subject'],
            'body' => $data['body'] ?? null,
            'email_type' => $data['email_type'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'provider' => $data['provider'] ?? null,
            'metadata' => $data['metadata'] ?? [],
        ]);
    }

    /**
     * Mark email as sent
     */
    public function markAsSent(string $messageId = null): void
    {
        $this->update([
            'status' => 'sent',
            'provider_message_id' => $messageId,
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark email as failed
     */
    public function markAsFailed(string $errorMessage = null): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Mark email as delivered
     */
    public function markAsDelivered(): void
    {
        $this->update([
            'delivered_at' => now(),
        ]);
    }

    /**
     * Mark email as opened
     */
    public function markAsOpened(): void
    {
        $this->update([
            'opened_at' => now(),
        ]);
    }

    /**
     * Mark email as clicked
     */
    public function markAsClicked(): void
    {
        $this->update([
            'clicked_at' => now(),
        ]);
    }

    /**
     * Mark email as bounced
     */
    public function markAsBounced(string $reason = null): void
    {
        $this->update([
            'status' => 'bounced',
            'bounced_at' => now(),
            'error_message' => $reason,
        ]);
    }
}
