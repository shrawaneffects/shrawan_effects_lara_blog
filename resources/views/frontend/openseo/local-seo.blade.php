@extends('frontend.openseo.layouts.openseo')

@section('title', 'Local Maps SEO & Geo-Grid - ' . $activeProject->domain . ' - OpenSEO')

@section('openseo_content')
<!-- KPI Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">Local Business</span>
            <div class="fw-bold text-white fs-5 font-cyber text-truncate">{{ $activeTracker->business_name ?? 'Shrawan Effects' }}</div>
            <span class="text-body-secondary font-mono small mt-2 d-block text-truncate">
                <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $activeTracker->address ?? 'Faridabad, Haryana' }}
            </span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">Average Map Rank</span>
            <div class="openseo-stat-num text-success">
                #{{ $activeTracker->average_rank ?? 1.8 }}
            </div>
            <span class="text-success font-mono small mt-2 d-block">
                <i class="bi bi-pin-map-fill me-1"></i> Dominant Local Presence
            </span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">Top 3 Grid Pack Share</span>
            <div class="openseo-stat-num text-info">
                {{ $activeTracker->top_3_percentage ?? 88.9 }}%
            </div>
            <span class="text-info font-mono small mt-2 d-block">
                <i class="bi bi-trophy-fill me-1"></i> Appears in Google 3-Pack
            </span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">Grid Configuration</span>
            <div class="openseo-stat-num text-white">3 × 3</div>
            <span class="text-body-secondary font-mono small mt-2 d-block">9 Geo-coordinates scanned</span>
        </div>
    </div>
</div>

<!-- Geo-Grid Visualization and Scan Form -->
<div class="row g-4">
    <!-- Visual Geo-Grid Map Pin Display -->
    <div class="col-lg-6">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h5 class="font-cyber fw-bold text-white mb-0">Google Maps 3×3 Geo-Grid</h5>
                    <p class="text-body-secondary font-mono small mb-0">Rank pins across geographical coordinates</p>
                </div>
                <span class="badge bg-dark text-success border border-success border-opacity-50 font-mono">
                    <span class="cyber-beacon me-1"></span>MAPS ONLINE
                </span>
            </div>

            <!-- Interactive Pin Grid Box -->
            <div class="p-4 rounded-4 bg-dark border border-secondary border-opacity-25 text-center position-relative my-2" style="background: radial-gradient(circle, rgba(9, 18, 6, 0.9) 0%, rgba(5, 8, 4, 0.98) 100%);">
                <div class="row g-3 justify-content-center" style="max-width: 360px; margin: 0 auto;">
                    @php
                        $gridPins = $activeTracker->grid_results ?? [
                            ['point' => 1, 'rank' => 2], ['point' => 2, 'rank' => 1], ['point' => 3, 'rank' => 2],
                            ['point' => 4, 'rank' => 1], ['point' => 5, 'rank' => 1], ['point' => 6, 'rank' => 2],
                            ['point' => 7, 'rank' => 3], ['point' => 8, 'rank' => 2], ['point' => 9, 'rank' => 3],
                        ];
                    @endphp
                    @foreach($gridPins as $pin)
                        @php
                            $rank = $pin['rank'];
                            $pinBg = $rank <= 3 ? 'bg-success text-dark' : ($rank <= 7 ? 'bg-warning text-dark' : 'bg-danger text-white');
                            $pinShadow = $rank <= 3 ? 'box-shadow: 0 0 15px rgba(0, 255, 102, 0.6);' : '';
                        @endphp
                        <div class="col-4">
                            <div class="d-flex flex-column align-items-center justify-content-center p-3 rounded-circle {{ $pinBg }} fw-bold font-mono fs-5 mx-auto transition-transform hover-lift" style="width: 64px; height: 64px; cursor: pointer; {{ $pinShadow }}">
                                #{{ $rank }}
                            </div>
                            <span class="font-mono text-body-secondary small mt-1 d-block" style="font-size: 0.65rem;">
                                Pin {{ $pin['point'] }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center gap-3 mt-4 font-mono small">
                    <span class="text-success"><i class="bi bi-circle-fill me-1"></i> Rank 1-3 (Top Pack)</span>
                    <span class="text-warning"><i class="bi bi-circle-fill me-1"></i> Rank 4-7</span>
                    <span class="text-danger"><i class="bi bi-circle-fill me-1"></i> Rank > 7</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Scan New Grid Keyword Form -->
    <div class="col-lg-6">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <h5 class="font-cyber fw-bold text-white mb-1">Scan Local Rank Grid</h5>
            <p class="text-body-secondary font-mono small mb-3">Audit Google Maps pack position for your targeted keyword</p>

            <form action="{{ route('openseo.local-seo.scan') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label font-mono small text-body-secondary">Business Name</label>
                    <input type="text" name="business_name" value="{{ $activeTracker->business_name ?? 'Shrawan Effects' }}" class="form-control bg-dark text-white border-secondary border-opacity-50 font-mono" required>
                </div>

                <div class="mb-3">
                    <label class="form-label font-mono small text-body-secondary">Target Search Keyword</label>
                    <input type="text" name="keyword" value="{{ $activeTracker->keyword ?? 'Fullstack Website Development Service in Faridabad' }}" class="form-control bg-dark text-white border-secondary border-opacity-50 font-mono" required>
                </div>

                <div class="mb-3">
                    <label class="form-label font-mono small text-body-secondary">Registered Business Address</label>
                    <input type="text" name="address" value="{{ $activeTracker->address ?? 'House No. 1049, Jeevan Nagar, Gounchhi, FARIDABAD, Haryana 121004' }}" class="form-control bg-dark text-white border-secondary border-opacity-50 font-mono">
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label font-mono small text-body-secondary">Latitude</label>
                        <input type="text" name="latitude" value="{{ $activeTracker->latitude ?? '28.3685000' }}" class="form-control bg-dark text-white border-secondary border-opacity-50 font-mono">
                    </div>
                    <div class="col-6">
                        <label class="form-label font-mono small text-body-secondary">Longitude</label>
                        <input type="text" name="longitude" value="{{ $activeTracker->longitude ?? '77.2950000' }}" class="form-control bg-dark text-white border-secondary border-opacity-50 font-mono">
                    </div>
                </div>

                <button type="submit" class="btn btn-gradient w-100 font-mono py-2.5">
                    <i class="bi bi-radar me-1"></i> Scan Local Geo-Grid Now
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
