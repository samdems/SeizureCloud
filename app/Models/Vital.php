<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vital extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ["user_id", "type", "value", "recorded_at", "notes"];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "recorded_at" => "datetime",
    ];

    /**
     * Get the user that owns the vital record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
