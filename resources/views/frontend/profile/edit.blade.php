@extends('layouts.app')

@section('title', 'Profile & Security Settings - ' . config('app.name', 'Laravel 12 Blog'))

@section('content')
    <div class="py-4 bg-body-tertiary border-bottom mb-5">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Profile & Security</li>
                </ol>
            </nav>
            <h1 class="fw-bold mb-1">Account & Privacy Settings</h1>
            <p class="text-body-secondary mb-0">Manage your profile details, cyber security credentials, session devices, and GDPR privacy rights.</p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row justify-content-center g-4">
            <!-- Left: Profile Details & Password -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Avatar Preview & Upload -->
                        <div class="d-flex align-items-center gap-4 mb-4 pb-4 border-bottom">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle shadow-sm" width="84" height="84" style="object-fit: cover;">
                            <div>
                                <label class="form-label fw-bold small mb-1">Profile Photo</label>
                                <input type="file" name="avatar" class="form-control form-control-sm rounded-pill" accept="image/*">
                                <small class="text-muted">JPG, PNG, WebP up to 2MB</small>
                            </div>
                        </div>

                        <!-- Basic Info -->
                        <h5 class="fw-bold mb-3"><i class="bi bi-person me-2 text-primary"></i> Basic Information</h5>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control rounded-pill" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control rounded-pill" value="{{ old('email', $user->email) }}" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Author Bio</label>
                            <textarea name="bio" class="form-control rounded-4" rows="3" placeholder="Tell readers a bit about yourself...">{{ old('bio', $user->bio) }}</textarea>
                        </div>

                        <hr class="my-4">

                        <!-- Password Change -->
                        <h5 class="fw-bold mb-3"><i class="bi bi-shield-lock me-2 text-primary"></i> Change Password <small class="text-muted fw-normal fs-6">(Optional)</small></h5>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Current Password</label>
                            <input type="password" name="current_password" class="form-control rounded-pill" placeholder="Required only if changing password">
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">New Password (8+ characters)</label>
                                <input type="password" name="password" class="form-control rounded-pill" placeholder="••••••••">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control rounded-pill" placeholder="••••••••">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-sm">
                            <i class="bi bi-check2-circle me-1"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right: GDPR Data Protection, Session Management & Account Erasure -->
            <div class="col-lg-5">
                <!-- 1. Data Privacy & GDPR Portability -->
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                    <h5 class="fw-bold mb-2"><i class="bi bi-shield-check text-success me-2"></i> Data Privacy & Portability</h5>
                    <p class="text-body-secondary small mb-3">
                        In compliance with GDPR and privacy standards, you have full ownership of your data. You can download an export of your personal information, articles, comments, and activity logs.
                    </p>
                    <a href="{{ route('profile.export-data') }}" class="btn btn-outline-primary rounded-pill w-100 mb-2">
                        <i class="bi bi-filetype-json me-1"></i> Download My Data (JSON Export)
                    </a>
                </div>

                <!-- 2. Active Session Management -->
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                    <h5 class="fw-bold mb-2"><i class="bi bi-laptop text-primary me-2"></i> Session Security</h5>
                    <p class="text-body-secondary small mb-3">
                        If you suspect unauthorized access or lost a device, you can revoke access from all other devices immediately.
                    </p>
                    <button type="button" class="btn btn-outline-warning rounded-pill w-100" data-bs-toggle="modal" data-bs-target="#logoutOtherDevicesModal">
                        <i class="bi bi-box-arrow-right me-1"></i> Log Out All Other Devices
                    </button>
                </div>

                <!-- 3. Recent Security Activity -->
                @if(isset($recentAuditLogs) && $recentAuditLogs->count() > 0)
                    <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-clock-history text-info me-2"></i> Recent Account Activity</h6>
                        <ul class="list-group list-group-flush small">
                            @foreach($recentAuditLogs as $log)
                                <li class="list-group-item px-0 bg-transparent d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="fw-semibold d-block">{{ $log->description }}</span>
                                        <small class="text-muted">IP: {{ $log->ip_address ?? 'Local' }}</small>
                                    </div>
                                    <span class="text-muted" style="font-size: 0.75rem;">{{ $log->created_at->diffForHumans() }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- 4. Danger Zone: Right to be Forgotten -->
                <div class="card border-danger border-opacity-25 shadow-sm p-4 rounded-4 bg-danger bg-opacity-10">
                    <h5 class="fw-bold text-danger mb-2"><i class="bi bi-exclamation-octagon-fill me-2"></i> Danger Zone</h5>
                    <p class="small text-body mb-3">
                        Permanently erase your account and associated personal data under GDPR Right to be Forgotten. This action cannot be undone.
                    </p>
                    <button type="button" class="btn btn-danger rounded-pill w-100" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        <i class="bi bi-trash3-fill me-1"></i> Delete My Account
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Logout Other Devices -->
    <div class="modal fade" id="logoutOtherDevicesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-warning"><i class="bi bi-shield-lock me-2"></i> Terminate Other Sessions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('profile.logout-other-devices') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="small text-muted mb-3">Please enter your password to confirm logging out from all other browsers and devices.</p>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Current Password</label>
                            <input type="password" name="password" class="form-control rounded-pill" placeholder="••••••••" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning rounded-pill px-4">Log Out Other Devices</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Delete Account -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Permanent Erasure</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('profile.delete-account') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p class="small text-muted mb-3">Are you sure you want to permanently delete your account? All your personal information will be wiped immediately.</p>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control rounded-pill" placeholder="••••••••" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Confirm Permanent Deletion</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
