<?php

namespace App\Http\Controllers;

use App\Models\TrustedContact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Requests\TrustedContactStoreRequest;
use App\Http\Requests\TrustedContactUpdateRequest;

class TrustedContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        // Get trusted contacts this user has granted access to
        $trustedContacts = $user
            ->trustedContacts()
            ->with("trustedUser")
            ->orderBy("created_at", "desc")
            ->get();

        // Get accounts this user has trusted access to
        $accessibleAccounts = $user
            ->trustedAccounts()
            ->with("user")
            ->valid()
            ->orderBy("created_at", "desc")
            ->get();

        return view(
            "settings.trusted-contacts.index",
            compact("trustedContacts", "accessibleAccounts"),
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("settings.trusted-contacts.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TrustedContactStoreRequest $request)
    {
        $user = auth()->user();

        $validated = $request->validated();

        // Find the trusted user
        $trustedUser = User::where("email", $validated["email"])->first();

        if (!$trustedUser) {
            return back()->withErrors([
                "email" => "User with this email not found.",
            ]);
        }

        if ($trustedUser->id === $user->id) {
            return back()->withErrors([
                "email" => "You cannot add yourself as a trusted contact.",
            ]);
        }

        $trustedContact = $user->trustedContacts()->create([
            "trusted_user_id" => $trustedUser->id,
            "nickname" => $validated["nickname"],
            "access_note" => $validated["access_note"],
            "expires_at" => $validated["expires_at"],
            "granted_at" => now(),
            "is_active" => true,
        ]);

        return redirect()
            ->route("settings.trusted-contacts.index")
            ->with(
                "success",
                "Trusted access granted to {$trustedUser->name}.",
            );
    }

    /**
     * Display the specified resource.
     */
    public function show(TrustedContact $trustedContact)
    {
        $this->authorize("view", $trustedContact);

        return view(
            "settings.trusted-contacts.show",
            compact("trustedContact"),
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TrustedContact $trustedContact)
    {
        $this->authorize("update", $trustedContact);

        return view(
            "settings.trusted-contacts.edit",
            compact("trustedContact"),
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        TrustedContactUpdateRequest $request,
        TrustedContact $trustedContact,
    ) {
        $this->authorize("update", $trustedContact);

        $validated = $request->validated();

        $trustedContact->update($validated);

        return redirect()
            ->route("settings.trusted-contacts.index")
            ->with("success", "Trusted contact updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TrustedContact $trustedContact)
    {
        $this->authorize("delete", $trustedContact);

        $trustedUser = $trustedContact->trustedUser;
        $trustedContact->delete();

        return redirect()
            ->route("settings.trusted-contacts.index")
            ->with(
                "success",
                "Trusted access revoked from {$trustedUser->name}.",
            );
    }

    /**
     * Toggle the active status of a trusted contact
     */
    public function toggleStatus(TrustedContact $trustedContact)
    {
        $this->authorize("update", $trustedContact);

        $trustedContact->update([
            "is_active" => !$trustedContact->is_active,
        ]);

        $status = $trustedContact->is_active ? "activated" : "deactivated";

        return redirect()
            ->route("settings.trusted-contacts.index")
            ->with("success", "Trusted contact {$status} successfully.");
    }

    /**
     * Switch to viewing another user's account (trusted access)
     */
    public function switchToAccount(User $user)
    {
        $currentUser = auth()->user();

        // Prevent switching to own account through this method
        if ($currentUser->id === $user->id) {
            return redirect()
                ->route("dashboard")
                ->with("error", "You are already viewing your own account.");
        }

        // Check if current user has trusted access to this account
        if (!$currentUser->hasTrustedAccessTo($user)) {
            abort(403, "You do not have trusted access to this account.");
        }

        // Verify the trusted contact relationship is still valid
        $trustedContact = $currentUser
            ->trustedAccounts()
            ->where("user_id", $user->id)
            ->valid()
            ->first();

        if (!$trustedContact) {
            return redirect()
                ->route("settings.trusted-contacts.index")
                ->with(
                    "error",
                    "Your trusted access to {$user->name}'s account has expired or been revoked.",
                );
        }

        // Store the original user ID in session
        session(["viewing_as_trusted_contact" => true]);
        session(["original_user_id" => $currentUser->id]);
        session(["trusted_user_id" => $user->id]);

        return redirect()
            ->route("dashboard")
            ->with("success", "Now viewing {$user->name}'s account.");
    }

    /**
     * Switch back to own account
     */
    public function switchBackToOwnAccount()
    {
        if (!session("viewing_as_trusted_contact")) {
            return redirect()
                ->route("dashboard")
                ->with("info", "You are already viewing your own account.");
        }

        $originalUserId = session("original_user_id");
        $originalUser = $originalUserId
            ? \App\Models\User::find($originalUserId)
            : null;

        session()->forget([
            "viewing_as_trusted_contact",
            "original_user_id",
            "trusted_user_id",
        ]);

        $message = $originalUser
            ? "Switched back to your account ({$originalUser->name})."
            : "Switched back to your own account.";

        return redirect()->route("dashboard")->with("success", $message);
    }
}
