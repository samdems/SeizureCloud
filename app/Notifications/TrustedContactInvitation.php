<?php

namespace App\Notifications;

use App\Models\UserInvitation;
use App\Models\User;
use App\Mail\LoggedMailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrustedContactInvitation extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(
        public UserInvitation $invitation,
        public User $inviter,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ["mail"];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $appName = config("app.name");
        $inviterName = $this->inviter->name;
        $invitationUrl = $this->invitation->getInvitationUrl();
        $expirationDate = $this->invitation->invitation_expires_at->format(
            'M j, Y \a\t g:i A',
        );

        $mailMessage = new LoggedMailMessage();
        $mailMessage
            ->subject(
                "You've been invited to access {$inviterName}'s health records",
            )
            ->emailType("invitation")
            ->forUser($this->inviter->id)
            ->withMetadata([
                "invitation_id" => $this->invitation->id,
                "inviter_id" => $this->inviter->id,
                "inviter_name" => $this->inviter->name,
                "invitation_type" =>
                    $this->invitation->invitation_type ?? "trusted_contact",
                "nickname" => $this->invitation->nickname,
                "expires_at" => $this->invitation->invitation_expires_at->toIso8601String(),
            ]);
        $mailMessage->greeting("Hello!");
        $mailMessage->line(
            "{$inviterName} has invited you to become a trusted contact on {$appName}.",
        );
        $mailMessage->line("As a trusted contact, you'll be able to:");
        $mailMessage->line(
            "• View {$inviterName}'s seizure records and emergency status",
        );
        $mailMessage->line("• See medication schedules and logs");
        $mailMessage->line("• Access vital signs data");
        $mailMessage->line("• Monitor health information during emergencies");

        if ($this->invitation->nickname) {
            $mailMessage->line(
                "You've been added as: **{$this->invitation->nickname}**",
            );
        }

        if ($this->invitation->access_note) {
            $mailMessage->line(
                "Note from {$inviterName}: \"{$this->invitation->access_note}\"",
            );
        }

        $mailMessage->action("Accept Invitation", $invitationUrl);
        $mailMessage->line("This invitation will expire on {$expirationDate}.");
        $mailMessage->line(
            "If you already have an account on {$appName}, clicking the link above will automatically grant you access. If you don't have an account, you'll be guided through creating one.",
        );
        $mailMessage->line(
            "If you don't want to accept this invitation, you can simply ignore this email.",
        );
        $mailMessage->line(
            "Thank you for helping to support {$inviterName}'s health management!",
        );

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            "invitation_id" => $this->invitation->id,
            "inviter_id" => $this->inviter->id,
            "inviter_name" => $this->inviter->name,
            "nickname" => $this->invitation->nickname,
            "expires_at" => $this->invitation->invitation_expires_at,
        ];
    }
}
