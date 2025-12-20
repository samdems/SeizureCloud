<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserInvitation;

class UserInvitationPolicy
{
    /**
     * Determine whether the user can view any invitations.
     */
    public function viewAny(User $user): bool
    {
        return true; // Users can view their own invitations
    }

    /**
     * Determine whether the user can view the invitation.
     */
    public function view(User $user, UserInvitation $invitation): bool
    {
        return $user->id === $invitation->inviter_id ||
               $user->email === $invitation->email;
    }

    /**
     * Determine whether the user can create invitations.
     */
    public function create(User $user): bool
    {
        return true; // All users can send invitations
    }

    /**
     * Determine whether the user can update the invitation.
     */
    public function update(User $user, UserInvitation $invitation): bool
    {
        return $user->id === $invitation->inviter_id &&
               $invitation->status === 'pending';
    }

    /**
     * Determine whether the user can delete the invitation.
     */
    public function delete(User $user, UserInvitation $invitation): bool
    {
        return $user->id === $invitation->inviter_id;
    }

    /**
     * Determine whether the user can resend the invitation.
     */
    public function resend(User $user, UserInvitation $invitation): bool
    {
        return $user->id === $invitation->inviter_id &&
               $invitation->canBeResent();
    }

    /**
     * Determine whether the user can cancel the invitation.
     */
    public function cancel(User $user, UserInvitation $invitation): bool
    {
        return $user->id === $invitation->inviter_id &&
               $invitation->status === 'pending';
    }

    /**
     * Determine whether the user can accept the invitation.
     */
    public function accept(User $user, UserInvitation $invitation): bool
    {
        return $user->email === $invitation->email &&
               $invitation->isValid();
    }
}
