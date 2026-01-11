        @if($emergencyStatus['is_emergency'])
            <div class="card bg-base-100 shadow-xl border-2 border-error">
                <div class="card-body p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-error">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        <h2 class="text-lg font-semibold text-error">⚠️ MEDICAL EMERGENCY DETECTED</h2>
                    </div>

                    <div class="space-y-2 mb-4">
                        @if($emergencyStatus['status_epilepticus'])
                            <div class="alert alert-error">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <h4 class="font-semibold">Possible Status Epilepticus</h4>
                                    <p class="text-sm">Seizure duration ({{ $seizure->formatted_duration }}) exceeds emergency threshold ({{ $emergencyStatus['duration_threshold'] }} min)</p>
                                </div>
                            </div>
                        @endif

                        @if($emergencyStatus['cluster_emergency'])
                            <div class="alert alert-error">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.601a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                                </svg>
                                <div>
                                    <h4 class="font-semibold">Seizure Cluster Emergency</h4>
                                    <p class="text-sm">{{ $emergencyStatus['cluster_count'] }} seizures detected within {{ $emergencyStatus['timeframe_hours'] }} hours, exceeding emergency threshold ({{ $emergencyStatus['count_threshold'] }} seizures)</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="bg-error/10 border border-error/20 rounded-lg p-3 text-sm">
                        <p class="font-semibold text-error mb-2">⚠️ Emergency Event Recorded</p>
                        <p class="text-error/80 mb-2">This seizure met emergency criteria. Review with your healthcare provider.</p>

                        @if(auth()->user()->emergency_contact_info)
                            <div class="mt-3 pt-2 border-t border-error/20">
                                <p class="font-semibold text-error">Emergency Contact Information:</p>
                                <div class="text-error/80">{!! auth()->user()->getFormattedEmergencyContact() !!}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
