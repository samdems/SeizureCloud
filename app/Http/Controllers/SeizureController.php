<?php

namespace App\Http\Controllers;

use App\Models\Seizure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\SeizureStoreRequest;
use App\Http\Requests\SeizureUpdateRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Notifications\SeizureAddedNotification;
use App\Services\VideoUploadService;
use App\Services\QrCodeService;

class SeizureController extends Controller
{
    use AuthorizesRequests;

    protected VideoUploadService $videoUploadService;
    protected QrCodeService $qrCodeService;

    public function __construct(
        VideoUploadService $videoUploadService,
        QrCodeService $qrCodeService,
    ) {
        $this->videoUploadService = $videoUploadService;
        $this->qrCodeService = $qrCodeService;
    }
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

        $seizure = Seizure::create($validated);

        // Handle video upload if present
        if ($request->hasFile("video_upload")) {
            $uploadSuccess = $this->videoUploadService->uploadVideo(
                $seizure,
                $request->file("video_upload"),
            );

            if (!$uploadSuccess) {
                // If video upload fails, still proceed but show warning
                session()->flash(
                    "warning",
                    "Seizure record created but video upload failed. Please try uploading the video again from the seizure details page.",
                );
            }
        }

        // Send notifications if enabled
        $user = Auth::user();

        // Send notification to the user themselves
        if ($user->notify_seizure_added) {
            $user->notify(new SeizureAddedNotification($seizure, $user));
        }

        // Send notifications to trusted contacts
        if ($user->notify_trusted_contacts_seizures) {
            $trustedUsers = $user->trustedUsers;
            foreach ($trustedUsers as $trustedUser) {
                $trustedUser->notify(
                    new SeizureAddedNotification($seizure, $user),
                );
            }
        }

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

        // Handle video upload if present
        if ($request->hasFile("video_upload")) {
            $uploadSuccess = $this->videoUploadService->uploadVideo(
                $seizure,
                $request->file("video_upload"),
            );

            if (!$uploadSuccess) {
                return redirect()
                    ->route("seizures.show", $seizure)
                    ->with(
                        "error",
                        "Seizure record updated but video upload failed. Please try uploading the video again.",
                    );
            }
        }

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

    public function exportMonthlyPdf(Request $request)
    {
        $month = $request->get("month", now()->month);
        $year = $request->get("year", now()->year);

        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->endOfDay();

        $seizures = Auth::user()
            ->seizures()
            ->whereBetween("start_time", [$startDate, $endDate])
            ->orderBy("start_time", "asc")
            ->get();

        $user = Auth::user();
        $monthName = $startDate->format("F Y");

        // Calculate statistics
        $totalSeizures = $seizures->count();
        $averageSeverity = $seizures->avg("severity");
        $totalDuration = $seizures->sum("duration_minutes");
        $longestSeizure = $seizures->max("duration_minutes");

        $pdf = Pdf::loadView(
            "seizures.pdf.monthly",
            compact(
                "seizures",
                "user",
                "monthName",
                "startDate",
                "endDate",
                "totalSeizures",
                "averageSeverity",
                "totalDuration",
                "longestSeizure",
            ),
        );

        $filename = "seizures_{$user->name}_{$year}-{$month}.pdf";

        return $pdf->download($filename);
    }

    public function exportMonthlyComprehensivePdf(Request $request)
    {
        $month = $request->get("month", now()->month);
        $year = $request->get("year", now()->year);

        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->endOfDay();

        $seizures = Auth::user()
            ->seizures()
            ->whereBetween("start_time", [$startDate, $endDate])
            ->orderBy("start_time", "asc")
            ->get();

        $user = Auth::user();
        $monthName = $startDate->format("F Y");

        // Calculate statistics
        $totalSeizures = $seizures->count();
        $averageSeverity = $seizures->avg("severity");
        $totalDuration = $seizures->sum("duration_minutes");
        $longestSeizure = $seizures->max("duration_minutes");

        // Get detailed data for each seizure
        $seizuresDetailed = $seizures->map(function ($seizure) use ($user) {
            // Get medications active at time of seizure
            $medications = $user
                ->medications()
                ->with("schedules")
                ->get()
                ->map(function ($medication) use ($seizure) {
                    $wasActive =
                        $medication->active ||
                        ($medication->start_date &&
                            $medication->start_date <= $seizure->start_time &&
                            (!$medication->end_date ||
                                $medication->end_date >= $seizure->start_time));

                    return $wasActive ? $medication : null;
                })
                ->filter();

            // Get vitals from seizure day
            $seizureDate = $seizure->start_time->startOfDay();
            $vitals = $user
                ->vitals()
                ->whereDate("recorded_at", $seizureDate)
                ->orderBy("recorded_at", "asc")
                ->get()
                ->groupBy("type");

            return [
                "seizure" => $seizure,
                "medications" => $medications,
                "vitals" => $vitals,
            ];
        });

        $pdf = Pdf::loadView(
            "seizures.pdf.comprehensive",
            compact(
                "seizures",
                "seizuresDetailed",
                "user",
                "monthName",
                "startDate",
                "endDate",
                "totalSeizures",
                "averageSeverity",
                "totalDuration",
                "longestSeizure",
            ),
        );

        $filename = "seizures_comprehensive_{$user->name}_{$year}-{$month}.pdf";

        return $pdf->download($filename);
    }

    public function exportSinglePdf(Seizure $seizure)
    {
        $this->authorize("view", $seizure);

        $user = Auth::user();

        // Get medications active at time of seizure with adherence data
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

        // Get vitals from seizure day
        $seizureDate = $seizure->start_time->startOfDay();
        $vitals = Auth::user()
            ->vitals()
            ->whereDate("recorded_at", $seizureDate)
            ->orderBy("recorded_at", "asc")
            ->get()
            ->groupBy("type");

        // Get emergency status for this seizure
        $emergencyStatus = Auth::user()->getEmergencyStatus($seizure);

        $pdf = Pdf::loadView(
            "seizures.pdf.single",
            compact(
                "seizure",
                "user",
                "medications",
                "vitals",
                "emergencyStatus",
            ),
        );

        $filename = "seizure_{$seizure->id}_{$seizure->start_time->format(
            "Y-m-d_H-i",
        )}.pdf";

        return $pdf->download($filename);
    }

    public function deleteVideo(Seizure $seizure)
    {
        $this->authorize("update", $seizure);

        $success = $this->videoUploadService->removeVideo($seizure);

        if (!$success) {
            return back()->with(
                "error",
                "Failed to delete video. Please try again.",
            );
        }

        return back()->with("success", "Video deleted successfully.");
    }

    public function regenerateVideoToken(Seizure $seizure)
    {
        $this->authorize("update", $seizure);

        if (!$seizure->video_file_path) {
            return back()->with(
                "error",
                "No video file found for this seizure.",
            );
        }

        $success = $this->videoUploadService->regenerateToken($seizure);

        if (!$success) {
            return back()->with(
                "error",
                "Failed to regenerate video access token.",
            );
        }

        return back()->with(
            "success",
            "New permanent video access token generated successfully.",
        );
    }
}
