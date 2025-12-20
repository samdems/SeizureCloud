<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserInvitation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        "inviter_id",
        "email",
        "token",
        "nickname",
        "access_note",
        "expires_at",
        "invitation_expires_at",
        "accepted_at",
        "accepted_user_id",
        "status",
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            "expires_at" => "datetime",
            "invitation_expires_at" => "datetime",
            "accepted_at" => "datetime",
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invitation) {
            if (!$invitation->token) {
                $invitation->token = Str::random(60);
            }

            if (!$invitation->invitation_expires_at) {
                $invitation->invitation_expires_at = now()->addDays(7); // Default 7 days
            }
        });
    }

    /**
     * The user who sent the invitation
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, "inviter_id");
    }

    /**
     * The user who accepted the invitation (if any)
     */
    public function acceptedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, "accepted_user_id");
    }

    /**
     * Scope to only include pending invitations
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where("status", "pending");
    }

    /**
     * Scope to only include accepted invitations
     */
    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where("status", "accepted");
    }

    /**
     * Scope to only include non-expired invitations
     */
    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where("invitation_expires_at", ">", now());
    }

    /**
     * Scope to only include valid (pending and not expired) invitations
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query->pending()->notExpired();
    }

    /**
     * Check if the invitation is valid (pending and not expired)
     */
    public function isValid(): bool
    {
        return $this->status === "pending" &&
            $this->invitation_expires_at->isFuture();
    }

    /**
     * Check if the invitation is expired
     */
    public function isExpired(): bool
    {
        return $this->invitation_expires_at->isPast();
    }

    /**
     * Mark the invitation as accepted
     */
    public function markAsAccepted(User $user): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        $this->update([
            "status" => "accepted",
            "accepted_at" => now(),
            "accepted_user_id" => $user->id,
        ]);

        return true;
    }

    /**
     * Mark the invitation as cancelled
     */
    public function markAsCancelled(): bool
    {
        if ($this->status !== "pending") {
            return false;
        }

        $this->update(["status" => "cancelled"]);
        return true;
    }

    /**
     * Mark the invitation as expired
     */
    public function markAsExpired(): bool
    {
        if ($this->status !== "pending") {
            return false;
        }

        $this->update(["status" => "expired"]);
        return true;
    }

    /**
     * Generate invitation URL
     */
    public function getInvitationUrl(): string
    {
        return route("invitation.show", ["token" => $this->token]);
    }

    /**
     * Get display name for the invitation
     */
    public function getDisplayName(): string
    {
        return $this->nickname ?: "Trusted Contact";
    }

    /**
     * Check if invitation can be resent
     */
    public function canBeResent(): bool
    {
        return $this->status === "pending" &&
            $this->invitation_expires_at->isPast();
    }

    /**
     * Resend the invitation by updating expiration and regenerating token
     */
    public function resend(): bool
    {
        if (!$this->canBeResent()) {
            return false;
        }

        $this->update([
            "token" => Str::random(60),
            "invitation_expires_at" => now()->addDays(7),
            "status" => "pending",
        ]);

        return true;
    }

    /**
     * Create trusted contact relationship from accepted invitation
     */
    public function createTrustedContact(): ?TrustedContact
    {
        if ($this->status !== "accepted" || !$this->accepted_user_id) {
            return null;
        }

        // Check if trusted contact already exists
        $existingContact = TrustedContact::where("user_id", $this->inviter_id)
            ->where("trusted_user_id", $this->accepted_user_id)
            ->first();

        if ($existingContact) {
            return $existingContact;
        }

        return TrustedContact::create([
            "user_id" => $this->inviter_id,
            "trusted_user_id" => $this->accepted_user_id,
            "nickname" => $this->nickname,
            "access_note" => $this->access_note,
            "expires_at" => $this->expires_at,
            "granted_at" => $this->accepted_at,
            "is_active" => true,
        ]);
    }
}
