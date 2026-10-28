@props(['isConfigured' => false, 'name' => '', 'description' => ''])

@if($isConfigured)
    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 font-mono small" title="{{ $description }}">
        <i class="bi bi-check-circle-fill me-1"></i> Active
    </span>
@else
    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1 font-mono small" title="{{ $description }}" data-bs-toggle="tooltip">
        <i class="bi bi-exclamation-triangle-fill me-1"></i> Setup Required
    </span>
@endif
