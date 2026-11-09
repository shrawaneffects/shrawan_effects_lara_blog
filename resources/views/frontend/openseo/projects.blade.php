@extends('frontend.openseo.layouts.openseo')

@section('title', 'Manage Projects - OpenSEO')

@section('openseo_content')
<div class="row g-4">
    <!-- Projects List -->
    <div class="col-lg-8">
        <div class="openseo-hud-card p-3 p-md-4">
            <h5 class="font-cyber fw-bold text-white mb-1">Your SEO Projects</h5>
            <p class="text-body-secondary font-mono small mb-4">Domains currently tracked in OpenSEO</p>

            <div class="row g-3">
                @foreach($projects as $p)
                    <div class="col-12">
                        <div class="p-3 rounded-3 bg-dark border {{ ($activeProject && $activeProject->id === $p->id) ? 'border-success' : 'border-secondary border-opacity-50' }} d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 shadow">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h6 class="text-white fw-bold font-cyber mb-0 fs-5">{{ $p->name }}</h6>
                                    @if($activeProject && $activeProject->id === $p->id)
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 font-mono small">
                                            ACTIVE PROJECT
                                        </span>
                                    @endif
                                    @if($p->is_default)
                                        <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-50 font-mono small">
                                            DEFAULT
                                        </span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-3 font-mono small text-body-secondary flex-wrap">
                                    <span class="text-white"><i class="bi bi-globe2 me-1"></i> {{ $p->domain }}</span>
                                    <span><i class="bi bi-geo-alt me-1"></i> {{ $p->target_country }} ({{ $p->target_language }})</span>
                                    <span><i class="bi bi-key me-1"></i> {{ $p->keywords_count }} Keywords</span>
                                    <span><i class="bi bi-crosshair me-1"></i> {{ $p->competitors_count }} Competitors</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                @if(!$activeProject || $activeProject->id !== $p->id)
                                    <a href="{{ route('openseo.projects.switch', $p->id) }}" class="btn btn-sm btn-outline-success font-mono">
                                        Switch To
                                    </a>
                                @else
                                    <a href="{{ route('openseo.domain') }}" class="btn btn-sm btn-gradient font-mono">
                                        Open Dashboard
                                    </a>
                                @endif

                                @if($projects->count() > 1 && (!$activeProject || $activeProject->id !== $p->id))
                                    <form action="{{ route('openseo.projects.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Delete project {{ $p->name }}?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger font-mono p-1 px-2" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Create New Project Form -->
    <div class="col-lg-4">
        <div class="openseo-hud-card p-3 p-md-4">
            <h5 class="font-cyber fw-bold text-white mb-1">Add New Domain</h5>
            <p class="text-body-secondary font-mono small mb-3">Track rankings and backlinks for another website</p>

            <form action="{{ route('openseo.projects.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label font-mono small text-body-secondary">Project Name</label>
                    <input type="text" name="name" class="form-control bg-dark text-white border-secondary border-opacity-50 font-mono" placeholder="e.g. Acme Corp" required>
                </div>
                <div class="mb-3">
                    <label class="form-label font-mono small text-body-secondary">Website Domain</label>
                    <input type="text" name="domain" class="form-control bg-dark text-white border-secondary border-opacity-50 font-mono" placeholder="acme.com" required>
                </div>
                <div class="mb-3">
                    <label class="form-label font-mono small text-body-secondary">Target Search Country</label>
                    <select name="target_country" class="form-select bg-dark text-white border-secondary border-opacity-50 font-mono">
                        <option value="US">United States (US)</option>
                        <option value="IN" selected>India (IN)</option>
                        <option value="GB">United Kingdom (GB)</option>
                        <option value="CA">Canada (CA)</option>
                        <option value="AU">Australia (AU)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label font-mono small text-body-secondary">Target Language</label>
                    <select name="target_language" class="form-select bg-dark text-white border-secondary border-opacity-50 font-mono">
                        <option value="en" selected>English (en)</option>
                        <option value="hi">Hindi (hi)</option>
                        <option value="es">Spanish (es)</option>
                        <option value="de">German (de)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-gradient w-100 font-mono py-2.5">
                    <i class="bi bi-plus-circle me-1"></i> Create Project
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
