<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class MedicationReminderLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'medication_id',
        'medication_schedule_id',
        'reminder_type',
        'sent_at',
        'recipient_email',
        'recipient_type',
        'recipient_user_id',
        'overdue_count',
        'due_count',
        'medication_data',
        'notification_id',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'medication_data' => 'array',
        'overdue_count' => 'integer',
        'due_count' => 'integer',
    ];

    /**
     * The user who owns the medication (patient)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The medication that the reminder was for
     */
    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }

    /**
     * The specific medication schedule that triggered the reminder
     */
    public function medicationSchedule(): BelongsTo
    {
        return $this->belongsTo(MedicationSchedule::class);
    }

    /**
     * The user who received the reminder (could be patient or trusted contact)
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    /**
     * Check if enough time has passed since last reminder for this medication schedule
     *
     * @param int $medicationScheduleId
     * @param string $reminderType
     * @param int $cooldownHours Default 2 hours
     * @return bool
     */
    public static function canSendReminder(
        int $medicationScheduleId,
        string $reminderType = 'overdue',
        int $cooldownHours = 2
    ): bool {
        $lastReminder = static::where('medication_schedule_id', $medicationScheduleId)
            ->where('reminder_type', $reminderType)
            ->where('sent_at', '>=', Carbon::now()->subHours($cooldownHours))
            ->latest('sent_at')
            ->first();

        return $lastReminder === null;
    }

    /**
     * Check if we can send any type of reminder for this medication schedule
     *
     * @param int $medicationScheduleId
     * @param int $cooldownHours Default 2 hours
     * @return bool
     */
    public static function canSendAnyReminder(
        int $medicationScheduleId,
        int $cooldownHours = 2
    ): bool {
        $lastReminder = static::where('medication_schedule_id', $medicationScheduleId)
            ->where('sent_at', '>=', Carbon::now()->subHours($cooldownHours))
            ->latest('sent_at')
            ->first();

        return $lastReminder === null;
    }

    /**
     * Check how many reminders have been sent today for this medication schedule
     *
     * @param int $medicationScheduleId
     * @return int
     */
    public static function todayReminderCount(int $medicationScheduleId): int
    {
        return static::where('medication_schedule_id', $medicationScheduleId)
            ->whereDate('sent_at', Carbon::today())
            ->count();
    }

    /**
     * Check if daily reminder limit has been reached
     *
     * @param int $medicationScheduleId
     * @param int $maxPerDay Default 6 reminders per day
     * @return bool
     */
    public static function hasReachedDailyLimit(
        int $medicationScheduleId,
        int $maxPerDay = 6
    ): bool {
        return static::todayReminderCount($medicationScheduleId) >= $maxPerDay;
    }

    /**
     * Get the last reminder sent for this medication schedule
     *
     * @param int $medicationScheduleId
     * @return MedicationReminderLog|null
     */
    public static function getLastReminder(int $medicationScheduleId): ?MedicationReminderLog
    {
        return static::where('medication_schedule_id', $medicationScheduleId)
            ->latest('sent_at')
            ->first();
    }

    /**
     * Log a sent medication reminder
     *
     * @param array $data
     * @return MedicationReminderLog
     */
    public static function logReminder(array $data): MedicationReminderLog
    {
        return static::create(array_merge([
            'sent_at' => Carbon::now(),
            'reminder_type' => 'overdue',
            'recipient_type' => 'patient',
            'overdue_count' => 0,
            'due_count' => 0,
        ], $data));
    }

    /**
     * Get reminder statistics for a user
     *
     * @param int $userId
     * @param Carbon|null $fromDate
     * @return array
     */
    public static function getUserStats(int $userId, Carbon $fromDate = null): array
    {
        $query = static::where('user_id', $userId);

        if ($fromDate) {
            $query->where('sent_at', '>=', $fromDate);
        } else {
            $query->whereDate('sent_at', '>=', Carbon::now()->subDays(30));
        }

        $logs = $query->get();

        return [
            'total_reminders' => $logs->count(),
            'overdue_reminders' => $logs->where('reminder_type', 'overdue')->count(),
            'due_reminders' => $logs->where('reminder_type', 'due')->count(),
            'unique_medications' => $logs->pluck('medication_id')->unique()->count(),
            'first_reminder' => $logs->min('sent_at'),
            'last_reminder' => $logs->max('sent_at'),
            'average_per_day' => $logs->count() / ($fromDate ? Carbon::now()->diffInDays($fromDate) + 1 : 30),
        ];
    }

    /**
     * Clean up old reminder logs (older than specified days)
     *
     * @param int $olderThanDays Default 90 days
     * @return int Number of deleted records
     */
    public static function cleanup(int $olderThanDays = 90): int
    {
        return static::where('sent_at', '<', Carbon::now()->subDays($olderThanDays))
            ->delete();
    }

    /**
     * Scope to filter by reminder type
     */
    public function scopeOfType($query, string $reminderType)
    {
        return $query->where('reminder_type', $reminderType);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeBetweenDates($query, Carbon $startDate, Carbon $endDate)
    {
        return $query->whereBetween('sent_at', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('sent_at', Carbon::today());
    }

    /**
     * Scope to filter by recipient type
     */
    public function scopeForRecipientType($query, string $recipientType)
    {
        return $query->where('recipient_type', $recipientType);
    }
}
