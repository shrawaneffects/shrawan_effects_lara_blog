@extends('layouts.admin')

@section('title', 'Security Audit Logs Explorer')
@section('page_title', 'Security Audit Logs')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">Security Audit Logs</h4>
            <p class="text-body-secondary small mb-0">Review user authentications, administrative changes, backup operations, and rate-limiting alerts.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.security.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Security Center
            </a>
            <button type="button" class="btn btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#clearLogsModal">
                <i class="bi bi-trash me-1"></i> Purge Old Logs
            </button>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="card border-0 shadow-sm p-3 mb-4 rounded-3 bg-body">
        <form action="{{ route('admin.security.audit-logs') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-body-tertiary"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search by IP, user, or description..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-3 col-6">
                <select name="event" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Event Types</option>
                    @foreach($eventTypes as $evt)
                        <option value="{{ $evt }}" {{ request('event') === $evt ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $evt)) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 col-6">
                <select name="severity" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Severities</option>
                    <option value="info" {{ request('severity') === 'info' ? 'selected' : '' }}>Info</option>
                    <option value="warning" {{ request('severity') === 'warning' ? 'selected' : '' }}>Warning</option>
                    <option value="danger" {{ request('severity') === 'danger' ? 'selected' : '' }}>Danger</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Filter</button>
                @if(request()->hasAny(['search', 'event', 'severity']))
                    <a href="{{ route('admin.security.audit-logs') }}" class="btn btn-outline-secondary btn-sm" title="Clear Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
                @endif
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-body overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 110px;">Severity</th>
                        <th style="width: 170px;">Event</th>
                        <th>Description & Details</th>
                        <th style="width: 150px;">User</th>
                        <th style="width: 140px;">IP Address</th>
                        <th style="width: 160px;">Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>
                                @if($log->severity === 'danger')
                                    <span class="badge bg-danger text-white rounded-pill px-2 py-1">
                                        <i class="bi bi-exclamation-octagon-fill me-1"></i> Danger
                                    </span>
                                @elseif($log->severity === 'warning')
                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-1">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Warning
                                    </span>
                                @else
                                    <span class="badge bg-info-subtle text-info rounded-pill px-2 py-1">
                                        <i class="bi bi-info-circle-fill me-1"></i> Info
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="font-monospace small fw-bold text-body">
                                    {{ $log->event }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold text-body small">{{ $log->description }}</div>
                                @if(!empty($log->details))
                                    <div class="text-muted small font-monospace mt-1" style="font-size: 0.75rem;">
                                        {{ json_encode($log->details) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($log->user)
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $log->user->avatar_url }}" alt="{{ $log->user->name }}" class="rounded-circle" width="24" height="24">
                                        <span class="small fw-semibold">{{ $log->user->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted small">Guest / System</span>
                                @endif
                            </td>
                            <td class="small font-monospace text-muted">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                            <td class="small text-muted">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-shield-check fs-1 d-block mb-2 text-success"></i>
                                No audit log records matching the search criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="card-footer bg-transparent py-3 d-flex justify-content-center">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    <!-- Modal: Purge Old Logs -->
    <div class="modal fade" id="clearLogsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger"><i class="bi bi-trash3 me-2"></i> Purge Historical Audit Logs</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.security.audit-logs.clear') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="small text-muted mb-3">Select the retention period for purging old audit records from the database:</p>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Purge logs older than:</label>
                            <select name="days" class="form-select rounded-pill">
                                <option value="30">30 Days</option>
                                <option value="60">60 Days</option>
                                <option value="90">90 Days</option>
                                <option value="0">All Historical Logs</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Confirm Purge</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
