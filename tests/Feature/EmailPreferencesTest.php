<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can access notification settings page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings/notifications')
        ->assertOk()
        ->assertSee('Notification Settings')
        ->assertSee('Email Preferences');
});

test('notification settings controller can handle basic notification updates', function () {
    $user = User::factory()->create([
        'notify_medication_taken' => false,
        'notify_seizure_added' => false,
        'notify_trusted_contacts_medication' => false,
        'notify_trusted_contacts_seizures' => false,
    ]);

    $response = $this->actingAs($user)
        ->put('/settings/notifications', [
            'notify_medication_taken' => true,
            'notify_seizure_added' => true,
            'notify_trusted_contacts_medication' => true,
            'notify_trusted_contacts_seizures' => true,
        ]);

    $response->assertRedirect('/settings/notifications');
    $response->assertSessionHas('success');

    $user->refresh();

    expect($user->notify_medication_taken)->toBeTrue();
    expect($user->notify_seizure_added)->toBeTrue();
    expect($user->notify_trusted_contacts_medication)->toBeTrue();
    expect($user->notify_trusted_contacts_seizures)->toBeTrue();
});

test('user can update email preference fields in database', function () {
    $user = User::factory()->create();

    // Test that we can save email preferences to database
    $user->update([
        'email_frequency_medication' => 'weekly',
        'email_frequency_seizures' => 'daily',
        'email_digest_enabled' => true,
        'email_digest_frequency' => 'weekly',
        'email_digest_day' => 5,
        'email_digest_time' => '09:00:00',
        'email_include_charts' => false,
        'email_include_summaries' => true,
        'email_marketing_enabled' => true,
        'email_medication_reminders' => false,
        'email_appointment_reminders' => true,
        'email_system_updates' => false,
    ]);

    $user->refresh();

    expect($user->email_frequency_medication)->toBe('weekly');
    expect($user->email_frequency_seizures)->toBe('daily');
    expect($user->email_digest_enabled)->toBeTrue();
    expect($user->email_digest_frequency)->toBe('weekly');
    expect($user->email_digest_day)->toBe(5);
    expect($user->email_digest_time->format('H:i'))->toBe('09:00');
    expect($user->email_include_charts)->toBeFalse();
    expect($user->email_include_summaries)->toBeTrue();
    expect($user->email_marketing_enabled)->toBeTrue();
    expect($user->email_medication_reminders)->toBeFalse();
    expect($user->email_appointment_reminders)->toBeTrue();
    expect($user->email_system_updates)->toBeFalse();
});

test('email preferences have proper default values', function () {
    $user = User::factory()->create();

    // Test default values from migration
    expect($user->email_frequency_medication)->toBe('immediate');
    expect($user->email_frequency_seizures)->toBe('immediate');
    expect($user->email_digest_enabled)->toBeFalse();
    expect($user->email_digest_frequency)->toBe('weekly');
    expect($user->email_digest_day)->toBe(1);
    expect($user->email_digest_time->format('H:i'))->toBe('08:00');
    expect($user->email_include_charts)->toBeTrue();
    expect($user->email_include_summaries)->toBeTrue();
    expect($user->email_marketing_enabled)->toBeFalse();
    expect($user->email_medication_reminders)->toBeTrue();
    expect($user->email_appointment_reminders)->toBeTrue();
    expect($user->email_system_updates)->toBeTrue();
});
