<?php

namespace App\Http\Controllers;

use App\Models\Seizure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\SeizureStoreRequest;
use App\Http\Requests\SeizureUpdateRequest;

class SeizureController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $seizures = Auth::user()->seizures()->latest()->paginate(20);

        // Add vitals count, seizure event grouping, and emergency status for each seizure
        $seizures->getCollection()->transform(function ($seizure) {
            $seizureDate = $seizure->start_time->startOfDay();
            $seizure->vitals_count = Auth::user()
                ->vitals()
                ->whereDate("recorded_at", $seizureDate)
                ->count();

            // Find seizures in the same event (within configured timeframe)
            $halfTimeframe = intval(
                Auth::user()->emergency_seizure_timeframe_hours / 2,
            );
            $eventStart = $seizure->start_time
                ->copy()
                ->subHours($halfTimeframe);
            $eventEnd = $seizure->start_time->copy()->addHours($halfTimeframe);

            $seizure->event_seizure_count = Auth::user()
                ->seizures()
                ->whereBetween("start_time", [$eventStart, $eventEnd])
                ->count();

            // Add emergency status
            $seizure->emergency_status = Auth::user()->getEmergencyStatus(
                $seizure,
            );

            return $seizure;
        });

        return view("seizures.index", compact("seizures"));
    }

    public function create()
    {
        return view("seizures.create");
    }

    public function store(SeizureStoreRequest $request)
    {
        $validated = $request->validated();

        Seizure::create($validated);

        return redirect()
            ->route("seizures.index")
            ->with("success", "Seizure record created successfully.");
    }

    public function show(Seizure $seizure)
    {
        $this->authorize("view", $seizure);

        // Get user's active medications with their schedules
        $medications = Auth::user()
            ->medications()
            ->with("schedules")
            ->get()
            ->map(function ($medication) use ($seizure) {
                // Check if medication was active at time of seizure
                $wasActive =
                    $medication->active ||
                    ($medication->start_date &&
                        $medication->start_date <= $seizure->start_time &&
                        (!$medication->end_date ||
                            $medication->end_date >= $seizure->start_time));

                if (!$wasActive) {
                    return null;
                }

                // Get the seizure date
                $seizureDate = $seizure->start_time->startOfDay();

                // Check adherence for scheduled medications on the seizure day
                $scheduledDoses = [];
                $takenCount = 0;
                $totalCount = 0;

                foreach ($medication->schedules as $schedule) {
                    // Only count schedules before the seizure time
                    $scheduledTime = $seizure->start_time
                        ->copy()
                        ->setTimeFromTimeString(
                            $schedule->scheduled_time->format("H:i"),
                        );

                    if ($scheduledTime <= $seizure->start_time) {
                        $totalCount++;

                        // Check if this dose was logged as taken
                        $log = $medication
                            ->logs()
                            ->where("medication_schedule_id", $schedule->id)
                            ->whereDate("taken_at", $seizureDate)
                            ->first();

                        if ($log && !$log->skipped) {
                            $takenCount++;
                        }

                        $scheduledDoses[] = [
                            "schedule" => $schedule,
                            "taken" => $log && !$log->skipped,
                            "log" => $log,
                        ];
                    }
                }

                $medication->adherence = [
                    "scheduled_doses" => $scheduledDoses,
                    "taken_count" => $takenCount,
                    "total_count" => $totalCount,
                    "all_taken" =>
                        $totalCount > 0 && $takenCount === $totalCount,
                    "was_needed" => $totalCount > 0, // Medication had doses scheduled before seizure
                ];

                return $medication;
            })
            ->filter(); // Remove null entries

        // Get vitals from the day of the seizure
        $seizureDate = $seizure->start_time->startOfDay();
        $vitals = Auth::user()
            ->vitals()
            ->whereDate("recorded_at", $seizureDate)
            ->orderBy("recorded_at", "asc")
            ->get()
            ->groupBy("type");

        // Get seizures from the same event (within configured timeframe)
        $halfTimeframe = intval(
            Auth::user()->emergency_seizure_timeframe_hours / 2,
        );
        $eventStart = $seizure->start_time->copy()->subHours($halfTimeframe);
        $eventEnd = $seizure->start_time->copy()->addHours($halfTimeframe);

        $seizureEvent = Auth::user()
            ->seizures()
            ->whereBetween("start_time", [$eventStart, $eventEnd])
            ->orderBy("start_time", "asc")
            ->get();

        // Get emergency status for this seizure
        $emergencyStatus = Auth::user()->getEmergencyStatus($seizure);

        return view(
            "seizures.show",
            compact(
                "seizure",
                "medications",
                "vitals",
                "seizureEvent",
                "emergencyStatus",
            ),
        );
    }

    public function edit(Seizure $seizure)
    {
        $this->authorize("update", $seizure);
        return view("seizures.edit", compact("seizure"));
    }

    public function update(SeizureUpdateRequest $request, Seizure $seizure)
    {
        $this->authorize("update", $seizure);

        $validated = $request->validated();

        $seizure->update($validated);

        return redirect()
            ->route("seizures.index")
            ->with("success", "Seizure record updated successfully.");
    }

    public function destroy(Seizure $seizure)
    {
        $this->authorize("delete", $seizure);
        $seizure->delete();

        return redirect()
            ->route("seizures.index")
            ->with("success", "Seizure record deleted successfully.");
    }

    public function liveTracker()
    {
        // Get all users that the current user has trusted access to
        $accessibleUsers = Auth::user()->validAccessibleAccounts()->get();

        // Add the current user to the list
        $users = collect([Auth::user()])
            ->merge($accessibleUsers)
            ->unique("id")
            ->filter(function ($user) {
                return $user->canTrackSeizures();
            });

        // Prepare user data for JavaScript
        $usersData = $users
            ->map(function ($user) {
                return [
                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email,
                    "avatar_url" => $user->avatarUrl(40),
                    "account_type" => $user->account_type,
                    "status_epilepticus_duration_minutes" =>
                        $user->status_epilepticus_duration_minutes ?? 5,
                    "emergency_contact_info" => $user->emergency_contact_info,
                ];
            })
            ->values();

        return view("seizures.live-tracker", compact("users", "usersData"));
    }
}
