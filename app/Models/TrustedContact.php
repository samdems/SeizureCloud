<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class TrustedContact extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        "user_id",
        "trusted_user_id",
        "nickname",
        "access_note",
        "is_active",
        "granted_at",
        "expires_at",
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            "is_active" => "boolean",
            "granted_at" => "datetime",
            "expires_at" => "datetime",
        ];
    }

    /**
     * The user who owns the account (the one being trusted to)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The user who has trusted access (the trusted contact)
     */
    public function trustedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, "trusted_user_id");
    }

    /**
     * Scope to only include active trusted contacts
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where("is_active", true);
    }

    /**
     * Scope to only include non-expired trusted contacts
     */
    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(function ($query) {
            $query->whereNull("expires_at")->orWhere("expires_at", ">", now());
        });
    }

    /**
     * Scope to get valid (active and not expired) trusted contacts
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query->active()->notExpired();
    }

    /**
     * Check if the trusted contact is currently valid
     */
    public function isValid(): bool
    {
        return $this->is_active &&
            ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /**
     * Get display name for the trusted contact
     */
    public function getDisplayName(): string
    {
        return $this->nickname ?: $this->trustedUser->name;
    }

    /**
     * Check if the trusted contact is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
