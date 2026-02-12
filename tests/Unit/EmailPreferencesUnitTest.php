<?php

use App\Models\User;

test('user model has email preference fillable fields', function () {
    $user = new User;

    $fillable = $user->getFillable();

    // Test that email preference fields are in fillable array
    expect($fillable)->toContain('email_frequency_medication');
    expect($fillable)->toContain('email_frequency_seizures');
    expect($fillable)->toContain('email_digest_enabled');
    expect($fillable)->toContain('email_digest_frequency');
    expect($fillable)->toContain('email_digest_day');
    expect($fillable)->toContain('email_digest_time');
    expect($fillable)->toContain('email_include_charts');
    expect($fillable)->toContain('email_include_summaries');
    expect($fillable)->toContain('email_marketing_enabled');
    expect($fillable)->toContain('email_medication_reminders');
    expect($fillable)->toContain('email_appointment_reminders');
    expect($fillable)->toContain('email_system_updates');
});

test('user model has proper casts for email preferences', function () {
    $user = new User;

    $casts = $user->getCasts();

    // Test that boolean fields are cast properly
    expect($casts)->toHaveKey('email_digest_enabled');
    expect($casts)->toHaveKey('email_include_charts');
    expect($casts)->toHaveKey('email_include_summaries');
    expect($casts)->toHaveKey('email_marketing_enabled');
    expect($casts)->toHaveKey('email_medication_reminders');
    expect($casts)->toHaveKey('email_appointment_reminders');
    expect($casts)->toHaveKey('email_system_updates');

    // Test time field cast
    expect($casts)->toHaveKey('email_digest_time');
    expect($casts['email_digest_time'])->toBe('datetime:H:i');

    // Test integer field cast
    expect($casts)->toHaveKey('email_digest_day');
    expect($casts['email_digest_day'])->toBe('integer');
});
