<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Observation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "user_id",
        "title",
        "description",
        "observed_at",
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "observed_at" => "datetime",
    ];

    /**
     * Get the user that owns the observation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
