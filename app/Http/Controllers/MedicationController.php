<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use App\Models\MedicationLog;
use App\Models\MedicationSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\MedicationStoreRequest;
use App\Http\Requests\MedicationUpdateRequest;
use App\Http\Requests\MedicationLogTakenRequest;
use App\Http\Requests\MedicationLogSkippedRequest;
use App\Http\Requests\MedicationLogBulkTakenRequest;
use App\Http\Requests\MedicationLogUpdateRequest;
use App\Http\Requests\MedicationScheduleStoreRequest;
use App\Notifications\MedicationNotifcation;

class MedicationController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $medications = Auth::user()
            ->medications()
            ->with([
                "schedules",
                "logs" => function ($query) {
                    $query->latest()->limit(5);
                },
            ])
            ->latest()
            ->get();
        return view("medications.index", compact("medications"));
    }

    public function create()
    {
        return view("medications.create");
    }

    public function store(MedicationStoreRequest $request)
    {
        $validated = $request->validated();
        $validated["user_id"] = Auth::id();

        Medication::create($validated);

        return redirect()
            ->route("medications.index")
            ->with("success", "Medication added successfully.");
    }

    public function show(Medication $medication)
    {
        $this->authorize("view", $medication);
        $medication->load([
            "schedules",
            "logs" => function ($query) {
                $query->latest()->limit(50);
            },
        ]);

        return view("medications.show", compact("medication"));
    }

    public function edit(Medication $medication)
    {
        $this->authorize("update", $medication);
        return view("medications.edit", compact("medication"));
    }

    public function update(
        MedicationUpdateRequest $request,
        Medication $medication,
    ) {
        $this->authorize("update", $medication);

        $validated = $request->validated();

        $medication->update($validated);

        return redirect()
            ->route("medications.index")
            ->with("success", "Medication updated successfully.");
    }

    public function destroy(Medication $medication)
    {
        $this->authorize("delete", $medication);
        $medication->delete();

        return redirect()
            ->route("medications.index")
            ->with("success", "Medication deleted successfully.");
    }

    // Medication Schedule Management
    public function schedule()
    {
        $medications = Auth::user()
            ->medications()
            ->where("active", true)
            ->with([
                "schedules" => function ($query) {
                    $query->where("active", true)->orderBy("scheduled_time");
                },
            ])
            ->get();

        $todaySchedule = [];

        foreach ($medications as $medication) {
            // If medication is marked as_needed, add it for each dose taken today plus one for next dose
            if ($medication->as_needed) {
                $logs = MedicationLog::where("medication_id", $medication->id)
                    ->whereDate("taken_at", today())
                    ->orderBy("taken_at")
                    ->get();

                // Add entry for each dose taken today
                foreach ($logs as $log) {
                    $todaySchedule[] = [
                        "medication" => $medication,
                        "schedule" => null,
                        "as_needed" => true,
                        "taken" => true,
                        "taken_late" => false, // As-needed meds can't be late
                        "is_due" => false, // As-needed meds can't be due
                        "is_overdue" => false, // As-needed meds can't be overdue
                        "log" => $log,
                    ];
                }

                // Always add one entry for the next potential dose
                $todaySchedule[] = [
                    "medication" => $medication,
                    "schedule" => null,
                    "as_needed" => true,
                    "taken" => false,
                    "taken_late" => false, // As-needed meds can't be late
                    "is_due" => false, // As-needed meds can't be due
                    "is_overdue" => false, // As-needed meds can't be overdue
                    "log" => null,
                ];
            }

            // Add scheduled medications
            foreach ($medication->schedules as $schedule) {
                if ($schedule->isScheduledForToday()) {
                    $log = MedicationLog::where(
                        "medication_id",
                        $medication->id,
                    )
                        ->where("medication_schedule_id", $schedule->id)
                        ->whereDate("taken_at", today())
                        ->first();

                    $todaySchedule[] = [
                        "medication" => $medication,
                        "schedule" => $schedule,
                        "as_needed" => false,
                        "taken" => $log ? true : false,
                        "taken_late" => $log ? $log->isTakenLate() : false,
                        "is_due" => !$log ? $schedule->isDue() : false,
                        "is_overdue" => !$log ? $schedule->isOverdue() : false,
                        "log" => $log,
                    ];
                }
            }
        }

        // Sort: as_needed items at the end, scheduled items by time
        usort($todaySchedule, function ($a, $b) {
            if ($a["as_needed"] && !$b["as_needed"]) {
                return 1;
            }
            if (!$a["as_needed"] && $b["as_needed"]) {
                return -1;
            }
            if ($a["as_needed"] && $b["as_needed"]) {
                return 0;
            }
            return $a["schedule"]->scheduled_time <=>
                $b["schedule"]->scheduled_time;
        });

        // Group by time period
        $groupedSchedule = [
            "morning" => [],
            "afternoon" => [],
            "evening" => [],
            "bedtime" => [],
            "as_needed" => [],
        ];

        $user = Auth::user();

        foreach ($todaySchedule as $item) {
            if ($item["as_needed"]) {
                $groupedSchedule["as_needed"][] = $item;
            } else {
                $time = $item["schedule"]->scheduled_time->format("H:i");
                $period = $user->getTimePeriod($time);
                $groupedSchedule[$period][] = $item;
            }
        }

        return view(
            "medications.schedule",
            compact("medications", "todaySchedule", "groupedSchedule", "user"),
        );
    }

    public function scheduleHistory(Request $request)
    {
        $user = Auth::user();

        // Get the end date (defaults to upcoming/current Sunday for calendar week view)
        if ($request->query("date")) {
            $endDate = \Carbon\Carbon::parse($request->query("date"));
        } else {
            // Default to this Sunday (or today if today is Sunday)
            $endDate = now();
            if (!$endDate->isSunday()) {
                $endDate = $endDate->next(\Carbon\Carbon::SUNDAY);
            }
        }

        // Calculate start date (7 days before end date, giving us Monday-Sunday)
        $startDate = $endDate->copy()->subDays(6);

        $medications = $user
            ->medications()
            ->where("active", true)
            ->with([
                "schedules" => function ($query) {
                    $query->where("active", true)->orderBy("scheduled_time");
                },
            ])
            ->get();

        // Build schedule for each day in the week
        $weekSchedule = [];

        for (
            $date = $startDate->copy();
            $date->lte($endDate);
            $date->addDay()
        ) {
            $daySchedule = [];

            foreach ($medications as $medication) {
                // If medication is marked as_needed, add it for each dose taken that day
                if ($medication->as_needed) {
                    $logs = MedicationLog::where(
                        "medication_id",
                        $medication->id,
                    )
                        ->whereDate("taken_at", $date)
                        ->orderBy("taken_at")
                        ->get();

                    // Add entry for each dose taken that day
                    foreach ($logs as $log) {
                        $daySchedule[] = [
                            "medication" => $medication,
                            "schedule" => null,
                            "as_needed" => true,
                            "taken" => true,
                            "taken_late" => false,
                            "is_due" => false,
                            "is_overdue" => false,
                            "log" => $log,
                        ];
                    }

                    // If no logs for that day, show one empty entry
                    if ($logs->isEmpty()) {
                        $daySchedule[] = [
                            "medication" => $medication,
                            "schedule" => null,
                            "as_needed" => true,
                            "taken" => false,
                            "taken_late" => false,
                            "is_due" => false,
                            "is_overdue" => false,
                            "log" => null,
                        ];
                    }
                }

                // Add scheduled medications
                foreach ($medication->schedules as $schedule) {
                    if ($schedule->isScheduledForToday()) {
                        $log = MedicationLog::where(
                            "medication_id",
                            $medication->id,
                        )
                            ->where("medication_schedule_id", $schedule->id)
                            ->whereDate("taken_at", $date)
                            ->first();

                        $daySchedule[] = [
                            "medication" => $medication,
                            "schedule" => $schedule,
                            "as_needed" => false,
                            "taken" => $log ? true : false,
                            "taken_late" => $log ? $log->isTakenLate() : false,
                            "is_due" => !$log ? $schedule->isDue($date) : false,
                            "is_overdue" => !$log
                                ? $schedule->isOverdue($date)
                                : false,
                            "log" => $log,
                        ];
                    }
                }
            }

            // Sort: as_needed items at the end, scheduled items by time
            usort($daySchedule, function ($a, $b) {
                if ($a["as_needed"] && !$b["as_needed"]) {
                    return 1;
                }
                if (!$a["as_needed"] && $b["as_needed"]) {
                    return -1;
                }
                if ($a["as_needed"] && $b["as_needed"]) {
                    return 0;
                }
                return $a["schedule"]->scheduled_time <=>
                    $b["schedule"]->scheduled_time;
            });

            // Group by time period
            $groupedSchedule = [
                "morning" => [],
                "afternoon" => [],
                "evening" => [],
                "bedtime" => [],
                "as_needed" => [],
            ];

            foreach ($daySchedule as $item) {
                if ($item["as_needed"]) {
                    $groupedSchedule["as_needed"][] = $item;
                } else {
                    $time = $item["schedule"]->scheduled_time->format("H:i");
                    $period = $user->getTimePeriod($time);
                    $groupedSchedule[$period][] = $item;
                }
            }

            $weekSchedule[$date->format("Y-m-d")] = [
                "date" => $date->copy(),
                "daySchedule" => $daySchedule,
                "groupedSchedule" => $groupedSchedule,
            ];
        }

        return view(
            "medications.schedule-history",
            compact(
                "medications",
                "weekSchedule",
                "user",
                "startDate",
                "endDate",
            ),
        );
    }

    public function logTaken(MedicationLogTakenRequest $request)
    {
        $validated = $request->validated();

        $medication = Medication::findOrFail($validated["medication_id"]);
        $this->authorize("view", $medication);

        // Set intended_time based on schedule if not provided
        if (
            !isset($validated["intended_time"]) &&
            isset($validated["medication_schedule_id"])
        ) {
            $schedule = MedicationSchedule::findOrFail(
                $validated["medication_schedule_id"],
            );
            $validated["intended_time"] = now()->setTimeFrom(
                $schedule->scheduled_time,
            );
        }

        $medicationLog = MedicationLog::create($validated);

        // Send notifications if enabled
        $user = Auth::user();

        // Send notification to the user themselves
        if ($user->notify_medication_taken) {
            $user->notify(new MedicationNotifcation($medicationLog, $user));
        }

        // Send notifications to trusted contacts
        if ($user->notify_trusted_contacts_medication) {
            $trustedUsers = $user->trustedUsers;
            foreach ($trustedUsers as $trustedUser) {
                $trustedUser->notify(
                    new MedicationNotifcation($medicationLog, $user),
                );
            }
        }

        return back()->with("success", "Medication logged successfully.");
    }

    public function logSkipped(MedicationLogSkippedRequest $request)
    {
        $validated = $request->validated();

        $medication = Medication::findOrFail($validated["medication_id"]);
        $this->authorize("view", $medication);

        $user = Auth::user();

        // Use intended_time from request, or set based on schedule if available
        $intendedTime = $validated["intended_time"] ?? null;
        if (!$intendedTime && isset($validated["medication_schedule_id"])) {
            $schedule = MedicationSchedule::findOrFail(
                $validated["medication_schedule_id"],
            );
            $intendedTime = now()->setTimeFrom($schedule->scheduled_time);
        }

        $medicationLog = MedicationLog::create([
            "medication_id" => $validated["medication_id"],
            "medication_schedule_id" =>
                $validated["medication_schedule_id"] ?? null,
            "taken_at" => now(),
            "intended_time" => $intendedTime,
            "skipped" => true,
            "skip_reason" => $validated["skip_reason"] ?? null,
            "notes" => $validated["notes"] ?? null,
        ]);

        // Send notifications for skipped medication
        // Send notification to the user themselves
        if ($user->notify_medication_taken) {
            $user->notify(new MedicationNotifcation($medicationLog, $user));
        }

        // Send notifications to trusted contacts
        if ($user->notify_trusted_contacts_medication) {
            $trustedUsers = $user->trustedUsers;
            foreach ($trustedUsers as $trustedUser) {
                $trustedUser->notify(
                    new MedicationNotifcation($medicationLog, $user),
                );
            }
        }

        return back()->with("success", "Skipped dose logged.");
    }

    public function updateLog(
        MedicationLogUpdateRequest $request,
        MedicationLog $medicationLog,
    ) {
        $this->authorize("update", $medicationLog);

        $validated = $request->validated();

        $medicationLog->update($validated);

        return back()->with(
            "success",
            "Medication history updated successfully.",
        );
    }

    public function destroyLog(MedicationLog $medicationLog)
    {
        $this->authorize("delete", $medicationLog);

        $medicationLog->delete();

        return back()->with("success", "Medication history entry deleted.");
    }

    public function logBulkTaken(MedicationLogBulkTakenRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();

        // Get all medications scheduled for the specified period
        $medications = $user->medications()->with("schedules")->get();
        $loggedCount = 0;
        $loggedMedications = [];

        foreach ($medications as $medication) {
            foreach ($medication->schedules as $schedule) {
                if ($schedule->isScheduledForToday()) {
                    $time = $schedule->scheduled_time->format("H:i");
                    $schedulePeriod = $user->getTimePeriod($time);

                    if ($schedulePeriod === $validated["period"]) {
                        // Check if already logged for today
                        $existingLog = MedicationLog::where(
                            "medication_id",
                            $medication->id,
                        )
                            ->where("medication_schedule_id", $schedule->id)
                            ->whereDate("taken_at", today())
                            ->first();

                        if (!$existingLog) {
                            // Set intended time to the schedule's time on the taken date
                            $intendedTime = \Carbon\Carbon::parse(
                                $validated["taken_at"],
                            )->setTimeFrom($schedule->scheduled_time);

                            $dosageTaken =
                                $schedule->getCalculatedDosageWithUnit() ??
                                $medication->dosage . " " . $medication->unit;

                            $medicationLog = MedicationLog::create([
                                "medication_id" => $medication->id,
                                "medication_schedule_id" => $schedule->id,
                                "taken_at" => $validated["taken_at"],
                                "intended_time" => $intendedTime,
                                "dosage_taken" => $dosageTaken,
                                "notes" => $validated["notes"],
                                "skipped" => false,
                            ]);

                            // Collect medication info for bulk notification including timing
                            $timingInfo = $medicationLog->getTimeDifference();
                            $loggedMedications[] = [
                                "name" => $medication->name,
                                "dosage" => $dosageTaken,
                                "timing_info" => $timingInfo,
                                "is_late" => $medicationLog->isTakenLate(),
                                "taken_time" => $medicationLog->taken_at->format(
                                    "g:i A",
                                ),
                                "intended_time" => $intendedTime->format(
                                    "g:i A",
                                ),
                            ];

                            $loggedCount++;
                        }
                    }
                }
            }
        }

        // Send single bulk notification if any medications were logged
        if ($loggedCount > 0) {
            $takenAt = \Carbon\Carbon::parse($validated["taken_at"]);

            // Send notification to the user themselves
            if ($user->notify_medication_taken) {
                $user->notify(
                    new MedicationNotifcation(
                        $loggedMedications,
                        $user,
                        "bulk",
                        $validated["period"],
                        $validated["notes"] ?? null,
                        $loggedCount,
                    ),
                );
            }

            // Send notifications to trusted contacts
            if ($user->notify_trusted_contacts_medication) {
                $trustedUsers = $user->trustedUsers;
                foreach ($trustedUsers as $trustedUser) {
                    $trustedUser->notify(
                        new MedicationNotifcation(
                            $loggedMedications,
                            $user,
                            "bulk",
                            $validated["period"],
                            $validated["notes"] ?? null,
                            $loggedCount,
                        ),
                    );
                }
            }
        }

        $periodLabel = ucfirst($validated["period"]);
        return back()->with(
            "success",
            "Marked {$loggedCount} {$periodLabel} medications as taken.",
        );
    }

    // Schedule CRUD
    public function storeSchedule(
        MedicationScheduleStoreRequest $request,
        Medication $medication,
    ) {
        $this->authorize("update", $medication);

        $validated = $request->validated();
        $medication->schedules()->create($validated);

        return back()->with("success", "Schedule added successfully.");
    }

    public function destroySchedule(
        Medication $medication,
        MedicationSchedule $schedule,
    ) {
        $this->authorize("update", $medication);
        $schedule->delete();

        return back()->with("success", "Schedule deleted successfully.");
    }
}
