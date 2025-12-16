<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class BulkMedicationTakenNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public array $medications,
        public User $patient,
        public string $period,
        public \Carbon\Carbon $takenAt,
        public ?string $notes = null,
        public int $count = 0,
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
        return ["database", "mail"];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $isPatient = $notifiable->id === $this->patient->id;
        $periodLabel = ucfirst($this->period);

        if ($isPatient) {
            $subject = "Bulk Medications Taken - {$periodLabel}";
            $greeting = "Hello {$notifiable->name}";
            $mainMessage = "This is a confirmation that you have taken your {$this->period} medications.";
        } else {
            $subject = "Bulk Medications Taken - {$this->patient->name}";
            $greeting = "Hello {$notifiable->name}";
            $mainMessage = "This is a notification that {$this->patient->name} has taken their {$this->period} medications.";
        }

        $takenTime = $this->takenAt->format("M j, Y g:i A");

        $mailMessage = new MailMessage();
        $mailMessage->subject($subject);
        $mailMessage->greeting($greeting);
        $mailMessage->line($mainMessage);
        $mailMessage->line("**Period:** {$periodLabel}");
        $mailMessage->line("**Number of medications:** {$this->count}");
        $mailMessage->line("**Taken at:** {$takenTime}");

        // List medications with timing information
        if (!empty($this->medications)) {
            // Add timing summary
            $onTimeCount = 0;
            $lateCount = 0;
            $earlyCount = 0;

            foreach ($this->medications as $medication) {
                if (isset($medication["timing_info"])) {
                    if ($medication["timing_info"] === "On time") {
                        $onTimeCount++;
                    } elseif (
                        isset($medication["is_late"]) &&
                        $medication["is_late"]
                    ) {
                        $lateCount++;
                    } else {
                        $earlyCount++;
                    }
                }
            }

            // Display timing summary if we have timing info
            if ($onTimeCount + $lateCount + $earlyCount > 0) {
                $timingSummary = [];
                if ($onTimeCount > 0) {
                    $timingSummary[] = "{$onTimeCount} on time";
                }
                if ($lateCount > 0) {
                    $timingSummary[] = "{$lateCount} late";
                }
                if ($earlyCount > 0) {
                    $timingSummary[] = "{$earlyCount} early";
                }

                $mailMessage->line(
                    "**Timing Summary:** " . implode(", ", $timingSummary),
                );
            }

            $mailMessage->line("**Medications taken:**");
            foreach ($this->medications as $medication) {
                $medicationInfo = "• {$medication["name"]} - {$medication["dosage"]}";

                if (
                    isset($medication["timing_info"]) &&
                    $medication["timing_info"]
                ) {
                    $medicationInfo .= " ({$medication["timing_info"]})";
                }

                $mailMessage->line($medicationInfo);
            }

            // Add encouraging message based on timing performance
            if ($onTimeCount + $lateCount + $earlyCount > 0) {
                if ($lateCount === 0 && $earlyCount === 0) {
                    $mailMessage->line("🎉 Perfect timing on all medications!");
                } elseif ($onTimeCount >= $lateCount + $earlyCount) {
                    $mailMessage->line("💪 Great medication adherence!");
                } else {
                    $mailMessage->line(
                        "👍 Keep up the good work with your medication routine!",
                    );
                }
            }
        }

        if ($this->notes) {
            $mailMessage->line("**Notes:** {$this->notes}");
        }

        $mailMessage->action(
            "View Medication Schedule",
            route("medications.schedule"),
        );
        $mailMessage->line(
            "Great job staying on track with your medication schedule!",
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
        $isPatient = $notifiable->id === $this->patient->id;
        $periodLabel = ucfirst($this->period);

        // Calculate timing summary
        $onTimeCount = 0;
        $lateCount = 0;
        $earlyCount = 0;

        foreach ($this->medications as $medication) {
            if (isset($medication["timing_info"])) {
                if ($medication["timing_info"] === "On time") {
                    $onTimeCount++;
                } elseif (
                    isset($medication["is_late"]) &&
                    $medication["is_late"]
                ) {
                    $lateCount++;
                } else {
                    $earlyCount++;
                }
            }
        }

        return [
            "type" => "bulk_medication_taken",
            "message" => $isPatient
                ? "You took {$this->count} {$this->period} medications"
                : "{$this->patient->name} took {$this->count} {$this->period} medications",
            "period" => $this->period,
            "period_label" => $periodLabel,
            "medication_count" => $this->count,
            "medications" => $this->medications,
            "taken_at" => $this->takenAt->toDateTimeString(),
            "patient_name" => $this->patient->name,
            "patient_id" => $this->patient->id,
            "notes" => $this->notes,
            "timing_summary" => [
                "on_time" => $onTimeCount,
                "late" => $lateCount,
                "early" => $earlyCount,
            ],
        ];
    }
}
