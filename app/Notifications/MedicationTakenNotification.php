<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\MedicationLog;
use App\Models\User;

class MedicationTakenNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public MedicationLog $medicationLog,
        public User $patient,
    ) {
        // Ensure the medication relationship is loaded
        $this->medicationLog->load("medication");
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
        $medicationName = $this->medicationLog->medication->name;

        if ($isPatient) {
            $subject = "Medication Taken - {$medicationName}";
            $greeting = "Hello {$notifiable->name}";
            $mainMessage =
                "This is a confirmation that you have taken your medication.";
        } else {
            $subject = "Medication Taken - {$this->patient->name}";
            $greeting = "Hello {$notifiable->name}";
            $mainMessage = "This is a notification that {$this->patient->name} has taken their medication.";
        }

        $takenTime = $this->medicationLog->taken_at->format("M j, Y g:i A");
        $timingInfo = $this->medicationLog->getTimeDifference();

        $mailMessage = new MailMessage();
        $mailMessage->subject($subject);
        $mailMessage->greeting($greeting);
        $mailMessage->line($mainMessage);
        $mailMessage->line("**Medication:** {$medicationName}");
        $mailMessage->line("**Dosage:** {$this->medicationLog->dosage_taken}");
        $mailMessage->line("**Taken at:** {$takenTime}");

        if ($timingInfo) {
            $mailMessage->line("**Timing:** {$timingInfo}");

            // Add friendly timing context
            if ($timingInfo === "On time") {
                $mailMessage->line("✅ Great job staying on schedule!");
            } elseif ($this->medicationLog->isTakenLate()) {
                $mailMessage->line(
                    "⏰ A bit late, but taking your medication is what matters most.",
                );
            } else {
                $mailMessage->line(
                    "⏱️ Taken early - you're on top of your medication schedule!",
                );
            }
        }

        $mailMessage->action(
            "View Medication Schedule",
            route("medications.schedule"),
        );

        if ($this->medicationLog->notes) {
            $mailMessage->line("**Notes:** {$this->medicationLog->notes}");
        }

        $mailMessage->line("Stay healthy and keep track of your medications!");

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

        $timingInfo = $this->medicationLog->getTimeDifference();

        return [
            "type" => "medication_taken",
            "message" => $isPatient
                ? "You took {$this->medicationLog->medication->name}"
                : "{$this->patient->name} took {$this->medicationLog->medication->name}",
            "medication_name" => $this->medicationLog->medication->name,
            "dosage_taken" => $this->medicationLog->dosage_taken,
            "taken_at" => $this->medicationLog->taken_at->toDateTimeString(),
            "intended_time" => $this->medicationLog->intended_time?->toDateTimeString(),
            "timing_info" => $timingInfo,
            "is_late" => $this->medicationLog->isTakenLate(),
            "patient_name" => $this->patient->name,
            "patient_id" => $this->patient->id,
            "medication_log_id" => $this->medicationLog->id,
            "notes" => $this->medicationLog->notes,
        ];
    }
}
