<?php

use App\Models\User;
use App\Models\Medication;
use App\Models\MedicationLog;
use App\Models\MedicationSchedule;
use App\Models\Seizure;
use App\Models\TrustedContact;
use App\Notifications\MedicationTakenNotification;
use App\Notifications\BulkMedicationTakenNotification;
use App\Notifications\SeizureAddedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a patient user
    $this->patient = User::factory()->create([
        "account_type" => "patient",
        "notify_medication_taken" => true,
        "notify_seizure_added" => true,
        "notify_trusted_contacts_medication" => true,
        "notify_trusted_contacts_seizures" => true,
    ]);

    // Create a trusted contact user
    $this->trustedContact = User::factory()->create([
        "account_type" => "carer",
    ]);

    // Create trusted contact relationship
    TrustedContact::create([
        "user_id" => $this->patient->id,
        "trusted_user_id" => $this->trustedContact->id,
        "nickname" => "Test Carer",
        "is_active" => true,
        "granted_at" => now(),
    ]);

    // Create a medication for testing
    $this->medication = Medication::factory()->create([
        "user_id" => $this->patient->id,
        "name" => "Test Medication",
        "dosage" => "10",
        "unit" => "mg",
    ]);

    // Create a medication schedule
    $this->medicationSchedule = MedicationSchedule::factory()->create([
        "medication_id" => $this->medication->id,
        "scheduled_time" => "09:00:00",
        "dosage_multiplier" => 1,
    ]);
});

it(
    "sends medication taken notifications to patient and trusted contacts",
    function () {
        Notification::fake();

        // Act as the patient
        $this->actingAs($this->patient);

        // Log a medication as taken
        $response = $this->post(route("medications.log-taken"), [
            "medication_id" => $this->medication->id,
            "medication_schedule_id" => $this->medicationSchedule->id,
            "taken_at" => now(),
            "dosage_taken" => "10 mg",
            "notes" => "Test notes",
        ]);

        $response->assertRedirect();
        $response->assertSessionHas("success");

        // Assert notifications were sent
        Notification::assertSentTo(
            $this->patient,
            MedicationTakenNotification::class,
        );
        Notification::assertSentTo(
            $this->trustedContact,
            MedicationTakenNotification::class,
        );

        // Verify notification content
        Notification::assertSentTo(
            $this->patient,
            MedicationTakenNotification::class,
            function ($notification) {
                $array = $notification->toArray($this->patient);
                return $array["type"] === "medication_taken" &&
                    $array["medication_name"] === "Test Medication" &&
                    $array["patient_name"] === $this->patient->name;
            },
        );
    },
);

it("does not send medication notifications when disabled", function () {
    Notification::fake();

    // Disable notifications for the patient
    $this->patient->update([
        "notify_medication_taken" => false,
        "notify_trusted_contacts_medication" => false,
    ]);

    // Act as the patient
    $this->actingAs($this->patient);

    // Log a medication as taken
    $this->post(route("medications.log-taken"), [
        "medication_id" => $this->medication->id,
        "medication_schedule_id" => $this->medicationSchedule->id,
        "taken_at" => now(),
        "dosage_taken" => "10 mg",
    ]);

    // Assert no notifications were sent
    Notification::assertNotSentTo(
        $this->patient,
        MedicationTakenNotification::class,
    );
    Notification::assertNotSentTo(
        $this->trustedContact,
        MedicationTakenNotification::class,
    );
});

it("includes timing information in medication notifications", function () {
    Notification::fake();

    // Act as the patient
    $this->actingAs($this->patient);

    // Log a late medication (taken 45 minutes after intended time)
    $intendedTime = now()->setTime(9, 0); // 9:00 AM
    $takenTime = $intendedTime->copy()->addMinutes(45); // 9:45 AM (45 minutes late)

    $this->post(route("medications.log-taken"), [
        "medication_id" => $this->medication->id,
        "medication_schedule_id" => $this->medicationSchedule->id,
        "taken_at" => $takenTime,
        "intended_time" => $intendedTime,
        "dosage_taken" => "10 mg",
    ]);

    // Verify timing information is included in notification
    Notification::assertSentTo(
        $this->patient,
        MedicationTakenNotification::class,
        function ($notification) {
            $array = $notification->toArray($this->patient);
            return isset($array["timing_info"]) &&
                isset($array["is_late"]) &&
                $array["is_late"] === true &&
                str_contains($array["timing_info"], "late");
        },
    );
});

