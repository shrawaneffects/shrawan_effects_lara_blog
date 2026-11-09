@extends('frontend.openseo.layouts.openseo')

@section('title', 'OpenSEO Settings & API - ' . $activeProject->domain . ' - OpenSEO')

@section('openseo_content')
<div class="row g-4">
    <!-- Project & API Settings Form -->
    <div class="col-lg-8">
        <div class="openseo-hud-card p-3 p-md-4">
            <h5 class="font-cyber fw-bold text-white mb-1">Project & API Integrations</h5>
            <p class="text-body-secondary font-mono small mb-4">Configure domain targets and connect live DataForSEO credentials</p>

            <form action="{{ route('openseo.settings.update') }}" method="POST">
                @csrf

                <!-- Basic Project Details -->
                <h6 class="font-mono text-success text-uppercase mb-3 small fw-bold">1. Project Configuration</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label font-mono small text-body-secondary">Project Name</label>
                        <input type="text" name="name" value="{{ old('name', $activeProject->name) }}" class="form-control bg-dark text-white border-secondary border-opacity-50 font-mono" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small text-body-secondary">Domain Name</label>
                        <input type="text" name="domain" value="{{ old('domain', $activeProject->domain) }}" class="form-control bg-dark text-white border-secondary border-opacity-50 font-mono" placeholder="example.com" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small text-body-secondary">Target Country</label>
                        <select name="target_country" class="form-select bg-dark text-white border-secondary border-opacity-50 font-mono">
                            <option value="US" {{ $activeProject->target_country == 'US' ? 'selected' : '' }}>United States (US)</option>
                            <option value="IN" {{ $activeProject->target_country == 'IN' ? 'selected' : '' }}>India (IN)</option>
                            <option value="GB" {{ $activeProject->target_country == 'GB' ? 'selected' : '' }}>United Kingdom (GB)</option>
                            <option value="CA" {{ $activeProject->target_country == 'CA' ? 'selected' : '' }}>Canada (CA)</option>
                            <option value="AU" {{ $activeProject->target_country == 'AU' ? 'selected' : '' }}>Australia (AU)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small text-body-secondary">Target Language</label>
                        <select name="target_language" class="form-select bg-dark text-white border-secondary border-opacity-50 font-mono">
                            <option value="en" {{ $activeProject->target_language == 'en' ? 'selected' : '' }}>English (en)</option>
                            <option value="hi" {{ $activeProject->target_language == 'hi' ? 'selected' : '' }}>Hindi (hi)</option>
                            <option value="es" {{ $activeProject->target_language == 'es' ? 'selected' : '' }}>Spanish (es)</option>
                            <option value="de" {{ $activeProject->target_language == 'de' ? 'selected' : '' }}>German (de)</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label font-mono small text-body-secondary">Google Search Console Property (Optional)</label>
                        <input type="text" name="gsc_property" value="{{ old('gsc_property', $activeProject->gsc_property) }}" class="form-control bg-dark text-white border-secondary border-opacity-50 font-mono" placeholder="sc-domain:example.com or https://example.com/">
                    </div>
                </div>

                <hr class="border-secondary border-opacity-25 my-4">

                <!-- DataForSEO API Integration -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="font-mono text-success text-uppercase mb-0 small fw-bold">2. DataForSEO API Credentials</h6>
                    @php
                        $client = new \App\Services\OpenSeo\DataForSeoClient($activeProject);
                    @endphp
                    @if($client->isConfigured())
                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 font-mono">
                            <i class="bi bi-check-circle-fill me-1"></i> API KEY CONFIGURED
                        </span>
                    @else
                        <span class="badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-50 font-mono">
                            <i class="bi bi-info-circle me-1"></i> RUNNING LOCAL ENGINE
                        </span>
                    @endif
                </div>

                <p class="text-body-secondary font-mono small mb-3">
                    OpenSEO works with your DataForSEO account to fetch real-time SERPs, backlink indexes, and keyword search volumes on a pay-per-use basis. If left blank, OpenSEO runs on intelligent local simulation algorithms.
                </p>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label font-mono small text-body-secondary">DataForSEO API Login</label>
                        <input type="text" name="dataforseo_login" value="{{ old('dataforseo_login', $activeProject->dataforseo_login) }}" class="form-control bg-dark text-white border-secondary border-opacity-50 font-mono" placeholder="e.g. your_email@example.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small text-body-secondary">DataForSEO API Password / Key</label>
                        <input type="password" name="dataforseo_password" value="{{ old('dataforseo_password', $activeProject->dataforseo_password) }}" class="form-control bg-dark text-white border-secondary border-opacity-50 font-mono" placeholder="••••••••••••••••">
                    </div>
                </div>

                <button type="submit" class="btn btn-gradient font-mono px-4 py-2">
                    <i class="bi bi-save me-1"></i> Save Settings
                </button>
            </form>
        </div>
    </div>

    <!-- Info & Documentation Side Card -->
    <div class="col-lg-4">
        <div class="openseo-hud-card p-3 p-md-4 mb-4">
            <h6 class="font-cyber fw-bold text-white mb-2">How DataForSEO Works</h6>
            <p class="text-slate-300 small mb-3">
                Unlike Semrush or Ahrefs which charge $130 - $400/month subscriptions, OpenSEO is pay-as-you-go. You connect your own DataForSEO account and only pay fractions of a cent per query.
            </p>
            <ul class="text-slate-400 small ps-3 mb-3 font-mono">
                <li>Keyword search: ~$0.001 / query</li>
                <li>Live SERP check: ~$0.002 / check</li>
                <li>Backlink lookup: ~$0.02 / domain</li>
            </ul>
            <a href="https://dataforseo.com" target="_blank" class="btn btn-sm btn-outline-info font-mono w-100">
                <i class="bi bi-box-arrow-up-right me-1"></i> Get DataForSEO Account
            </a>
        </div>

        <div class="openseo-hud-card p-3 p-md-4">
            <h6 class="font-cyber fw-bold text-white mb-2">System Telemetry</h6>
            <div class="d-flex flex-column gap-2 font-mono small">
                <div class="d-flex justify-content-between">
                    <span class="text-body-secondary">OpenSEO Version:</span>
                    <span class="text-success">v0.1.26 (Native)</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-body-secondary">PHP Engine:</span>
                    <span class="text-white">{{ PHP_VERSION }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-body-secondary">Framework:</span>
                    <span class="text-white">Laravel {{ app()->version() }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-body-secondary">Active Database:</span>
                    <span class="text-info">{{ config('database.connections.mysql.database') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
