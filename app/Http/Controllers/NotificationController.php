<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotificationSettingsUpdateRequest;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Show the notification settings page
     */
    public function settings()
    {
        $user = Auth::user();

        return view('settings.notifications', compact('user'));
    }

    /**
     * Update notification settings
     */
    public function updateSettings(NotificationSettingsUpdateRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->update($request->validated());

        return redirect()
            ->route('settings.notifications')
            ->with('success', 'Notification settings updated successfully.');
    }
}
