<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get("/", function () {
    return view("welcome");
})->name("home");

Route::view("dashboard", "dashboard")
    ->middleware(["auth", "verified"])
    ->name("dashboard");

Route::middleware(["auth"])->group(function () {
    Route::redirect("settings", "settings/profile");

    // Profile settings
    Route::get("settings/profile", [
        \App\Http\Controllers\SettingsController::class,
        "profile",
    ])->name("settings.profile");

    // Legacy profile edit route for compatibility
    Route::get("profile/edit", [
        \App\Http\Controllers\SettingsController::class,
        "profile",
    ])->name("profile.edit");
    Route::put("settings/profile", [
        \App\Http\Controllers\SettingsController::class,
        "updateProfile",
    ])->name("settings.profile.update");
    Route::delete("settings/profile", [
        \App\Http\Controllers\SettingsController::class,
        "destroy",
    ])->name("profile.destroy");

    // Password settings
    Route::get("settings/password", [
        \App\Http\Controllers\SettingsController::class,
        "password",
    ])->name("settings.password");
    Route::put("settings/password", [
        \App\Http\Controllers\SettingsController::class,
        "updatePassword",
    ])->name("settings.password.update");

    // Time preferences
    Route::get("settings/time-preferences", [
        \App\Http\Controllers\SettingsController::class,
        "timePreferences",
    ])->name("settings.time-preferences");
    Route::put("settings/time-preferences", [
        \App\Http\Controllers\SettingsController::class,
        "updateTimePreferences",
    ])->name("settings.time-preferences.update");

    // Appearance settings
    Route::get("settings/appearance", [
        \App\Http\Controllers\SettingsController::class,
        "appearance",
    ])->name("settings.appearance");
    Route::put("settings/appearance", [
        \App\Http\Controllers\SettingsController::class,
        "updateAppearance",
    ])->name("settings.appearance.update");

    // Avatar settings
    Route::get("settings/avatar", [
        \App\Http\Controllers\SettingsController::class,
        "avatar",
    ])->name("settings.avatar");

    // Avatar demo/showcase
    Volt::route("demo/avatar-showcase", "demo.avatar-showcase")->name(
        "demo.avatar-showcase",
    );

    // Emergency settings
    Route::get("settings/emergency-settings", [
        \App\Http\Controllers\SettingsController::class,
        "emergencySettings",
    ])->name("settings.emergency-settings");
    Route::put("settings/emergency-settings", [
        \App\Http\Controllers\SettingsController::class,
        "updateEmergencySettings",
    ])->name("settings.emergency-settings.update");

    // Trusted contacts
    Route::get("settings/trusted-contacts", [
        \App\Http\Controllers\TrustedContactController::class,
        "index",
    ])->name("settings.trusted-contacts.index");
    Route::get("settings/trusted-contacts/create", [
        \App\Http\Controllers\TrustedContactController::class,
        "create",
    ])->name("settings.trusted-contacts.create");
    Route::post("settings/trusted-contacts", [
        \App\Http\Controllers\TrustedContactController::class,
        "store",
    ])->name("settings.trusted-contacts.store");
    Route::get("settings/trusted-contacts/{trustedContact}", [
        \App\Http\Controllers\TrustedContactController::class,
        "show",
    ])->name("settings.trusted-contacts.show");
    Route::get("settings/trusted-contacts/{trustedContact}/edit", [
        \App\Http\Controllers\TrustedContactController::class,
        "edit",
    ])->name("settings.trusted-contacts.edit");
    Route::put("settings/trusted-contacts/{trustedContact}", [
        \App\Http\Controllers\TrustedContactController::class,
        "update",
    ])->name("settings.trusted-contacts.update");
    Route::delete("settings/trusted-contacts/{trustedContact}", [
        \App\Http\Controllers\TrustedContactController::class,
        "destroy",
    ])->name("settings.trusted-contacts.destroy");
    Route::patch("settings/trusted-contacts/{trustedContact}/toggle-status", [
        \App\Http\Controllers\TrustedContactController::class,
        "toggleStatus",
    ])->name("settings.trusted-contacts.toggle-status");

    // Trusted account switching
    Route::post("trusted-access/switch-to/{user}", [
        \App\Http\Controllers\TrustedContactController::class,
        "switchToAccount",
    ])->name("trusted-access.switch-to");
    Route::post("trusted-access/switch-back", [
        \App\Http\Controllers\TrustedContactController::class,
        "switchBackToOwnAccount",
    ])->name("trusted-access.switch-back");

    Volt::route("settings/two-factor", "settings.two-factor")
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication() &&
                    Features::optionEnabled(
                        Features::twoFactorAuthentication(),
                        "confirmPassword",
                    ),
                ["password.confirm"],
                [],
            ),
        )
        ->name("two-factor.show");

    Route::get("seizures/live-tracker", [
        \App\Http\Controllers\SeizureController::class,
        "liveTracker",
    ])->name("seizures.live-tracker");

    // Short URL for emergency access to live seizure tracker
    Route::get("live", [
        \App\Http\Controllers\SeizureController::class,
        "liveTracker",
    ])->name("live");
    Route::resource(
        "seizures",
        \App\Http\Controllers\SeizureController::class,
    )->middleware("account.type:patient");
    Route::resource(
        "vitals",
        \App\Http\Controllers\VitalController::class,
    )->middleware("account.type:patient");

    // Medication routes - Patient accounts only
    Route::middleware("account.type:patient")->group(function () {
        Route::resource(
            "medications",
            \App\Http\Controllers\MedicationController::class,
        );
        Route::get("medications-schedule", [
            \App\Http\Controllers\MedicationController::class,
            "schedule",
        ])->name("medications.schedule");
        Route::get("medications-schedule/history", [
            \App\Http\Controllers\MedicationController::class,
            "scheduleHistory",
        ])->name("medications.schedule.history");
        Route::post("medications-log-taken", [
            \App\Http\Controllers\MedicationController::class,
            "logTaken",
        ])->name("medications.log-taken");
        Route::post("medications-log-skipped", [
            \App\Http\Controllers\MedicationController::class,
            "logSkipped",
        ])->name("medications.log-skipped");
        Route::post("medications/{medication}/schedules", [
            \App\Http\Controllers\MedicationController::class,
            "storeSchedule",
        ])->name("medications.schedules.store");
        Route::delete("medications/{medication}/schedules/{schedule}", [
            \App\Http\Controllers\MedicationController::class,
            "destroySchedule",
        ])->name("medications.schedules.destroy");
    });
});