it("shows on-time status for punctual medication taking", function () {
    Notification::fake();

    // Act as the patient
    $this->actingAs($this->patient);

    // Log medication taken exactly on time
    $scheduledTime = now()->setTime(9, 0);

    $this->post(route("medications.log-taken"), [
        "medication_id" => $this->medication->id,
        "medication_schedule_id" => $this->medicationSchedule->id,
        "taken_at" => $scheduledTime,
        "intended_time" => $scheduledTime,
        "dosage_taken" => "10 mg",
    ]);

    // Verify on-time status
    Notification::assertSentTo(
        $this->patient,
        MedicationTakenNotification::class,
        function ($notification) {
            $array = $notification->toArray($this->patient);
            return isset($array["timing_info"]) &&
                $array["timing_info"] === "On time" &&
                isset($array["is_late"]) &&
                $array["is_late"] === false;
        },
    );
});

it(
    "sends seizure added notifications to patient and trusted contacts",
    function () {
        Notification::fake();

        // Act as the patient
        $this->actingAs($this->patient);

        // Create a seizure
        $response = $this->post(route("seizures.store"), [
            "user_id" => $this->patient->id,
            "start_time" => now(),
            "end_time" => now()->addMinutes(5),
            "severity" => 5,
            "seizure_type" => "Tonic-clonic",
            "notes" => "Test seizure notes",
        ]);

        $response->assertRedirect(route("seizures.index"));
        $response->assertSessionHas("success");

        // Assert notifications were sent
        Notification::assertSentTo(
            $this->patient,
            SeizureAddedNotification::class,
        );
        Notification::assertSentTo(
            $this->trustedContact,
            SeizureAddedNotification::class,
        );

        // Verify notification content
        Notification::assertSentTo(
            $this->patient,
            SeizureAddedNotification::class,
            function ($notification) {
                $array = $notification->toArray($this->patient);
                return $array["type"] === "seizure_added" &&
                    $array["seizure_type"] === "Tonic-clonic" &&
                    $array["severity"] === 5 &&
                    $array["patient_name"] === $this->patient->name;
            },
        );
    },
);

it("does not send seizure notifications when disabled", function () {
    Notification::fake();

    // Disable seizure notifications
    $this->patient->update([
        "notify_seizure_added" => false,
        "notify_trusted_contacts_seizures" => false,
    ]);

    // Act as the patient
    $this->actingAs($this->patient);

    // Create a seizure
    $this->post(route("seizures.store"), [
        "user_id" => $this->patient->id,
        "start_time" => now(),
        "end_time" => now()->addMinutes(5),
        "severity" => 5,
        "seizure_type" => "Tonic-clonic",
    ]);

    // Assert no notifications were sent
    Notification::assertNotSentTo(
        $this->patient,
        SeizureAddedNotification::class,
    );
    Notification::assertNotSentTo(
        $this->trustedContact,
        SeizureAddedNotification::class,
    );
});

it(
    "sends notifications only to patient when trusted contact notifications disabled",
    function () {
        Notification::fake();

        // Disable trusted contact notifications but keep personal notifications
        $this->patient->update([
            "notify_medication_taken" => true,
            "notify_trusted_contacts_medication" => false,
        ]);

        // Act as the patient
        $this->actingAs($this->patient);

        // Log a medication as taken
        $this->post(route("medications.log-taken"), [
            "medication_id" => $this->medication->id,
            "medication_schedule_id" => $this->medicationSchedule->id,
            "taken_at" => now(),
            "dosage_taken" => "10 mg",
        ]);

        // Assert notification was sent to patient but not trusted contact
        Notification::assertSentTo(
            $this->patient,
            MedicationTakenNotification::class,
        );
        Notification::assertNotSentTo(
            $this->trustedContact,
            MedicationTakenNotification::class,
        );
    },
);

it("includes emergency status in seizure notifications", function () {
    Notification::fake();

    // Set emergency thresholds
    $this->patient->update([
        "status_epilepticus_duration_minutes" => 5,
        "emergency_seizure_count" => 2,
        "emergency_seizure_timeframe_hours" => 24,
    ]);

    // Act as the patient
    $this->actingAs($this->patient);

    // Create an emergency seizure (long duration)
    $this->post(route("seizures.store"), [
        "user_id" => $this->patient->id,
        "start_time" => now(),
        "end_time" => now()->addMinutes(10), // Longer than emergency threshold
        "severity" => 8,
        "seizure_type" => "Tonic-clonic",
        "notes" => "Emergency seizure",
    ]);

    // Verify emergency status is included in notification
    Notification::assertSentTo(
        $this->trustedContact,
        SeizureAddedNotification::class,
        function ($notification) {
            $array = $notification->toArray($this->trustedContact);
            return $array["is_emergency"] === true &&
                $array["emergency_reason"] === "Possible Status Epilepticus";
        },
    );
});

