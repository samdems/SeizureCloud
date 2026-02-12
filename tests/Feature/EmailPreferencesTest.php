<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

test('user can view email preferences on notification settings page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings/notifications')
        ->assertOk()
        ->assertSeeLivewire('settings.email-preferences');
});

test('user can update email frequency preferences', function () {
    $user = User::factory()->create([
        'email_frequency_medication' => 'immediate',
        'email_frequency_seizures' => 'immediate',
    ]);

    Volt::test('settings.email-preferences')
        ->actingAs($user)
        ->set('email_frequency_medication', 'daily')
        ->set('email_frequency_seizures', 'weekly')
        ->call('saveEmailPreferences')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->email_frequency_medication)->toBe('daily');
    expect($user->email_frequency_seizures)->toBe('weekly');
});

test('user can enable email digest with custom settings', function () {
    $user = User::factory()->create([
        'email_digest_enabled' => false,
    ]);

    Volt::test('settings.email-preferences')
        ->actingAs($user)
        ->set('email_digest_enabled', true)
        ->set('email_digest_frequency', 'weekly')
        ->set('email_digest_day', 3) // Wednesday
        ->set('email_digest_time', '09:30')
        ->call('saveEmailPreferences')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->email_digest_enabled)->toBeTrue();
    expect($user->email_digest_frequency)->toBe('weekly');
    expect($user->email_digest_day)->toBe(3);
    expect($user->email_digest_time->format('H:i'))->toBe('09:30');
});

test('user can toggle content preferences for email digest', function () {
    $user = User::factory()->create([
        'email_include_charts' => false,
        'email_include_summaries' => false,
    ]);

    Volt::test('settings.email-preferences')
        ->actingAs($user)
        ->set('email_include_charts', true)
        ->set('email_include_summaries', true)
        ->call('saveEmailPreferences')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->email_include_charts)->toBeTrue();
    expect($user->email_include_summaries)->toBeTrue();
});

test('user can manage notification type preferences', function () {
    $user = User::factory()->create([
        'email_medication_reminders' => false,
        'email_appointment_reminders' => false,
        'email_system_updates' => false,
        'email_marketing_enabled' => false,
    ]);

    Volt::test('settings.email-preferences')
        ->actingAs($user)
        ->set('email_medication_reminders', true)
        ->set('email_appointment_reminders', true)
        ->set('email_system_updates', true)
        ->set('email_marketing_enabled', true)
        ->call('saveEmailPreferences')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->email_medication_reminders)->toBeTrue();
    expect($user->email_appointment_reminders)->toBeTrue();
    expect($user->email_system_updates)->toBeTrue();
    expect($user->email_marketing_enabled)->toBeTrue();
});

test('email frequency validation requires valid values', function () {
    $user = User::factory()->create();

    Volt::test('settings.email-preferences')
        ->actingAs($user)
        ->set('email_frequency_medication', 'invalid')
        ->call('saveEmailPreferences')
        ->assertHasErrors(['email_frequency_medication']);
});

test('digest day validation requires valid day of week', function () {
    $user = User::factory()->create();

    Volt::test('settings.email-preferences')
        ->actingAs($user)
        ->set('email_digest_day', 8) // Invalid day
        ->call('saveEmailPreferences')
        ->assertHasErrors(['email_digest_day']);
});

test('digest time validation requires valid time format', function () {
    $user = User::factory()->create();

    Volt::test('settings.email-preferences')
        ->actingAs($user)
        ->set('email_digest_time', '25:30') // Invalid time
        ->call('saveEmailPreferences')
        ->assertHasErrors(['email_digest_time']);
});

test('successful update displays success message', function () {
    $user = User::factory()->create();

    Volt::test('settings.email-preferences')
        ->actingAs($user)
        ->set('email_frequency_medication', 'weekly')
        ->call('saveEmailPreferences')
        ->assertHasNoErrors();

    expect(session('success'))->toBe('Email preferences updated successfully!');
});

test('user can disable all email preferences', function () {
    $user = User::factory()->create([
        'email_frequency_medication' => 'immediate',
        'email_frequency_seizures' => 'immediate',
        'email_digest_enabled' => true,
        'email_medication_reminders' => true,
        'email_appointment_reminders' => true,
        'email_system_updates' => true,
        'email_marketing_enabled' => true,
    ]);

    Volt::test('settings.email-preferences')
        ->actingAs($user)
        ->set('email_frequency_medication', 'never')
        ->set('email_frequency_seizures', 'never')
        ->set('email_digest_enabled', false)
        ->set('email_medication_reminders', false)
        ->set('email_appointment_reminders', false)
        ->set('email_system_updates', false)
        ->set('email_marketing_enabled', false)
        ->call('saveEmailPreferences')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->email_frequency_medication)->toBe('never');
    expect($user->email_frequency_seizures)->toBe('never');
    expect($user->email_digest_enabled)->toBeFalse();
    expect($user->email_medication_reminders)->toBeFalse();
    expect($user->email_appointment_reminders)->toBeFalse();
    expect($user->email_system_updates)->toBeFalse();
    expect($user->email_marketing_enabled)->toBeFalse();
});
