<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\NotificationSettingsUpdateRequest;

class NotificationController extends Controller
{
    /**
     * Show the notification settings page
     */
    public function settings()
    {
        $user = Auth::user();
        return view("settings.notifications", compact("user"));
    }

    /**
     * Update notification settings
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            "notify_medication_taken" => "boolean",
            "notify_seizure_added" => "boolean",
            "notify_trusted_contacts_medication" => "boolean",
            "notify_trusted_contacts_seizures" => "boolean",
        ]);

        // Convert null values to false for checkboxes
        $validated = array_map(function ($value) {
            return $value ?? false;
        }, $validated);

        $user->update($validated);

        return redirect()
            ->route("settings.notifications")
            ->with("success", "Notification settings updated successfully.");
    }
}
