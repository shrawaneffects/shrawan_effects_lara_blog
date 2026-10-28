@extends('layouts.app')

@section('title', 'Scraping Job #' . $job->id . ' - Useful Tools | ' . config('app.name', 'Shrawan Effects'))

@section('content')
<div class="container py-5 my-3">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-lg p-4 p-md-5 rounded-4 bg-body text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle mx-auto mb-3" style="width: 68px; height: 68px;">
                    <i class="bi bi-cpu fs-2 text-gradient"></i>
                </div>

                <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                    <span class="telemetry-tag"><i class="bi bi-gear-wide-connected"></i> TASK #{{ $job->id }}</span>
                    <span class="badge bg-secondary-subtle text-body font-mono small">{{ strtoupper($job->status) }}</span>
                </div>

                <h3 class="fw-bold font-cyber mb-1">
                    Extracting <span class="text-gradient">{{ $job->query_keyword }}</span>
                </h3>
                <p class="text-body-secondary small font-mono mb-4">
                    Location: {{ $job->city ?: 'All' }}{{ $job->country ? ', ' . $job->country : '' }} • Provider: {{ ucfirst(str_replace('_', ' ', $job->provider)) }}
                </p>

                <!-- Progress Bar -->
                <div class="progress mb-3 rounded-pill" style="height: 14px;">
                    <div id="jobProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: {{ $job->progress_percent }}%;" aria-valuenow="{{ $job->progress_percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <div class="d-flex justify-content-between font-mono small text-body-secondary mb-4">
                    <span id="jobProgressText">{{ $job->progress_percent }}% Processed</span>
                    <span id="jobCountText">{{ $job->total_collected }} collected</span>
                </div>

                @if($job->error_message)
                    <div class="alert alert-danger font-mono small rounded-3 text-start mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $job->error_message }}
                    </div>
                @endif

                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('useful-tools.business-scraper.index') }}" class="btn btn-gradient rounded-pill px-4">
                        <i class="bi bi-arrow-left me-1"></i> Return to Scraper
                    </a>
                    @if($job->isRunning())
                        <form action="{{ route('useful-tools.business-scraper.job-cancel', $job->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger rounded-pill px-4" onclick="return confirm('Cancel this scraping task?');">
                                <i class="bi bi-stop-circle me-1"></i> Cancel Task
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($job->isRunning())
<script>
    (function pollStatus() {
        fetch("{{ route('useful-tools.business-scraper.job-status', $job->id) }}", {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            const bar = document.getElementById('jobProgressBar');
            const pText = document.getElementById('jobProgressText');
            const cText = document.getElementById('jobCountText');

            if (bar) bar.style.width = data.progress_percent + '%';
            if (pText) pText.innerText = data.progress_percent + '% Processed';
            if (cText) cText.innerText = data.total_collected + ' collected';

            if (data.status === 'completed' || data.status === 'failed' || data.status === 'cancelled') {
                window.location.reload();
            } else {
                setTimeout(pollStatus, 2500);
            }
        })
        .catch(err => setTimeout(pollStatus, 5000));
    })();
</script>
@endif
@endsection
