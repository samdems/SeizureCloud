<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Seizure;
use App\Models\User;
use App\Mail\LoggedMailMessage;

class SeizureAddedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Seizure $seizure, public User $patient)
    {
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
        $emergencyStatus = $this->patient->getEmergencyStatus($this->seizure);

        if ($isPatient) {
            $subject = "Seizure Recorded";
            $greeting = "Hello {$notifiable->name}";
            $mainMessage =
                "A seizure has been recorded in your SeizureCloud account.";
        } else {
            $subject = "Seizure Recorded - {$this->patient->name}";
            $greeting = "Hello {$notifiable->name}";
            $mainMessage = "A seizure has been recorded for {$this->patient->name}.";
        }

        $startTime = $this->seizure->start_time->format("M j, Y g:i A");

        $mailMessage = new LoggedMailMessage();
        $mailMessage
            ->subject($subject)
            ->emailType(
                $emergencyStatus["is_emergency"]
                    ? "emergency"
                    : "seizure_alert",
            )
            ->forUser($this->patient->id)
            ->withMetadata(
                array_filter(
                    [
                        "seizure_id" => (string) $this->seizure->id,
                        "patient_id" => (string) $this->patient->id,
                        "patient_name" => $this->patient->name,
                        "seizure_type" => $this->seizure->seizure_type,
                        "severity" => (string) $this->seizure->severity,
                        "start_time" => $this->seizure->start_time->toIso8601String(),
                        "duration_minutes" => $this->seizure
                            ->calculated_duration
                            ? (string) round(
                                $this->seizure->calculated_duration / 60,
                                1,
                            )
                            : null,
                        "is_emergency" => $emergencyStatus["is_emergency"]
                            ? "true"
                            : "false",
                        "emergency_reason" => $emergencyStatus["is_emergency"]
                            ? ($emergencyStatus["status_epilepticus"]
                                ? "Possible Status Epilepticus"
                                : "Seizure Cluster")
                            : null,
                        "is_for_patient" => $isPatient ? "true" : "false",
                    ],
                    function ($value) {
                        return $value !== null;
                    },
                ),
            );
        $mailMessage->greeting($greeting);
        $mailMessage->line($mainMessage);
        $mailMessage->line("**Date & Time:** {$startTime}");
        $mailMessage->line("**Type:** {$this->seizure->seizure_type}");
        $mailMessage->line("**Severity:** {$this->seizure->severity}/10");

        if ($this->seizure->calculated_duration) {
            $mailMessage->line(
                "**Duration:** {$this->seizure->calculated_duration} minutes",
            );
        }

        if ($emergencyStatus["is_emergency"]) {
            $mailMessage->line(
                "⚠️ **EMERGENCY ALERT:** This seizure meets emergency criteria!",
            );
            $mailMessage->line(
                "Please review emergency protocols and contact medical professionals if needed.",
            );
        }

        $mailMessage->action(
            "View Seizure Details",
            route("seizures.show", $this->seizure->id),
        );

        if ($this->seizure->notes) {
            $mailMessage->line("**Notes:** {$this->seizure->notes}");
        }

        $mailMessage->line(
            "Take care and stay safe. Consider discussing this with your healthcare provider.",
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
        $emergencyStatus = $this->patient->getEmergencyStatus($this->seizure);

        return array_filter(
            [
                "type" => "seizure_added",
                "message" => $isPatient
                    ? "A seizure was recorded"
                    : "A seizure was recorded for {$this->patient->name}",
                "seizure_type" => $this->seizure->seizure_type,
                "severity" => $this->seizure->severity,
                "start_time" => $this->seizure->start_time->toDateTimeString(),
                "duration_minutes" => $this->seizure->calculated_duration
                    ? round($this->seizure->calculated_duration / 60, 1)
                    : null,
                "patient_name" => $this->patient->name,
                "patient_id" => $this->patient->id,
                "seizure_id" => $this->seizure->id,
                "is_emergency" => $emergencyStatus["is_emergency"],
                "emergency_reason" => $emergencyStatus["is_emergency"]
                    ? ($emergencyStatus["status_epilepticus"]
                        ? "Possible Status Epilepticus"
                        : "Seizure Cluster")
                    : null,
                "notes" => $this->seizure->notes,
            ],
            function ($value) {
                return $value !== null;
            },
        );
    }
}
