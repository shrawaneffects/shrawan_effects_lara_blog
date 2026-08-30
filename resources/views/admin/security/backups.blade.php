@extends('layouts.admin')

@section('title', 'Database Backups & Disaster Recovery')
@section('page_title', 'Disaster Recovery & Backups')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">Database Disaster Recovery & Backups</h4>
            <p class="text-body-secondary small mb-0">Create instant database snapshots, download archives, and restore database tables in disaster recovery events.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.security.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Security Center
            </a>
            <form action="{{ route('admin.security.backups.create') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Create Database Backup Now
                </button>
            </form>
        </div>
    </div>

    <!-- Overview Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-body-secondary small fw-semibold">Total Backup Snapshots</span>
                        <h4 class="fw-bold mb-0 text-gradient mt-1">{{ count($backups) }}</h4>
                    </div>
                    <div class="bg-primary-subtle text-primary p-3 rounded-4">
                        <i class="bi bi-database-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-body-secondary small fw-semibold">Last Backup Time</span>
                        <h6 class="fw-bold mb-0 text-body mt-2">{{ $healthReport['latest_backup_time'] }}</h6>
                    </div>
                    <div class="bg-success-subtle text-success p-3 rounded-4">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-body-secondary small fw-semibold">Storage Location</span>
                        <span class="small font-monospace text-muted d-block mt-2">storage/app/backups</span>
                    </div>
                    <div class="bg-info-subtle text-info p-3 rounded-4">
                        <i class="bi bi-folder-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Backups Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-body overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Backup Archive Name</th>
                        <th style="width: 140px;">File Size</th>
                        <th style="width: 200px;">Generated At</th>
                        <th style="width: 220px;" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backups as $b)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-file-earmark-code fs-4 text-primary"></i>
                                    <div>
                                        <span class="fw-bold font-monospace text-body small d-block">{{ $b['filename'] }}</span>
                                        <span class="text-muted small" style="font-size: 0.75rem;">Full Database Tables & Data JSON Snapshot</span>
                                    </div>
                                </div>
                            </td>
                            <td class="small fw-semibold">{{ $b['size'] }}</td>
                            <td class="small text-muted">{{ $b['created_at'] }}</td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <!-- Download Button -->
                                    <a href="{{ route('admin.security.backups.download', $b['filename']) }}" class="btn btn-outline-primary" title="Download backup snapshot">
                                        <i class="bi bi-download me-1"></i> Download
                                    </a>

                                    <!-- Restore Button -->
                                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#restoreModal-{{ md5($b['filename']) }}" title="Restore database from this snapshot">
                                        <i class="bi bi-arrow-repeat me-1"></i> Restore
                                    </button>

                                    <!-- Delete Button -->
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ md5($b['filename']) }}" title="Delete snapshot">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <!-- Restore Modal -->
                                <div class="modal fade text-start" id="restoreModal-{{ md5($b['filename']) }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold text-warning">
                                                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Database Restoration
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="mb-2">Are you sure you want to restore the database from snapshot <strong>{{ $b['filename'] }}</strong>?</p>
                                                <div class="alert alert-warning small mb-0 rounded-3">
                                                    <i class="bi bi-info-circle me-1"></i> This will replace current database tables and records with the state saved in this snapshot.
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.security.backups.restore', $b['filename']) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-warning rounded-pill px-4">Confirm Restoration</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade text-start" id="deleteModal-{{ md5($b['filename']) }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold text-danger">
                                                    <i class="bi bi-trash3 me-2"></i> Delete Backup Snapshot
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to permanently delete backup archive <strong>{{ $b['filename'] }}</strong>?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.security.backups.delete', $b['filename']) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger rounded-pill px-4">Delete Backup</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-database-x fs-1 d-block mb-3 opacity-50"></i>
                                <h6 class="fw-bold">No Backup Snapshots Created Yet</h6>
                                <p class="small mb-3">Click "Create Database Backup Now" to generate a full snapshot of all articles, users, comments, settings, and tables.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
