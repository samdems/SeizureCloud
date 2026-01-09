<?php

namespace App\Listeners;

use App\Models\EmailLog;
use App\Mail\LoggedMailMessage;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mime\Email;

/**
 * Listener that automatically logs emails to the EmailLog model
 *
 * This listener hooks into Laravel's mail events to automatically
 * create and update email logs for all outgoing emails.
 */
class EmailLoggingListener
{
    /**
     * Store temporary email log IDs keyed by message ID
     *
     * @var array
     */
    protected static $pendingLogs = [];

    /**
     * Handle the MessageSending event
     *
     * This creates a pending email log entry before the email is sent
     *
     * @param MessageSending $event
     * @return void
     */
    public function handleMessageSending(MessageSending $event): void
    {
        try {
            $message = $event->message;

            // Extract email details from the message
            $to = $message->getTo();
            $recipientEmail = null;
            $recipientName = null;

            if (!empty($to)) {
                $firstRecipient = array_key_first($to);
                $recipientEmail = $firstRecipient;
                $recipientName = $to[$firstRecipient] ?? null;
            }

            if (!$recipientEmail) {
                Log::warning("Email sending without recipient", [
                    "subject" => $message->getSubject(),
                ]);
                return;
            }

            // Extract the body
            $body = $this->extractEmailBody($message);

            // Get email type and metadata from LoggedMailMessage if available
            $emailType = "notification";
            $userId = null;
            $metadata = [];

            // Try to extract data from the original notification
            if (isset($event->data["__laravel_notification"])) {
                $notification = $event->data["__laravel_notification"];

                // Check if notification used LoggedMailMessage
                if (method_exists($notification, "toMail")) {
                    try {
                        $notifiable =
                            $event->data["__laravel_notification_notifiable"] ??
                            null;
                        if ($notifiable) {
                            $mailMessage = $notification->toMail($notifiable);

                            if ($mailMessage instanceof LoggedMailMessage) {
                                $emailType =
                                    $mailMessage->getEmailType() ??
                                    "notification";
                                $userId = $mailMessage->getUserId();
                                $metadata = $mailMessage->getMetadata();
                            }
                        }
                    } catch (\Exception $e) {
                        Log::debug("Could not extract LoggedMailMessage data", [
                            "error" => $e->getMessage(),
                        ]);
                    }
                }

                // Try to infer email type from notification class name
                if ($emailType === "notification") {
                    $emailType = $this->inferEmailTypeFromNotification(
                        $notification,
                    );
                }

                // Try to get user ID from notifiable
                if (
                    !$userId &&
                    isset($event->data["__laravel_notification_notifiable"])
                ) {
                    $notifiable =
                        $event->data["__laravel_notification_notifiable"];
                    if (method_exists($notifiable, "getKey")) {
                        $userId = $notifiable->getKey();
                    }
                }
            }

            // Ensure metadata values are strings and filter out nulls
            $filteredMetadata = array_filter(
                array_map(function ($value) {
                    if (is_bool($value)) {
                        return $value ? "true" : "false";
                    }
                    if (is_numeric($value)) {
                        return (string) $value;
                    }
                    return $value;
                }, $metadata),
                function ($value) {
                    return $value !== null;
                },
            );

            // Create the email log entry
            $emailLog = EmailLog::create([
                "user_id" => $userId,
                "recipient_email" => $recipientEmail,
                "recipient_name" => $recipientName,
                "subject" => $message->getSubject(),
                "body" => $body,
                "email_type" => $emailType,
                "status" => "pending",
                "provider" => config("mail.default"),
                "metadata" => $filteredMetadata,
            ]);

            // Store the log ID for later use when the email is sent
            $messageId = $this->generateMessageKey($message);
            self::$pendingLogs[$messageId] = $emailLog->id;

            Log::info("Email log created", [
                "email_log_id" => $emailLog->id,
                "recipient" => $recipientEmail,
                "subject" => $message->getSubject(),
                "type" => $emailType,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to create email log", [
                "error" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Handle the MessageSent event
     *
     * This updates the email log entry to mark it as sent
     *
     * @param MessageSent $event
     * @return void
     */
    public function handleMessageSent(MessageSent $event): void
    {
        try {
            $message = $event->message;
            $messageId = $this->generateMessageKey($message);

            // Find the pending log for this message
            if (isset(self::$pendingLogs[$messageId])) {
                $emailLogId = self::$pendingLogs[$messageId];
                $emailLog = EmailLog::find($emailLogId);

                if ($emailLog) {
                    // Get provider message ID if available
                    $providerMessageId = $message
                        ->getHeaders()
                        ->get("Message-ID")
                        ?->getBodyAsString();

                    $emailLog->markAsSent($providerMessageId);

                    Log::info("Email log updated as sent", [
                        "email_log_id" => $emailLog->id,
                        "provider_message_id" => $providerMessageId,
                    ]);
                }

                // Clean up the pending log
                unset(self::$pendingLogs[$messageId]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to update email log as sent", [
                "error" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Extract the email body from the message
     *
     * @param Email $message
     * @return string|null
     */
    protected function extractEmailBody(Email $message): ?string
    {
        try {
            // Try to get HTML body first
            $htmlBody = $message->getHtmlBody();
            if ($htmlBody) {
                return $htmlBody;
            }

            // Fall back to text body
            $textBody = $message->getTextBody();
            if ($textBody) {
                return $textBody;
            }

            return null;
        } catch (\Exception $e) {
            Log::debug("Could not extract email body", [
                "error" => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Generate a unique key for the message
     *
     * @param Email $message
     * @return string
     */
    protected function generateMessageKey(Email $message): string
    {
        $to = $message->getTo();
        $recipientEmail = !empty($to) ? array_key_first($to) : "unknown";

        return md5($recipientEmail . $message->getSubject() . microtime());
    }

    /**
     * Infer email type from notification class name
     *
     * @param object $notification
     * @return string
     */
    protected function inferEmailTypeFromNotification(
        object $notification,
    ): string {
        $className = class_basename($notification);

        // Map notification class names to email types
        $typeMap = [
            "MedicationReminderNotification" => "medication_reminder",
            "MedicationNotifcation" => "medication_taken",
            "MedicationTakenNotification" => "medication_taken",
            "BulkMedicationTakenNotification" => "medication_taken",
            "SeizureAddedNotification" => "seizure_alert",
            "TrustedContactInvitation" => "invitation",
            "VerifyEmail" => "verification",
            "ResetPassword" => "password_reset",
        ];

        return $typeMap[$className] ?? "notification";
    }

    /**
     * Register the listeners for the subscriber
     *
     * @param \Illuminate\Events\Dispatcher $events
     * @return array
     */
    public function subscribe($events): array
    {
        return [
            MessageSending::class => "handleMessageSending",
            MessageSent::class => "handleMessageSent",
        ];
    }
}
