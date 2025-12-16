<?php

namespace App\Http\Controllers;

use App\Models\Observation;
use App\Http\Requests\ObservationStoreRequest;
use App\Http\Requests\ObservationUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ObservationController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $observations = Auth::user()->observations()->latest('observed_at')->paginate(20);
        return view('observations.index', compact('observations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('observations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ObservationStoreRequest $request)
    {
        $validated = $request->validated();

        $observation = Auth::user()->observations()->create($validated);

        return redirect()
            ->route('observations.index')
            ->with('success', 'Observation recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Observation $observation)
    {
        $this->authorize('view', $observation);

        return view('observations.show', compact('observation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Observation $observation)
    {
        $this->authorize('update', $observation);
        return view('observations.edit', compact('observation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ObservationUpdateRequest $request, Observation $observation)
    {
        $this->authorize('update', $observation);

        $validated = $request->validated();
        unset($validated['user_id']); // Don't update user_id

        $observation->update($validated);

        return redirect()
            ->route('observations.index')
            ->with('success', 'Observation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Observation $observation)
    {
        $this->authorize('delete', $observation);

        $observation->delete();

        return redirect()
            ->route('observations.index')
            ->with('success', 'Observation deleted successfully.');
    }
}
