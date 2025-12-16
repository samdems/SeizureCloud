<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\MedicationSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MedicationReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public User $patient,
        public array $overdueMedications = [],
        public array $dueMedications = [],
        public string $reminderType = "overdue",
    ) {
        // 'overdue', 'due', 'both'
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
        $mailMessage = new MailMessage();

        $subject = $this->getSubject();

        return $mailMessage
            ->subject($subject)
            ->markdown("mail.medication-reminder", [
                "overdueMedications" => $this->overdueMedications,
                "dueMedications" => $this->dueMedications,
                "patient" => $this->patient,
                "recipient" => $notifiable,
                "reminderType" => $this->reminderType,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            "type" => "medication_reminder",
            "reminder_type" => $this->reminderType,
            "patient_id" => $this->patient->id,
            "patient_name" => $this->patient->name,
            "overdue_count" => count($this->overdueMedications),
            "due_count" => count($this->dueMedications),
            "overdue_medications" => $this->formatMedicationsForArray(
                $this->overdueMedications,
            ),
            "due_medications" => $this->formatMedicationsForArray(
                $this->dueMedications,
            ),
        ];
    }

    /**
     * Get the subject line for the email
     */
    private function getSubject(): string
    {
        $overdueCount = count($this->overdueMedications);
        $dueCount = count($this->dueMedications);

        if ($overdueCount > 0 && $dueCount > 0) {
            return "Medication Reminder: Overdue and Due Medications";
        } elseif ($overdueCount > 0) {
            return $overdueCount === 1
                ? "Medication Reminder: 1 Overdue Medication"
                : "Medication Reminder: {$overdueCount} Overdue Medications";
        } else {
            return $dueCount === 1
                ? "Medication Reminder: 1 Medication Due"
                : "Medication Reminder: {$dueCount} Medications Due";
        }
    }

    /**
     * Get the greeting for the email
     */
    private function getGreeting(object $notifiable): string
    {
        if ($notifiable->id === $this->patient->id) {
            return "Hello {$notifiable->name},";
        } else {
            return "Hello {$notifiable->name},";
        }
    }

    /**
     * Get the main message for the email
     */
    private function getMainMessage(): string
    {
        $overdueCount = count($this->overdueMedications);
        $dueCount = count($this->dueMedications);

        if ($overdueCount > 0 && $dueCount > 0) {
            return "This is a reminder that you have {$overdueCount} overdue medication(s) and {$dueCount} medication(s) currently due.";
        } elseif ($overdueCount > 0) {
            return $overdueCount === 1
                ? "This is a reminder that you have 1 overdue medication that needs to be taken."
                : "This is a reminder that you have {$overdueCount} overdue medications that need to be taken.";
        } else {
            return $dueCount === 1
                ? "This is a reminder that you have 1 medication that is currently due."
                : "This is a reminder that you have {$dueCount} medications that are currently due.";
        }
    }

    /**
     * Format medications for array representation
     */
    private function formatMedicationsForArray(array $schedules): array
    {
        return array_map(function ($schedule) {
            $medication = $schedule->medication;
            return [
                "id" => $medication->id,
                "name" => $medication->name,
                "dosage" =>
                    $schedule->getCalculatedDosageWithUnit() ?:
                    $medication->dosage . " " . $medication->unit,
                "scheduled_time" => $schedule->scheduled_time->format("H:i"),
                "scheduled_time_formatted" => $schedule->scheduled_time->format(
                    "g:i A",
                ),
            ];
        }, $schedules);
    }
}
