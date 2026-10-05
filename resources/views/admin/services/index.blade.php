@extends('layouts.admin')

@section('title', 'Manage Services - Shrawan Effects')
@section('page_title', 'Services Management')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">Company Services Suite</h4>
            <p class="text-body-secondary small mb-0">Manage services offered by Shrawan Effects in Faridabad (Web Dev, Custom SaaS, E-Commerce, APIs, SEO).</p>
        </div>
        <a href="{{ route('admin.services.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-circle me-1"></i> Add New Service
        </a>
    </div>

    <!-- Overview Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-body-secondary small fw-semibold">Total Services</span>
                        <h4 class="fw-bold mb-0 text-gradient mt-1">{{ number_format($totalServices) }}</h4>
                    </div>
                    <div class="bg-primary-subtle text-primary p-3 rounded-4">
                        <i class="bi bi-briefcase fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-body-secondary small fw-semibold">Active & Live</span>
                        <h4 class="fw-bold mb-0 text-success mt-1">{{ number_format($activeServices) }}</h4>
                    </div>
                    <div class="bg-success-subtle text-success p-3 rounded-4">
                        <i class="bi bi-check2-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-body-secondary small fw-semibold">Featured on Homepage</span>
                        <h4 class="fw-bold mb-0 text-warning mt-1">{{ number_format($featuredServices) }}</h4>
                    </div>
                    <div class="bg-warning-subtle text-warning p-3 rounded-4">
                        <i class="bi bi-star-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="card border-0 shadow-sm p-3 mb-4 rounded-3 bg-body">
        <form action="{{ route('admin.services.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-body-tertiary"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search services by title, tech stack, or description..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-3 col-6">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                </select>
            </div>

            <div class="col-md-3 col-6 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary rounded-pill flex-grow-1">Filter</button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.services.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Services Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-body overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase small font-monospace">
                    <tr>
                        <th class="ps-4">Service</th>
                        <th>Indicative Pricing</th>
                        <th>Technologies</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-3" style="width: 42px; height: 42px;">
                                        <i class="bi {{ $service->icon ?: 'bi-briefcase' }} fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0">
                                            <a href="{{ route('admin.services.edit', $service->id) }}" class="text-decoration-none text-body">
                                                {{ $service->title }}
                                            </a>
                                            @if($service->is_featured)
                                                <span class="badge bg-warning text-dark ms-1 small"><i class="bi bi-star-fill me-0.5"></i> Featured</span>
                                            @endif
                                        </h6>
                                        <small class="text-muted font-monospace">/services/{{ $service->slug }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success font-monospace px-2.5 py-1">
                                    {{ $service->pricing_starts_at ?: 'Custom Quote' }}
                                </span>
                            </td>
                            <td>
                                <span class="small text-body-secondary text-truncate d-inline-block" style="max-width: 220px;" title="{{ $service->technologies }}">
                                    {{ $service->technologies ?: 'Modern Web Technologies' }}
                                </span>
                            </td>
                            <td>
                                <span class="font-monospace text-muted">{{ $service->order }}</span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input service-toggle" type="checkbox" role="switch" data-id="{{ $service->id }}" {{ $service->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label small text-muted">{{ $service->is_active ? 'Active' : 'Inactive' }}</label>
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('services.show', $service->slug) }}" target="_blank" class="btn btn-outline-secondary" title="View Public Page">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                    <a href="{{ route('admin.services.edit', $service->id) }}" class="btn btn-outline-primary" title="Edit Service">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteServiceModal{{ $service->id }}" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <!-- Delete Confirmation Modal -->
                                <div class="modal fade text-start" id="deleteServiceModal{{ $service->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow rounded-4">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i> Delete Service</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body py-3">
                                                Are you sure you want to delete the service <strong>"{{ $service->title }}"</strong>? This action cannot be undone.
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-sm btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">Confirm Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-briefcase fs-1 d-block mb-2 opacity-50"></i>
                                No services found. Click "Add New Service" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($services->hasPages())
            <div class="p-3 border-top">
                {{ $services->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        document.querySelectorAll('.service-toggle').forEach(function(toggle) {
            toggle.addEventListener('change', function() {
                const serviceId = this.dataset.id;
                const label = this.nextElementSibling;
                fetch(`/admin/services/${serviceId}/toggle-active`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        label.textContent = data.is_active ? 'Active' : 'Inactive';
                    }
                })
                .catch(err => {
                    console.error('Error toggling service status:', err);
                });
            });
        });
    </script>
    @endpush
@endsection
