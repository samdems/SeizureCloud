<?php

namespace App\Http\Controllers;

use App\Models\Vital;
use App\Models\VitalTypeThreshold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\VitalStoreRequest;
use App\Http\Requests\VitalUpdateRequest;

class VitalController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $vitals = Auth::user()->vitals()->latest()->paginate(20);
        return view("vitals.index", compact("vitals"));
    }

    public function create()
    {
        return view("vitals.create");
    }

    public function store(VitalStoreRequest $request)
    {
        $validated = $request->validated();
        $validated["user_id"] = Auth::id();

        // Handle blood pressure parsing
        if ($validated["type"] === "Blood Pressure") {
            $parsed = Vital::parseBloodPressure($validated["value"]);
            $validated["systolic_value"] = $parsed["systolic"];
            $validated["diastolic_value"] = $parsed["diastolic"];
            $validated["value"] = $parsed["combined_value"];
        }

        $vital = Vital::create($validated);

        // Calculate and update status based on thresholds
        $vital->updateStatus();
        $vital->save();

        return redirect()
            ->route("vitals.index")
            ->with("success", "Vital record created successfully.");
    }

    public function show(Vital $vital)
    {
        $this->authorize("view", $vital);

        return view("vitals.show", compact("vital"));
    }

    public function edit(Vital $vital)
    {
        $this->authorize("update", $vital);
        return view("vitals.edit", compact("vital"));
    }

    public function update(VitalUpdateRequest $request, Vital $vital)
    {
        $this->authorize("update", $vital);

        $validated = $request->validated();

        // Handle blood pressure parsing
        if ($validated["type"] === "Blood Pressure") {
            $parsed = Vital::parseBloodPressure($validated["value"]);
            $validated["systolic_value"] = $parsed["systolic"];
            $validated["diastolic_value"] = $parsed["diastolic"];
            $validated["value"] = $parsed["combined_value"];
        }

        $vital->update($validated);

        // Recalculate and update status based on thresholds
        $vital->updateStatus();
        $vital->save();

        return redirect()
            ->route("vitals.index")
            ->with("success", "Vital record updated successfully.");
    }

    public function destroy(Vital $vital)
    {
        $this->authorize("delete", $vital);
        $vital->delete();

        return redirect()
            ->route("vitals.index")
            ->with("success", "Vital record deleted successfully.");
    }

    public function thresholds()
    {
        $user = Auth::user();
        $thresholds = $user
            ->vitalTypeThresholds()
            ->where("is_active", true)
            ->get()
            ->keyBy("vital_type");

        $vitalTypes = config("app.vital_types");

        return view("vitals.thresholds", compact("thresholds", "vitalTypes"));
    }

    public function updateThresholds(Request $request)
    {
        $validated = $request->validate([
            "thresholds" => "required|array",
            "thresholds.*.vital_type" => "required|string",
            "thresholds.*.low_threshold" => "nullable|numeric",
            "thresholds.*.high_threshold" => "nullable|numeric",
            "thresholds.*.systolic_low_threshold" => "nullable|numeric",
            "thresholds.*.systolic_high_threshold" => "nullable|numeric",
            "thresholds.*.diastolic_low_threshold" => "nullable|numeric",
            "thresholds.*.diastolic_high_threshold" => "nullable|numeric",
            "thresholds.*.is_active" => "boolean",
        ]);

        $userId = Auth::id();

        foreach ($validated["thresholds"] as $thresholdData) {
            $updateData = [
                "low_threshold" => $thresholdData["low_threshold"] ?? null,
                "high_threshold" => $thresholdData["high_threshold"] ?? null,
                "is_active" => $thresholdData["is_active"] ?? true,
            ];

            // Add blood pressure specific fields if present
            if ($thresholdData["vital_type"] === "Blood Pressure") {
                $updateData["systolic_low_threshold"] =
                    $thresholdData["systolic_low_threshold"] ?? null;
                $updateData["systolic_high_threshold"] =
                    $thresholdData["systolic_high_threshold"] ?? null;
                $updateData["diastolic_low_threshold"] =
                    $thresholdData["diastolic_low_threshold"] ?? null;
                $updateData["diastolic_high_threshold"] =
                    $thresholdData["diastolic_high_threshold"] ?? null;
            }

            VitalTypeThreshold::updateOrCreate(
                [
                    "user_id" => $userId,
                    "vital_type" => $thresholdData["vital_type"],
                ],
                $updateData,
            );
        }

        return redirect()
            ->route("vitals.thresholds")
            ->with("success", "Vital thresholds updated successfully.");
    }
}
