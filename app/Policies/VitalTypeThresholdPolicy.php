<?php

namespace App\Policies;

use App\Models\VitalTypeThreshold;
use App\Models\User;

class VitalTypeThresholdPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, VitalTypeThreshold $vitalTypeThreshold): bool
    {
        return $user->id === $vitalTypeThreshold->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, VitalTypeThreshold $vitalTypeThreshold): bool
    {
        return $user->id === $vitalTypeThreshold->user_id;
    }

    public function delete(User $user, VitalTypeThreshold $vitalTypeThreshold): bool
    {
        return $user->id === $vitalTypeThreshold->user_id;
    }

    public function restore(User $user, VitalTypeThreshold $vitalTypeThreshold): bool
    {
        return $user->id === $vitalTypeThreshold->user_id;
    }

    public function forceDelete(User $user, VitalTypeThreshold $vitalTypeThreshold): bool
    {
        return $user->id === $vitalTypeThreshold->user_id;
    }
}
