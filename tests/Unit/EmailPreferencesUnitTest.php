<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user model has email preference attributes', function () {
    $user = User::factory()->create();

    // Test default values from migration
    expect($user->email_frequency_medication)->toBe('immediate');
    expect($user->email_frequency_seizures)->toBe('immediate');
    expect($user->email_digest_enabled)->toBeFalse();
    expect($user->email_include_charts)->toBeTrue();
    expect($user->email_include_summaries)->toBeTrue();
    expect($user->email_marketing_enabled)->toBeFalse();
});

test('user can update email preferences', function () {
    $user = User::factory()->create();

    $user->update([
        'email_frequency_medication' => 'weekly',
        'email_frequency_seizures' => 'daily',
        'email_digest_enabled' => true,
        'email_digest_frequency' => 'weekly',
        'email_digest_day' => 5, // Friday
        'email_digest_time' => '09:00:00',
        'email_include_charts' => false,
        'email_marketing_enabled' => true,
    ]);

    $user->refresh();

    expect($user->email_frequency_medication)->toBe('weekly');
    expect($user->email_frequency_seizures)->toBe('daily');
    expect($user->email_digest_enabled)->toBeTrue();
    expect($user->email_digest_frequency)->toBe('weekly');
    expect($user->email_digest_day)->toBe(5);
    expect($user->email_digest_time->format('H:i'))->toBe('09:00');
    expect($user->email_include_charts)->toBeFalse();
    expect($user->email_marketing_enabled)->toBeTrue();
});

test('email preference fields are fillable', function () {
    $user = User::factory()->create();

    $preferences = [
        'email_frequency_medication' => 'never',
        'email_frequency_seizures' => 'never',
        'email_digest_enabled' => true,
        'email_include_summaries' => false,
        'email_medication_reminders' => false,
        'email_appointment_reminders' => false,
        'email_system_updates' => false,
        'email_marketing_enabled' => true,
    ];

    // This should not throw a mass assignment exception
    expect(fn () => $user->fill($preferences))->not->toThrow();

    $user->update($preferences);
    $user->refresh();

    expect($user->email_frequency_medication)->toBe('never');
    expect($user->email_marketing_enabled)->toBeTrue();
});
