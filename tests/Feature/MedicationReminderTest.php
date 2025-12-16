<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Medication;
use App\Models\MedicationSchedule;
use App\Models\MedicationLog;
use App\Models\TrustedContact;
use App\Notifications\MedicationReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Carbon\Carbon;

class MedicationReminderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    /** @test */
    public function it_sends_reminder_for_overdue_medication()
    {
        // Create a patient with an overdue medication
        $patient = User::factory()->create([
            'account_type' => 'patient',
            'email_verified_at' => now(),
            'notify_trusted_contacts_medication' => false,
        ]);

        $medication = Medication::factory()->create([
            'user_id' => $patient->id,
            'active' => true,
            'name' => 'Test Medication',
            'dosage' => '10',
            'unit' => 'mg',
        ]);

        $schedule = MedicationSchedule::factory()->create([
            'medication_id' => $medication->id,
            'scheduled_time' => Carbon::now()->subHours(2), // 2 hours overdue
            'frequency' => 'daily',
            'active' => true,
            'dosage_multiplier' => 1,
        ]);

        // Run the command
        Artisan::call('medication:send-reminders');

        // Assert notification was sent to patient
        Notification::assertSentTo(
            $patient,
            MedicationReminderNotification::class,
            function ($notification) use ($medication) {
                return count($notification->overdueMedications) === 1 &&
                       $notification->overdueMedications[0]->medication->name === 'Test Medication';
            }
        );
    }

    /** @test */
    public function it_sends_reminder_for_due_medication()
    {
        $patient = User::factory()->create([
            'account_type' => 'patient',
            'email_verified_at' => now(),
        ]);

        $medication = Medication::factory()->create([
            'user_id' => $patient->id,
            'active' => true,
        ]);

        $schedule = MedicationSchedule::factory()->create([
            'medication_id' => $medication->id,
            'scheduled_time' => Carbon::now()->subMinutes(10), // 10 minutes past due (not overdue yet)
            'frequency' => 'daily',
            'active' => true,
        ]);

        Artisan::call('medication:send-reminders');

        Notification::assertSentTo(
            $patient,
            MedicationReminderNotification::class,
            function ($notification) {
                return count($notification->dueMedications) === 1 &&
                       count($notification->overdueMedications) === 0;
            }
        );
    }

    /** @test */
    public function it_does_not_send_reminder_for_already_taken_medication()
    {
        $patient = User::factory()->create([
            'account_type' => 'patient',
            'email_verified_at' => now(),
        ]);

        $medication = Medication::factory()->create([
            'user_id' => $patient->id,
            'active' => true,
        ]);

        $schedule = MedicationSchedule::factory()->create([
            'medication_id' => $medication->id,
            'scheduled_time' => Carbon::now()->subHours(1), // Overdue
            'frequency' => 'daily',
            'active' => true,
        ]);

        // Log that medication was taken today
        MedicationLog::factory()->create([
            'medication_id' => $medication->id,
            'medication_schedule_id' => $schedule->id,
            'taken_at' => now(),
            'skipped' => false,
        ]);

        Artisan::call('medication:send-reminders');

        // Should not send any notification since medication was already taken
        Notification::assertNotSentTo($patient, MedicationReminderNotification::class);
    }

    /** @test */
    public function it_sends_reminder_to_trusted_contacts_when_enabled()
    {
        $patient = User::factory()->create([
            'account_type' => 'patient',
            'email_verified_at' => now(),
            'notify_trusted_contacts_medication' => true,
        ]);

        $trustedContact = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // Create trusted contact relationship
        TrustedContact::factory()->create([
            'user_id' => $patient->id,
            'trusted_user_id' => $trustedContact->id,
            'is_active' => true,
        ]);

        $medication = Medication::factory()->create([
            'user_id' => $patient->id,
            'active' => true,
        ]);

        MedicationSchedule::factory()->create([
            'medication_id' => $medication->id,
            'scheduled_time' => Carbon::now()->subHours(1), // Overdue
            'frequency' => 'daily',
            'active' => true,
        ]);

        Artisan::call('medication:send-reminders');

        // Should send to both patient and trusted contact
        Notification::assertSentTo($patient, MedicationReminderNotification::class);
        Notification::assertSentTo($trustedContact, MedicationReminderNotification::class);
    }

    /** @test */
    public function it_does_not_send_reminder_to_trusted_contacts_when_disabled()
    {
        $patient = User::factory()->create([
            'account_type' => 'patient',
            'email_verified_at' => now(),
            'notify_trusted_contacts_medication' => false,
        ]);

        $trustedContact = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        TrustedContact::factory()->create([
            'user_id' => $patient->id,
            'trusted_user_id' => $trustedContact->id,
            'is_active' => true,
        ]);

        $medication = Medication::factory()->create([
            'user_id' => $patient->id,
            'active' => true,
        ]);

        MedicationSchedule::factory()->create([
            'medication_id' => $medication->id,
            'scheduled_time' => Carbon::now()->subHours(1),
            'frequency' => 'daily',
            'active' => true,
        ]);

        Artisan::call('medication:send-reminders');

        // Should send only to patient
        Notification::assertSentTo($patient, MedicationReminderNotification::class);
        Notification::assertNotSentTo($trustedContact, MedicationReminderNotification::class);
    }

    /** @test */
    public function it_does_not_send_reminder_for_inactive_medication()
    {
        $patient = User::factory()->create([
            'account_type' => 'patient',
            'email_verified_at' => now(),
        ]);

        $medication = Medication::factory()->create([
            'user_id' => $patient->id,
            'active' => false, // Inactive medication
        ]);

        MedicationSchedule::factory()->create([
            'medication_id' => $medication->id,
            'scheduled_time' => Carbon::now()->subHours(1),
            'frequency' => 'daily',
            'active' => true,
        ]);

        Artisan::call('medication:send-reminders');

        Notification::assertNotSentTo($patient, MedicationReminderNotification::class);
    }

    /** @test */
    public function it_does_not_send_reminder_for_as_needed_medication()
    {
        $patient = User::factory()->create([
            'account_type' => 'patient',
            'email_verified_at' => now(),
        ]);

        $medication = Medication::factory()->create([
            'user_id' => $patient->id,
            'active' => true,
            'as_needed' => true,
        ]);

        MedicationSchedule::factory()->create([
            'medication_id' => $medication->id,
            'scheduled_time' => Carbon::now()->subHours(1),
            'frequency' => 'as_needed',
            'active' => true,
        ]);

        Artisan::call('medication:send-reminders');

        Notification::assertNotSentTo($patient, MedicationReminderNotification::class);
    }

    /** @test */
    public function it_handles_multiple_overdue_medications()
    {
        $patient = User::factory()->create([
            'account_type' => 'patient',
            'email_verified_at' => now(),
        ]);

        // Create two overdue medications
        $medication1 = Medication::factory()->create([
            'user_id' => $patient->id,
            'active' => true,
            'name' => 'Medication 1',
        ]);

        $medication2 = Medication::factory()->create([
            'user_id' => $patient->id,
            'active' => true,
            'name' => 'Medication 2',
        ]);

        MedicationSchedule::factory()->create([
            'medication_id' => $medication1->id,
            'scheduled_time' => Carbon::now()->subHours(2),
            'frequency' => 'daily',
            'active' => true,
        ]);

        MedicationSchedule::factory()->create([
            'medication_id' => $medication2->id,
            'scheduled_time' => Carbon::now()->subHours(1),
            'frequency' => 'daily',
            'active' => true,
        ]);

        Artisan::call('medication:send-reminders');

        Notification::assertSentTo(
            $patient,
            MedicationReminderNotification::class,
            function ($notification) {
                return count($notification->overdueMedications) === 2;
            }
        );
    }

    /** @test */
    public function it_respects_user_filter_option()
    {
        $patient1 = User::factory()->create([
            'account_type' => 'patient',
            'email_verified_at' => now(),
        ]);

        $patient2 = User::factory()->create([
            'account_type' => 'patient',
            'email_verified_at' => now(),
        ]);

        // Create overdue medications for both patients
        foreach ([$patient1, $patient2] as $patient) {
            $medication = Medication::factory()->create([
                'user_id' => $patient->id,
                'active' => true,
            ]);

            MedicationSchedule::factory()->create([
                'medication_id' => $medication->id,
                'scheduled_time' => Carbon::now()->subHours(1),
                'frequency' => 'daily',
                'active' => true,
            ]);
        }

        // Run command for specific user only
        Artisan::call('medication:send-reminders', ['--user' => $patient1->id]);

        // Should only send to patient1
        Notification::assertSentTo($patient1, MedicationReminderNotification::class);
        Notification::assertNotSentTo($patient2, MedicationReminderNotification::class);
    }

    /** @test */
    public function it_handles_dry_run_mode()
    {
        $patient = User::factory()->create([
            'account_type' => 'patient',
            'email_verified_at' => now(),
        ]);

        $medication = Medication::factory()->create([
            'user_id' => $patient->id,
            'active' => true,
        ]);

        MedicationSchedule::factory()->create([
            'medication_id' => $medication->id,
            'scheduled_time' => Carbon::now()->subHours(1),
            'frequency' => 'daily',
            'active' => true,
        ]);

        // Run in dry-run mode
        Artisan::call('medication:send-reminders', ['--dry-run' => true]);

        // Should not send any notifications in dry-run mode
        Notification::assertNothingSent();
    }

    /** @test */
    public function it_handles_overdue_only_option()
    {
        $patient = User::factory()->create([
            'account_type' => 'patient',
            'email_verified_at' => now(),
        ]);

        $medication1 = Medication::factory()->create([
            'user_id' => $patient->id,
            'active' => true,
        ]);

        $medication2 = Medication::factory()->create([
            'user_id' => $patient->id,
            'active' => true,
        ]);

        // One overdue (past grace period)
        MedicationSchedule::factory()->create([
            'medication_id' => $medication1->id,
            'scheduled_time' => Carbon::now()->subMinutes(45), // 45 minutes past = overdue
            'frequency' => 'daily',
            'active' => true,
        ]);

        // One due but not overdue yet
        MedicationSchedule::factory()->create([
            'medication_id' => $medication2->id,
            'scheduled_time' => Carbon::now()->subMinutes(15), // 15 minutes past = due but not overdue
            'frequency' => 'daily',
            'active' => true,
        ]);

        Artisan::call('medication:send-reminders', ['--overdue-only' => true]);

        Notification::assertSentTo(
            $patient,
            MedicationReminderNotification::class,
            function ($notification) {
                return count($notification->overdueMedications) === 1 &&
                       count($notification->dueMedications) === 0;
            }
        );
    }

    /** @test */
    public function it_does_not_send_reminder_for_unverified_email()
    {
        $patient = User::factory()->create([
            'account_type' => 'patient',
            'email_verified_at' => null, // Unverified email
        ]);

        $medication = Medication::factory()->create([
            'user_id' => $patient->id,
            'active' => true,
        ]);

        MedicationSchedule::factory()->create([
            'medication_id' => $medication->id,
            'scheduled_time' => Carbon::now()->subHours(1),
            'frequency' => 'daily',
            'active' => true,
        ]);

        Artisan::call('medication:send-reminders');

        Notification::assertNotSentTo($patient, MedicationReminderNotification::class);
    }

    /** @test */
    public function it_does_not_send_reminder_for_non_patient_accounts()
    {
        $carer = User::factory()->create([
            'account_type' => 'carer',
            'email_verified_at' => now(),
        ]);

        // This shouldn't happen in normal usage, but test defensive coding
        $medication = Medication::factory()->create([
            'user_id' => $carer->id,
            'active' => true,
        ]);

        MedicationSchedule::factory()->create([
            'medication_id' => $medication->id,
            'scheduled_time' => Carbon::now()->subHours(1),
            'frequency' => 'daily',
            'active' => true,
        ]);

        Artisan::call('medication:send-reminders');

        Notification::assertNotSentTo($carer, MedicationReminderNotification::class);
    }
}
