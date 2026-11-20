@php
    $onboardingProgress = auth()->user()->getOnboardingProgress();
@endphp
@if(!$onboardingProgress['is_complete'])
<div class="mb-4">
    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h6 class="fw-bold mb-1">Account Setup Progress</h6>
                    <p class="text-muted mb-0" style="font-size: 0.8rem;">
                        {{ $onboardingProgress['remaining'] }} {{ $onboardingProgress['remaining'] === 1 ? 'step' : 'steps' }} remaining
                    </p>
                </div>
                <span class="fw-bold fs-5" style="color: var(--base-color, #008080);">
                    {{ $onboardingProgress['percentage'] }}%
                </span>
            </div>

            <div class="progress mb-4" style="height: 8px; border-radius: 99px;">
                <div class="progress-bar" role="progressbar"
                     style="width: {{ $onboardingProgress['percentage'] }}%; background-color: var(--base-color, #008080); border-radius: 99px; transition: width 0.7s ease;"
                     aria-valuenow="{{ $onboardingProgress['percentage'] }}"
                     aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                @foreach($onboardingProgress['steps'] as $step)
                <span class="d-inline-flex align-items-center gap-1 px-3 py-1 rounded-pill"
                      style="font-size: 0.75rem; font-weight: 600;
                             {{ $step['completed']
                                 ? 'background:#d1fae5; color:#065f46; border:1px solid #a7f3d0;'
                                 : 'background:#f3f4f6; color:#6b7280; border:1px solid #e5e7eb;' }}">
                    <i class="{{ $step['completed'] ? 'ri-checkbox-circle-fill' : 'ri-time-line' }}"
                       style="font-size: 0.95rem;"></i>
                    {{ $step['label'] }}
                </span>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif
