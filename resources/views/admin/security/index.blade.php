@extends('layouts.admin')

@section('title', 'Cyber Security Center & System Health')
@section('page_title', 'Cyber Security Center')

@section('content')
    <!-- Top Header & Quick Links -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">Cyber Security & System Health</h4>
            <p class="text-body-secondary small mb-0">Live monitoring of security posture, attack prevention metrics, and audit history.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.security.audit-logs') }}" class="btn btn-outline-primary rounded-pill px-3">
                <i class="bi bi-clock-history me-1"></i> View Audit Logs
            </a>
            <a href="{{ route('admin.security.backups') }}" class="btn btn-primary rounded-pill px-3 shadow-sm">
                <i class="bi bi-database-check me-1"></i> Disaster Recovery Backups
            </a>
        </div>
    </div>

    <!-- Security Scorecard Banner -->
    <div class="card border-0 shadow-sm rounded-4 bg-body mb-4 p-4">
        <div class="row align-items-center g-4">
            <div class="col-md-3 text-center text-md-start">
                <span class="text-body-secondary small fw-semibold text-uppercase d-block mb-1">Security Health Score</span>
                <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2">
                    <h1 class="display-4 fw-bold mb-0 text-{{ $healthReport['status_color'] }}">{{ $healthReport['score'] }}</h1>
                    <span class="fs-4 text-muted">/ 100</span>
                </div>
                <span class="badge bg-{{ $healthReport['status_color'] }}-subtle text-{{ $healthReport['status_color'] }} rounded-pill px-3 py-1 mt-2 fw-semibold">
                    <i class="bi bi-shield-check me-1"></i> {{ $healthReport['status_label'] }}
                </span>
            </div>

            <div class="col-md-9">
                <div class="row g-3">
                    <div class="col-sm-4">
                        <div class="p-3 bg-body-tertiary rounded-4">
                            <span class="text-muted small d-block mb-1">Failed Logins (24h)</span>
                            <h4 class="fw-bold mb-0 {{ $healthReport['failed_logins_24h'] > 5 ? 'text-danger' : 'text-body' }}">
                                {{ $healthReport['failed_logins_24h'] }}
                            </h4>
                            <small class="text-muted">Brute force shielded</small>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 bg-body-tertiary rounded-4">
                            <span class="text-muted small d-block mb-1">Total Administrators</span>
                            <h4 class="fw-bold mb-0 text-primary">{{ $healthReport['total_admins'] }}</h4>
                            <small class="text-muted">Super-user accounts</small>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 bg-body-tertiary rounded-4">
                            <span class="text-muted small d-block mb-1">Latest Database Backup</span>
                            <h6 class="fw-bold mb-0 text-truncate text-body" title="{{ $healthReport['latest_backup_time'] }}">
                                {{ $healthReport['latest_backup_time'] }}
                            </h6>
                            <small class="text-success">{{ $healthReport['backup_count'] }} snapshots available</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Checks & Audit Logs Row -->
    <div class="row g-4">
        <!-- Left: Active Cyber Security Defenses -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 bg-body p-4 h-100">
                <h5 class="fw-bold mb-3"><i class="bi bi-shield-lock-fill text-primary me-2"></i> Security Defenses & Status</h5>
                
                <div class="d-flex flex-column gap-3">
                    @foreach($healthReport['checks'] as $check)
                        <div class="p-3 rounded-4 border d-flex align-items-start gap-3">
                            <div class="bg-{{ $check['status'] }}-subtle text-{{ $check['status'] }} p-2 rounded-circle fs-5">
                                <i class="{{ $check['icon'] }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold mb-0">{{ $check['title'] }}</h6>
                                    <span class="badge bg-{{ $check['status'] }}-subtle text-{{ $check['status'] }} rounded-pill small">
                                        {{ ucfirst($check['status']) }}
                                    </span>
                                </div>
                                <p class="text-body-secondary small mb-0">{{ $check['message'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right: Live Security Audit Log -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 bg-body p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-activity text-info me-2"></i> Live Security Events</h5>
                    <a href="{{ route('admin.security.audit-logs') }}" class="btn btn-sm btn-link text-decoration-none">View All</a>
                </div>

                @if($recentAuditLogs->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentAuditLogs as $log)
                            <div class="list-group-item px-0 py-3 bg-transparent border-bottom">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <span class="fw-semibold small d-flex align-items-center gap-1">
                                        @if($log->severity === 'danger')
                                            <span class="badge bg-danger-subtle text-danger rounded-pill">Danger</span>
                                        @elseif($log->severity === 'warning')
                                            <span class="badge bg-warning-subtle text-warning rounded-pill">Warning</span>
                                        @else
                                            <span class="badge bg-info-subtle text-info rounded-pill">Info</span>
                                        @endif
                                        {{ $log->description }}
                                    </span>
                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $log->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center small text-muted">
                                    <span>User: {{ $log->user ? $log->user->name : 'System / Guest' }}</span>
                                    <span class="font-monospace">{{ $log->ip_address ?? 'Local' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-shield-check fs-1 d-block mb-2 text-success"></i>
                        No suspicious security events logged yet.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
