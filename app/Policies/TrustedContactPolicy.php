<?php

namespace App\Policies;

use App\Models\TrustedContact;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TrustedContactPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Users can view their own trusted contacts
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TrustedContact $trustedContact): bool
    {
        // User can view if they own the trusted contact or if they are the trusted user
        return $trustedContact->user_id === $user->id ||
            $trustedContact->trusted_user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can create trusted contacts
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TrustedContact $trustedContact): bool
    {
        // Only the owner of the account can update trusted contact settings
        return $trustedContact->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TrustedContact $trustedContact): bool
    {
        // Both the owner and the trusted user can delete the relationship
        return $trustedContact->user_id === $user->id ||
            $trustedContact->trusted_user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TrustedContact $trustedContact): bool
    {
        return $trustedContact->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(
        User $user,
        TrustedContact $trustedContact,
    ): bool {
        return $trustedContact->user_id === $user->id;
    }
}
