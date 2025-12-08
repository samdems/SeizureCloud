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
use App\Http\Requests\MedicationScheduleStoreRequest;

class MedicationController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $medications = Auth::user()
            ->medications()
            ->with("schedules")
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
            // If medication is marked as_needed, add it without a specific schedule
            if ($medication->as_needed) {
                $log = MedicationLog::where("medication_id", $medication->id)
                    ->whereDate("taken_at", today())
                    ->first();

                $todaySchedule[] = [
                    "medication" => $medication,
                    "schedule" => null,
                    "as_needed" => true,
                    "taken" => $log ? true : false,
                    "taken_late" => false, // As-needed meds can't be late
                    "is_due" => false, // As-needed meds can't be due
                    "is_overdue" => false, // As-needed meds can't be overdue
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
        $date = $request->query("date")
            ? \Carbon\Carbon::parse($request->query("date"))
            : now()->subDay();

        $medications = $user
            ->medications()
            ->where("active", true)
            ->with([
                "schedules" => function ($query) {
                    $query->where("active", true)->orderBy("scheduled_time");
                },
            ])
            ->get();

        $daySchedule = [];

        foreach ($medications as $medication) {
            // If medication is marked as_needed, add it without a specific schedule
            if ($medication->as_needed) {
                $log = MedicationLog::where("medication_id", $medication->id)
                    ->whereDate("taken_at", $date)
                    ->first();

                $daySchedule[] = [
                    "medication" => $medication,
                    "schedule" => null,
                    "as_needed" => true,
                    "taken" => $log ? true : false,
                    "taken_late" => false, // As-needed meds can't be late
                    "is_due" => false, // As-needed meds can't be due
                    "is_overdue" => false, // As-needed meds can't be overdue
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

        return view(
            "medications.schedule-history",
            compact(
                "medications",
                "daySchedule",
                "groupedSchedule",
                "user",
                "date",
            ),
        );
    }

    public function logTaken(MedicationLogTakenRequest $request)
    {
        $validated = $request->validated();

        $medication = Medication::findOrFail($validated["medication_id"]);
        $this->authorize("view", $medication);

        MedicationLog::create($validated);

        return back()->with("success", "Medication logged successfully.");
    }

    public function logSkipped(MedicationLogSkippedRequest $request)
    {
        $validated = $request->validated();

        $medication = Medication::findOrFail($validated["medication_id"]);
        $this->authorize("view", $medication);

        MedicationLog::create([
            "medication_id" => $validated["medication_id"],
            "medication_schedule_id" =>
                $validated["medication_schedule_id"] ?? null,
            "taken_at" => now(),
            "skipped" => true,
            "skip_reason" => $validated["skip_reason"] ?? null,
            "notes" => $validated["notes"] ?? null,
        ]);

        return back()->with("success", "Skipped dose logged.");
    }

    public function logBulkTaken(MedicationLogBulkTakenRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();

        // Get all medications scheduled for the specified period
        $medications = $user->medications()->with("schedules")->get();
        $loggedCount = 0;

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
                            MedicationLog::create([
                                "medication_id" => $medication->id,
                                "medication_schedule_id" => $schedule->id,
                                "taken_at" => $validated["taken_at"],
                                "dosage_taken" =>
                                    $schedule->getCalculatedDosageWithUnit() ??
                                    $medication->dosage .
                                        " " .
                                        $medication->unit,
                                "notes" => $validated["notes"],
                                "skipped" => false,
                            ]);
                            $loggedCount++;
                        }
                    }
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
