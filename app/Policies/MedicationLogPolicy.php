<?php

namespace App\Policies;

use App\Models\MedicationLog;
use App\Models\User;

class MedicationLogPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MedicationLog $medicationLog): bool
    {
        return $user->id === $medicationLog->medication->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, MedicationLog $medicationLog): bool
    {
        return $user->id === $medicationLog->medication->user_id;
    }

    public function delete(User $user, MedicationLog $medicationLog): bool
    {
        return $user->id === $medicationLog->medication->user_id;
    }

    public function restore(User $user, MedicationLog $medicationLog): bool
    {
        return $user->id === $medicationLog->medication->user_id;
    }

    public function forceDelete(User $user, MedicationLog $medicationLog): bool
    {
        return $user->id === $medicationLog->medication->user_id;
    }
}
