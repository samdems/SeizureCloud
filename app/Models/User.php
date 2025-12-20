<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        "name",
        "email",
        "password",
        "account_type",
        "avatar_style",
        "created_via_invitation",
        "is_admin",
        "morning_time",
        "afternoon_time",
        "evening_time",
        "bedtime",
        "status_epilepticus_duration_minutes",
        "emergency_seizure_count",
        "emergency_seizure_timeframe_hours",
        "emergency_contact_info",
        "notify_medication_taken",
        "notify_seizure_added",
        "notify_trusted_contacts_medication",
        "notify_trusted_contacts_seizures",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        "password",
        "two_factor_secret",
        "two_factor_recovery_codes",
        "remember_token",
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "password" => "hashed",
            "morning_time" => "datetime:H:i",
            "afternoon_time" => "datetime:H:i",
            "evening_time" => "datetime:H:i",
            "bedtime" => "datetime:H:i",
            "status_epilepticus_duration_minutes" => "integer",
            "emergency_seizure_count" => "integer",
            "emergency_seizure_timeframe_hours" => "integer",
            "notify_medication_taken" => "boolean",
            "notify_seizure_added" => "boolean",
            "notify_trusted_contacts_medication" => "boolean",
            "notify_trusted_contacts_seizures" => "boolean",
            "created_via_invitation" => "boolean",
            "is_admin" => "boolean",
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(" ")
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode("");
    }

    /**
     * Get the user's avatar URL using DiceBear API
     */
    public function avatarUrl(int $size = 80): string
    {
        $style = $this->avatar_style ?? "initials";

        return "https://api.dicebear.com/8.x/{$style}/svg?" .
            http_build_query([
                "seed" => $this->email,
                "size" => $size,
                "backgroundColor" => "b6e3f4,c4b5fd,fbbf24,fb7185,34d399",
            ]);
    }

    /**
     * Get available avatar styles
     */
    public static function getAvailableAvatarStyles(): array
    {
        return [
            "personas" => "Personas - Modern illustrated avatars",
            "avataaars" => "Avataaars - Sketch style avatars",
            "adventurer" => "Adventurer - Adventure themed avatars",
            "big-ears" => "Big Ears - Cute cartoon avatars",
            "big-smile" => "Big Smile - Happy cartoon avatars",
            "bottts" => "Bottts - Robot avatars",
            "croodles" => "Croodles - Doodle style avatars",
            "initials" => "Initials - Letter based avatars",
            "micah" => "Micah - Simple illustrated faces",
            "miniavs" => "Miniavs - Minimal avatars",
            "pixel-art" => "Pixel Art - Retro pixel avatars",
        ];
    }

    public function seizures(): HasMany
    {
        return $this->hasMany(Seizure::class);
    }

    public function vitals(): HasMany
    {
        return $this->hasMany(Vital::class);
    }

    public function observations(): HasMany
    {
        return $this->hasMany(Observation::class);
    }

    public function vitalTypeThresholds(): HasMany
    {
        return $this->hasMany(VitalTypeThreshold::class);
    }

    public function medications(): HasMany
    {
        return $this->hasMany(Medication::class);
    }

    /**
     * Get trusted contacts that this user has granted access to
     */
    public function trustedContacts(): HasMany
    {
        return $this->hasMany(TrustedContact::class);
    }

    /**
     * Get invitations sent by this user
     */
    public function sentInvitations(): HasMany
    {
        return $this->hasMany(UserInvitation::class, "inviter_id");
    }

    /**
     * Get accounts that this user has trusted access to
     */
    public function trustedAccounts(): HasMany
    {
        return $this->hasMany(TrustedContact::class, "trusted_user_id");
    }

    /**
     * Get users who have trusted access to this account
     */
    public function trustedUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            "trusted_contacts",
            "user_id",
            "trusted_user_id",
        )
            ->wherePivot("is_active", true)
            ->withPivot([
                "nickname",
                "access_note",
                "granted_at",
                "expires_at",
            ]);
    }

    /**
     * Get accounts this user has trusted access to
     */
    public function accessibleAccounts(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            "trusted_contacts",
            "trusted_user_id",
            "user_id",
        )
            ->wherePivot("is_active", true)
            ->withPivot([
                "nickname",
                "access_note",
                "granted_at",
                "expires_at",
            ]);
    }

    /**
     * Get accounts this user has valid trusted access to (active and not expired)
     */
    public function validAccessibleAccounts(): BelongsToMany
    {
        return $this->accessibleAccounts()->whereRaw(
            "(trusted_contacts.expires_at IS NULL OR trusted_contacts.expires_at > ?)",
            [now()],
        );
    }

    /**
     * Check if this user has trusted access to another user's account
     */
    public function hasTrustedAccessTo(User $user): bool
    {
        return $this->trustedAccounts()
            ->where("user_id", $user->id)
            ->valid()
            ->exists();
    }

    /**
     * Check if another user has trusted access to this account
     */
    public function hasTrustedAccessFrom(User $user): bool
    {
        return $this->trustedContacts()
            ->where("trusted_user_id", $user->id)
            ->valid()
            ->exists();
    }

    /**
     * Get active trusted contact relationship for a user
     */
    public function getTrustedContactFor(User $trustedUser): ?TrustedContact
    {
        return $this->trustedContacts()
            ->where("trusted_user_id", $trustedUser->id)
            ->valid()
            ->first();
    }

    public function getTimePeriod(string $time): string
    {
        $timeObj = \Carbon\Carbon::createFromFormat("H:i", $time);
        $morning = \Carbon\Carbon::createFromFormat(
            "H:i",
            $this->morning_time->format("H:i"),
        );
        $afternoon = \Carbon\Carbon::createFromFormat(
            "H:i",
            $this->afternoon_time->format("H:i"),
        );
        $evening = \Carbon\Carbon::createFromFormat(
            "H:i",
            $this->evening_time->format("H:i"),
        );
        $bedtime = \Carbon\Carbon::createFromFormat(
            "H:i",
            $this->bedtime->format("H:i"),
        );

        if ($timeObj < $afternoon) {
            return "morning";
        } elseif ($timeObj < $evening) {
            return "afternoon";
        } elseif ($timeObj < $bedtime) {
            return "evening";
        } else {
            return "bedtime";
        }
    }

    /**
     * Check if a seizure qualifies as possible status epilepticus (emergency duration)
     */
    public function isStatusEpilepticus(Seizure $seizure): bool
    {
        if (!$seizure->calculated_duration) {
            return false;
        }

        return $seizure->calculated_duration >=
            $this->status_epilepticus_duration_minutes;
    }

    /**
     * Check if there's an emergency seizure cluster
     */
    public function hasEmergencySeizureCluster(
        \Carbon\Carbon $fromTime = null,
    ): bool {
        $fromTime =
            $fromTime ??
            now()->subHours($this->emergency_seizure_timeframe_hours);

        $seizureCount = $this->seizures()
            ->where("start_time", ">=", $fromTime)
            ->count();

        return $seizureCount >= $this->emergency_seizure_count;
    }

    /**
     * Get emergency status for a seizure or seizure event
     */
    public function getEmergencyStatus(Seizure $seizure): array
    {
        $isStatusEpilepticus = $this->isStatusEpilepticus($seizure);

        // Check for cluster emergency within configured timeframe
        $halfTimeframe = intval($this->emergency_seizure_timeframe_hours / 2);
        $eventStart = $seizure->start_time->copy()->subHours($halfTimeframe);
        $eventEnd = $seizure->start_time->copy()->addHours($halfTimeframe);

        $eventSeizures = $this->seizures()
            ->whereBetween("start_time", [$eventStart, $eventEnd])
            ->count();

        $isClusterEmergency = $eventSeizures >= $this->emergency_seizure_count;

        return [
            "is_emergency" => $isStatusEpilepticus || $isClusterEmergency,
            "status_epilepticus" => $isStatusEpilepticus,
            "cluster_emergency" => $isClusterEmergency,
            "cluster_count" => $eventSeizures,
            "duration_threshold" => $this->status_epilepticus_duration_minutes,
            "count_threshold" => $this->emergency_seizure_count,
            "timeframe_hours" => $this->emergency_seizure_timeframe_hours,
        ];
    }

    /**
     * Get emergency contact information formatted for display
     */
    public function getFormattedEmergencyContact(): ?string
    {
        if (!$this->emergency_contact_info) {
            return null;
        }

        // Basic formatting for display
        return nl2br(e($this->emergency_contact_info));
    }

    /**
     * Check if this user is a patient account (can track seizures)
     */
    public function isPatient(): bool
    {
        return $this->account_type === "patient";
    }

    /**
     * Check if this user is a carer account (trusted access only)
     */
    public function isCarer(): bool
    {
        return $this->account_type === "carer";
    }

    /**
     * Check if this user is a medical professional account
     */
    public function isMedical(): bool
    {
        return $this->account_type === "medical";
    }

    /**
     * Check if this user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    /**
     * Set admin status for this user
     */
    public function setAdminStatus(bool $isAdmin): void
    {
        $this->is_admin = $isAdmin;
        $this->save();
    }

    /**
     * Check if this user can track their own seizures
     */
    public function canTrackSeizures(): bool
    {
        return $this->account_type === "patient";
    }

    /**
     * Get available account types
     */
    public static function getAccountTypes(): array
    {
        return [
            "patient" => "Patient - Can track seizures and manage own data",
            "carer" => "Carer - Trusted access to patient accounts only",
            "medical" => "Medical Professional - Healthcare provider access",
        ];
    }

    /**
     * Check if this user was created through an invitation
     */
    public function wasCreatedThroughInvitation(): bool
    {
        return $this->created_via_invitation;
    }

    /**
     * Override sendEmailVerificationNotification to prevent sending
     * verification emails to users who were auto-verified through invitations
     */
    public function sendEmailVerificationNotification()
    {
        // Don't send verification emails to users who were created through invitations
        // since their email was already verified during the invitation acceptance process
        if ($this->wasCreatedThroughInvitation()) {
            Log::info("Skipping email verification for invited user", [
                "user_id" => $this->id,
                "email" => $this->email,
                "reason" => "User was created through invitation",
                "already_verified" => $this->hasVerifiedEmail(),
            ]);
            return;
        }

        // Also skip if user is already verified (regardless of how they were created)
        if ($this->hasVerifiedEmail()) {
            Log::info("Skipping email verification for already verified user", [
                "user_id" => $this->id,
                "email" => $this->email,
                "reason" => "User email is already verified",
            ]);
            return;
        }

        // Send normal verification email for other users
        parent::sendEmailVerificationNotification();
    }
}
