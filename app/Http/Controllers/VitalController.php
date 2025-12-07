<?php

namespace App\Http\Controllers;

use App\Models\Vital;
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

        Vital::create($validated);

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

        $vital->update($validated);

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
}
