@extends('layouts.admin')

@section('title', 'Add New Service - Shrawan Effects')
@section('page_title', 'Create Service')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Add New Service</h4>
            <p class="text-body-secondary small mb-0">Publish a new engineering or consulting service for Shrawan Effects.</p>
        </div>
        <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-1"></i> Back to Services
        </a>
    </div>

    <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            <!-- Left Main Column -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Service Overview</h5>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Service Title *</label>
                        <input type="text" name="title" class="form-control rounded-3" placeholder="e.g. Fullstack Website Development" value="{{ old('title') }}" required>
                        @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Custom URL Slug (Leave blank to auto-generate)</label>
                        <input type="text" name="slug" class="form-control rounded-3 font-monospace" placeholder="e.g. fullstack-website-development" value="{{ old('slug') }}">
                        @error('slug') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Short Summary / Excerpt * (Max 500 chars)</label>
                        <textarea name="short_description" class="form-control rounded-3" rows="3" placeholder="Brief summary displayed on service cards..." required>{{ old('short_description') }}</textarea>
                        @error('short_description') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Detailed Description & Scope (HTML / Rich details)</label>
                        <textarea name="content" class="form-control rounded-3" rows="8" placeholder="In-depth details about what is included, development methodology, and client benefits...">{{ old('content') }}</textarea>
                        @error('content') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Key Features & Deliverables (One per line)</label>
                        <textarea name="features" class="form-control rounded-3 font-monospace" rows="5" placeholder="Custom Fullstack Architecture&#10;High-Speed Responsive UI&#10;Database Query Optimization&#10;Production Server Hardening">{{ old('features') }}</textarea>
                        <small class="text-muted d-block mt-1">Each line will be displayed as a feature bullet point with a checkmark.</small>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar Column -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Publish Settings</h5>

                    <div class="mb-3">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActiveSwitch" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isActiveSwitch">Active & Visible to Public</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="isFeaturedSwitch" value="1" {{ old('is_featured', false) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isFeaturedSwitch">Featured Service</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Display Order</label>
                        <input type="number" name="order" class="form-control rounded-3 font-monospace" value="{{ old('order', 0) }}" min="0">
                        <small class="text-muted">Lower numbers appear first.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Bootstrap Icon Class</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-star"></i></span>
                            <input type="text" name="icon" class="form-control font-monospace" placeholder="bi-laptop" value="{{ old('icon', 'bi-laptop') }}">
                        </div>
                        <small class="text-muted">Options: bi-laptop, bi-cpu, bi-cart3, bi-phone, bi-diagram-3, bi-graph-up-arrow, bi-cloud-check, bi-megaphone</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Indicative Pricing</label>
                        <input type="text" name="pricing_starts_at" class="form-control font-monospace" placeholder="e.g. Starting from ₹40,000 / project" value="{{ old('pricing_starts_at') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Tech Stack / Technologies</label>
                        <input type="text" name="technologies" class="form-control" placeholder="e.g. Laravel, PHP, React, MySQL, Docker" value="{{ old('technologies') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Service Cover Image</label>
                        <input type="file" name="image" class="form-control mb-2" accept="image/*">
                        <input type="url" name="image_url" class="form-control form-control-sm font-monospace" placeholder="Or paste external image URL..." value="{{ old('image_url') }}">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2.5 fw-bold shadow-sm">
                        <i class="bi bi-check-circle me-1"></i> Save & Publish Service
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
