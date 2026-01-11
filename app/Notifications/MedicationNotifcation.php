<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\MedicationLog;
use App\Mail\LoggedMailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MedicationNotifcation extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public MedicationLog|array $medications,
        public User $patient,
        public string $type = "single", // 'single' or 'bulk'
        public ?string $period = null,
        public ?string $notes = null,
        public int $count = 1,
    ) {
        //
    }

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
        $mailMessage = new LoggedMailMessage();

        // Determine email type based on action
        $emailType = "medication_taken";
        if ($this->type === "single" && $this->medications->skipped) {
            $emailType = "medication_skipped";
        } elseif ($this->type === "bulk") {
            $emailType = "medication_bulk_taken";
        }

        // Build metadata with normalized, non-null string values
        $metadata = [
            "patient_id" => (string) $this->patient->id,
            "patient_name" => (string) $this->patient->name,
            "notification_type" => (string) $this->type,
            "is_for_patient" =>
                $notifiable->id === $this->patient->id ? "1" : "0",
        ];

        if ($this->type === "bulk") {
            if ($this->period !== null) {
                $metadata["period"] = (string) $this->period;
            }

            $metadata["count"] = (string) $this->count;

            if ($this->notes !== null && $this->notes !== "") {
                $metadata["notes"] = (string) $this->notes;
            }
        } elseif ($this->type === "single") {
            $metadata["medication_id"] =
                (string) $this->medications->medication_id;
            $metadata["medication_name"] =
                (string) ($this->medications->medication->name ?? "Unknown");
            $metadata["skipped"] = $this->medications->skipped ? "1" : "0";
            if (
                $this->medications->skipped &&
                $this->medications->skip_reason !== null
            ) {
                $metadata["skip_reason"] =
                    (string) $this->medications->skip_reason;
            }
        }

        return $mailMessage
            ->emailType($emailType)
            ->forUser($this->patient->id)
            ->withMetadata($metadata)
            ->markdown("mail.medication-notifcation", [
                "medications" => $this->medications,
                "patient" => $this->patient,
                "type" => $this->type,
                "period" => $this->period,
                "notes" => $this->notes,
                "count" => $this->count,
                "isSkipped" =>
                    $this->type === "single" && $this->medications->skipped,
                "recipient" => $notifiable,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if ($this->type === "bulk") {
            return [
                "type" => "medication_bulk_taken",
                "patient_name" => $this->patient->name,
                "period" => $this->period,
                "count" => $this->count,
                "medications" => $this->medications,
                "notes" => $this->notes,
            ];
        }

        return [
            "type" => $this->medications->skipped
                ? "medication_skipped"
                : "medication_taken",
            "patient_name" => $this->patient->name,
            "medication_name" => $this->medications->medication->name,
            "dosage" => $this->medications->dosage_taken,
            "taken_at" => $this->medications->taken_at,
            "timing_info" => $this->medications->getTimeDifference(),
            "is_late" => $this->medications->isTakenLate(),
            "skip_reason" => $this->medications->skip_reason,
            "notes" => $this->medications->notes,
        ];
    }
}
