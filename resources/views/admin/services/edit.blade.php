@extends('layouts.admin')

@section('title', 'Edit Service: ' . $service->title . ' - Shrawan Effects')
@section('page_title', 'Edit Service')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Edit Service: {{ $service->title }}</h4>
            <p class="text-body-secondary small mb-0">Update service scope, deliverables, pricing, and visibility.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('services.show', $service->slug) }}" target="_blank" class="btn btn-outline-primary rounded-pill px-3">
                <i class="bi bi-box-arrow-up-right me-1"></i> View Live
            </a>
            <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Back to Services
            </a>
        </div>
    </div>

    <form action="{{ route('admin.services.update', $service->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <!-- Left Main Column -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 rounded-4 bg-body mb-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Service Overview</h5>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Service Title *</label>
                        <input type="text" name="title" class="form-control rounded-3" value="{{ old('title', $service->title) }}" required>
                        @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">URL Slug</label>
                        <input type="text" name="slug" class="form-control rounded-3 font-monospace" value="{{ old('slug', $service->slug) }}">
                        @error('slug') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Short Summary / Excerpt * (Max 500 chars)</label>
                        <textarea name="short_description" class="form-control rounded-3" rows="3" required>{{ old('short_description', $service->short_description) }}</textarea>
                        @error('short_description') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Detailed Description & Scope (HTML / Rich details)</label>
                        <textarea name="content" class="form-control rounded-3" rows="8">{{ old('content', $service->content) }}</textarea>
                        @error('content') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    @php
                        $featuresText = is_array($service->features) ? implode("\n", $service->features) : '';
                    @endphp
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Key Features & Deliverables (One per line)</label>
                        <textarea name="features" class="form-control rounded-3 font-monospace" rows="5">{{ old('features', $featuresText) }}</textarea>
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
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActiveSwitch" value="1" {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isActiveSwitch">Active & Visible to Public</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="isFeaturedSwitch" value="1" {{ old('is_featured', $service->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isFeaturedSwitch">Featured Service</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Display Order</label>
                        <input type="number" name="order" class="form-control rounded-3 font-monospace" value="{{ old('order', $service->order) }}" min="0">
                        <small class="text-muted">Lower numbers appear first.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Bootstrap Icon Class</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi {{ $service->icon ?: 'bi-briefcase' }}"></i></span>
                            <input type="text" name="icon" class="form-control font-monospace" value="{{ old('icon', $service->icon) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Indicative Pricing</label>
                        <input type="text" name="pricing_starts_at" class="form-control font-monospace" value="{{ old('pricing_starts_at', $service->pricing_starts_at) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Tech Stack / Technologies</label>
                        <input type="text" name="technologies" class="form-control" value="{{ old('technologies', $service->technologies) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Service Cover Image</label>
                        @if($service->image)
                            <div class="mb-2">
                                <img src="{{ $service->image_url }}" alt="{{ $service->title }}" class="img-fluid rounded-3" style="max-height: 120px; object-fit: cover;">
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control mb-2" accept="image/*">
                        <input type="url" name="image_url" class="form-control form-control-sm font-monospace" placeholder="Or paste external image URL..." value="{{ old('image_url', str_starts_with($service->image ?? '', 'http') ? $service->image : '') }}">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2.5 fw-bold shadow-sm">
                        <i class="bi bi-save me-1"></i> Update Service
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