it("sends bulk medication notifications correctly", function () {
    Notification::fake();

    // Create additional medication and schedule
    $medication2 = Medication::factory()->create([
        "user_id" => $this->patient->id,
        "name" => "Morning Medication 2",
        "dosage" => "5",
        "unit" => "mg",
    ]);

    MedicationSchedule::factory()->create([
        "medication_id" => $medication2->id,
        "scheduled_time" => "09:30:00",
        "dosage_multiplier" => 1,
    ]);

    // Act as the patient
    $this->actingAs($this->patient);

    // Log bulk medications for morning period
    $this->post(route("medications.log-bulk-taken"), [
        "period" => "morning",
        "taken_at" => now(),
        "notes" => "Bulk morning medications",
    ]);

    // Should receive single bulk notification (not individual ones)
    Notification::assertSentTo(
        $this->patient,
        BulkMedicationTakenNotification::class,
    );
    Notification::assertSentTo(
        $this->trustedContact,
        BulkMedicationTakenNotification::class,
    );

    // Should NOT receive individual medication notifications
    Notification::assertNotSentTo(
        $this->patient,
        MedicationTakenNotification::class,
    );
    Notification::assertNotSentTo(
        $this->trustedContact,
        MedicationTakenNotification::class,
    );
});

it("includes timing summary in bulk medication notifications", function () {
    Notification::fake();

    // Create additional morning medications
    $medication2 = Medication::factory()->create([
        "user_id" => $this->patient->id,
        "name" => "Morning Med 2",
        "dosage" => "5",
        "unit" => "mg",
    ]);

    MedicationSchedule::factory()->create([
        "medication_id" => $medication2->id,
        "scheduled_time" => "09:30:00",
        "dosage_multiplier" => 1,
    ]);

    // Act as the patient
    $this->actingAs($this->patient);

    // Log bulk medications for morning period
    $this->post(route("medications.log-bulk-taken"), [
        "period" => "morning",
        "taken_at" => now()->setTime(9, 45), // 45 minutes late
        "notes" => "Late morning medications",
    ]);

    // Verify timing summary is included
    Notification::assertSentTo(
        $this->patient,
        BulkMedicationTakenNotification::class,
        function ($notification) {
            $array = $notification->toArray($this->patient);
            return isset($array["timing_summary"]) &&
                is_array($array["timing_summary"]) &&
                isset($array["medications"]) &&
                count($array["medications"]) >= 2;
        },
    );
});

it("can access notification settings page", function () {
    $this->actingAs($this->patient);

    $response = $this->get(route("settings.notifications"));

    $response->assertOk();
    $response->assertViewIs("settings.notifications");
    $response->assertSee("Notification Settings");
    $response->assertSee("Personal Notifications");
    $response->assertSee("Trusted Contact Notifications");
});

it("can update notification settings", function () {
    $this->actingAs($this->patient);

    $response = $this->put(route("settings.notifications.update"), [
        "notify_medication_taken" => false,
        "notify_seizure_added" => true,
        "notify_trusted_contacts_medication" => false,
        "notify_trusted_contacts_seizures" => true,
    ]);

    $response->assertRedirect(route("settings.notifications"));
    $response->assertSessionHas("success");

    // Verify settings were updated
    $this->patient->refresh();
    expect($this->patient->notify_medication_taken)->toBeFalse();
    expect($this->patient->notify_seizure_added)->toBeTrue();
    expect($this->patient->notify_trusted_contacts_medication)->toBeFalse();
    expect($this->patient->notify_trusted_contacts_seizures)->toBeTrue();
});

it("shows trusted contacts in notification settings", function () {
    $this->actingAs($this->patient);

    $response = $this->get(route("settings.notifications"));

    $response->assertOk();
    $response->assertSee("Your Trusted Contacts");
    $response->assertSee("Test Carer");
});

it("shows warning when no trusted contacts exist", function () {
    // Remove trusted contact relationship
    TrustedContact::where("user_id", $this->patient->id)->delete();

    $this->actingAs($this->patient);

    $response = $this->get(route("settings.notifications"));

    $response->assertOk();
    $response->assertSee("No Trusted Contacts");
    $response->assertSee("Set Up Trusted Contacts");
});
