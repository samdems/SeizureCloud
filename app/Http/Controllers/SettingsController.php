<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\TimePreferencesUpdateRequest;
use App\Http\Requests\AppearanceUpdateRequest;
use App\Http\Requests\AccountDeleteRequest;
use App\Http\Requests\EmergencySettingsUpdateRequest;

class SettingsController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        return view("settings.profile", compact("user"));
    }

    public function updateProfile(ProfileUpdateRequest $request)
    {
        $user = Auth::user();

        $validated = $request->validated();

        if ($user->isDirty("email") && $validated["email"] !== $user->email) {
            $user->email_verified_at = null;
        }

        $user->update($validated);

        return redirect()
            ->route("settings.profile")
            ->with("success", "Profile updated successfully.");
    }

    public function password()
    {
        return view("settings.password");
    }

    public function updatePassword(PasswordUpdateRequest $request)
    {
        $validated = $request->validated();

        Auth::user()->update([
            "password" => Hash::make($validated["password"]),
        ]);

        return redirect()
            ->route("settings.password")
            ->with("success", "Password updated successfully.");
    }

    public function timePreferences()
    {
        $user = Auth::user();
        return view("settings.time-preferences", compact("user"));
    }

    public function updateTimePreferences(TimePreferencesUpdateRequest $request)
    {
        $user = Auth::user();

        $validated = $request->validated();

        $user->update($validated);

        return redirect()
            ->route("settings.time-preferences")
            ->with("success", "Time preferences updated successfully.");
    }

    public function appearance()
    {
        return view("settings.appearance");
    }

    public function updateAppearance(AppearanceUpdateRequest $request)
    {
        $validated = $request->validated();

        session(["appearance" => $validated["appearance"]]);

        return redirect()
            ->route("settings.appearance")
            ->with("success", "Appearance settings updated successfully.");
    }

    public function avatar()
    {
        return view("settings.avatar");
    }

    public function destroy(AccountDeleteRequest $request)
    {
        $validated = $request->validated();

        $user = Auth::user();
        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect("/")->with("success", "Your account has been deleted.");
    }

    public function emergencySettings()
    {
        $user = Auth::user();
        return view("settings.emergency-settings", compact("user"));
    }

    public function updateEmergencySettings(
        EmergencySettingsUpdateRequest $request,
    ) {
        $user = Auth::user();

        $validated = $request->validated();

        $user->update($validated);

        return redirect()
            ->route("settings.emergency-settings")
            ->with("success", "Emergency settings updated successfully.");
    }
}
