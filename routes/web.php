<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get("/", function () {
    return view("welcome");
})->name("home");

// Legal pages
Route::get("privacy", [
    \App\Http\Controllers\LegalController::class,
    "privacy",
])->name("legal.privacy");
Route::get("terms", [
    \App\Http\Controllers\LegalController::class,
    "terms",
])->name("legal.terms");

// Public invitation routes (accessible without authentication)
Route::get("invitations/{token}", [
    \App\Http\Controllers\InvitationController::class,
    "show",
])->name("invitation.show");
Route::post("invitations/{token}/accept", [
    \App\Http\Controllers\InvitationController::class,
    "accept",
])->name("invitation.accept");
Route::post("invitations/{token}/decline", [
    \App\Http\Controllers\InvitationController::class,
    "decline",
])->name("invitation.decline");
Route::post("invitations/{token}/accept-after-registration", [
    \App\Http\Controllers\InvitationController::class,
    "acceptAfterRegistration",
])->name("invitation.accept-after-registration");

Route::view("dashboard", "dashboard")
    ->middleware(["auth", "custom.verified"])
    ->name("dashboard");

Route::middleware(["auth"])->group(function () {
    // Email verification routes for debugging and fallback
    Route::post("verify-invited-user", [
        \App\Http\Controllers\EmailVerificationController::class,
        "verifyInvitedUser",
    ])->name("verify-invited-user");

    Route::get("verification-status", [
        \App\Http\Controllers\EmailVerificationController::class,
        "status",
    ])->name("verification.status");

    Route::post("force-verify", [
        \App\Http\Controllers\EmailVerificationController::class,
        "forceVerify",
    ])->name("verification.force");

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

    // Notification settings
    Route::get("settings/notifications", [
        \App\Http\Controllers\NotificationController::class,
        "settings",
    ])->name("settings.notifications");
    Route::put("settings/notifications", [
        \App\Http\Controllers\NotificationController::class,
        "updateSettings",
    ])->name("settings.notifications.update");

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

    // Invitation management
    Route::get("invitations/manage", [
        \App\Http\Controllers\InvitationController::class,
        "manage",
    ])->name("invitations.manage");
    Route::post("invitations/{invitation}/resend", [
        \App\Http\Controllers\InvitationController::class,
        "resend",
    ])->name("invitations.resend");
    Route::post("invitations/{invitation}/cancel", [
        \App\Http\Controllers\InvitationController::class,
        "cancel",
    ])->name("invitations.cancel");

    // Email preview for testing (local only)
    Route::get("invitations/{token}/preview", [
        \App\Http\Controllers\InvitationController::class,
        "preview",
    ])->name("invitations.preview");

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

    Route::resource(
        "seizures",
        \App\Http\Controllers\SeizureController::class,
    )->middleware("account.type:patient");

    // PDF export routes for seizures
    Route::get("seizures/export/monthly-pdf", [
        \App\Http\Controllers\SeizureController::class,
        "exportMonthlyPdf",
    ])
        ->name("seizures.export.monthly-pdf")
        ->middleware("account.type:patient");

    Route::get("seizures/{seizure}/export-pdf", [
        \App\Http\Controllers\SeizureController::class,
        "exportSinglePdf",
    ])
        ->name("seizures.export.single-pdf")
        ->middleware("account.type:patient");

    Route::get("seizures/export/comprehensive-pdf", [
        \App\Http\Controllers\SeizureController::class,
        "exportMonthlyComprehensivePdf",
    ])
        ->name("seizures.export.comprehensive-pdf")
        ->middleware("account.type:patient");
    Route::resource(
        "vitals",
        \App\Http\Controllers\VitalController::class,
    )->middleware("account.type:patient");

    Route::get("vitals-thresholds", [
        \App\Http\Controllers\VitalController::class,
        "thresholds",
    ])
        ->name("vitals.thresholds")
        ->middleware("account.type:patient");

    Route::put("vitals-thresholds", [
        \App\Http\Controllers\VitalController::class,
        "updateThresholds",
    ])
        ->name("vitals.thresholds.update")
        ->middleware("account.type:patient");

    Route::resource(
        "observations",
        \App\Http\Controllers\ObservationController::class,
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
        Route::post("medications-log-bulk-taken", [
            \App\Http\Controllers\MedicationController::class,
            "logBulkTaken",
        ])->name("medications.log-bulk-taken");
        Route::put("medications-log/{medicationLog}", [
            \App\Http\Controllers\MedicationController::class,
            "updateLog",
        ])->name("medications.log-update");
        Route::delete("medications-log/{medicationLog}", [
            \App\Http\Controllers\MedicationController::class,
            "destroyLog",
        ])->name("medications.log-destroy");
        Route::post("medications/{medication}/schedules", [
            \App\Http\Controllers\MedicationController::class,
            "storeSchedule",
        ])->name("medications.schedules.store");
        Route::delete("medications/{medication}/schedules/{schedule}", [
            \App\Http\Controllers\MedicationController::class,
            "destroySchedule",
        ])->name("medications.schedules.destroy");
    });

    // Admin routes - only accessible to admin users
    Route::middleware("admin")
        ->prefix("admin")
        ->name("admin.")
        ->group(function () {
            Route::get("/", [
                \App\Http\Controllers\AdminController::class,
                "index",
            ])->name("dashboard");

            // User management
            Route::get("users", [
                \App\Http\Controllers\AdminController::class,
                "users",
            ])->name("users.index");
            Route::get("users/{user}", [
                \App\Http\Controllers\AdminController::class,
                "showUser",
            ])->name("users.show");
            Route::get("users/{user}/edit", [
                \App\Http\Controllers\AdminController::class,
                "editUser",
            ])->name("users.edit");
            Route::put("users/{user}", [
                \App\Http\Controllers\AdminController::class,
                "updateUser",
            ])->name("users.update");
            Route::post("users/{user}/toggle-admin", [
                \App\Http\Controllers\AdminController::class,
                "toggleAdmin",
            ])->name("users.toggle-admin");
            Route::post("users/{user}/deactivate", [
                \App\Http\Controllers\AdminController::class,
                "deactivateUser",
            ])->name("users.deactivate");
            Route::post("users/{user}/activate", [
                \App\Http\Controllers\AdminController::class,
                "activateUser",
            ])->name("users.activate");
            Route::delete("users/{user}", [
                \App\Http\Controllers\AdminController::class,
                "deleteUser",
            ])->name("users.delete");

            // System management
            Route::get("settings", [
                \App\Http\Controllers\AdminController::class,
                "settings",
            ])->name("settings");
            Route::get("logs", [
                \App\Http\Controllers\AdminController::class,
                "logs",
            ])->name("logs");
            Route::get("export/users", [
                \App\Http\Controllers\AdminController::class,
                "exportUsers",
            ])->name("export.users");

            // Email logs
            Route::get("email-logs", [
                \App\Http\Controllers\AdminController::class,
                "emailLogs",
            ])->name("email-logs");
            Route::get("users/{user}/email-logs", [
                \App\Http\Controllers\AdminController::class,
                "userEmailLogs",
            ])->name("users.email-logs");
            Route::get("email-logs/{emailLog}", [
                \App\Http\Controllers\AdminController::class,
                "getEmailLog",
            ])->name("email-logs.show");
        });
});
