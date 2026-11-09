@extends('frontend.openseo.layouts.openseo')

@section('title', 'SEO Reports & Exports - ' . $activeProject->domain . ' - OpenSEO')

@section('openseo_content')
<!-- Actions & Generation Bar -->
<div class="row g-4 mb-4">
    <!-- Generate Report Card -->
    <div class="col-lg-7">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <h5 class="font-cyber fw-bold text-white mb-1">Generate Audit Report</h5>
            <p class="text-body-secondary font-mono small mb-3">Create an executive client-ready SEO snapshot with public share link</p>

            <form action="{{ route('openseo.reports.generate') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label font-mono small text-body-secondary">Report Title</label>
                    <input type="text" name="title" value="Monthly Executive SEO Audit - {{ $activeProject->name }} ({{ date('F Y') }})" class="form-control bg-dark text-white border-secondary border-opacity-50 font-mono" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label font-mono small text-body-secondary">Report Type</label>
                        <select name="report_type" class="form-select bg-dark text-white border-secondary border-opacity-50 font-mono">
                            <option value="executive_audit">Executive Site Audit</option>
                            <option value="rank_tracking">Rankings & Growth Report</option>
                            <option value="backlink_profile">Backlinks & Authority Profile</option>
                        </select>
                    </div>
                    <div class="col-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-gradient w-100 font-mono py-2">
                            <i class="bi bi-file-earmark-plus me-1"></i> Generate Report Now
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Direct CSV Exports Card -->
    <div class="col-lg-5">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <h5 class="font-cyber fw-bold text-white mb-1">Instant Data Downloads</h5>
            <p class="text-body-secondary font-mono small mb-3">Export raw data tables to spreadsheet CSV</p>

            <div class="d-flex flex-column gap-2">
                <a href="{{ route('openseo.reports.export', 'keywords') }}" class="btn btn-outline-success font-mono text-start d-flex align-items-center justify-content-between p-2.5">
                    <span><i class="bi bi-file-earmark-spreadsheet me-2"></i> Tracked Keywords & SERPs (.csv)</span>
                    <i class="bi bi-download"></i>
                </a>
                <a href="{{ route('openseo.reports.export', 'backlinks') }}" class="btn btn-outline-info font-mono text-start d-flex align-items-center justify-content-between p-2.5">
                    <span><i class="bi bi-link-45deg me-2"></i> Backlink Profile & Anchors (.csv)</span>
                    <i class="bi bi-download"></i>
                </a>
                <a href="{{ route('openseo.reports.export', 'audit') }}" class="btn btn-outline-warning font-mono text-start d-flex align-items-center justify-content-between p-2.5">
                    <span><i class="bi bi-shield-exclamation me-2"></i> Site Technical Audit Issues (.csv)</span>
                    <i class="bi bi-download"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Generated Reports List Table -->
<div class="openseo-hud-card p-3 p-md-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h5 class="font-cyber fw-bold text-white mb-0">Saved Reports Archive</h5>
            <p class="text-body-secondary font-mono small mb-0">Reports generated for {{ $activeProject->name }}</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table openseo-table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Report Title</th>
                    <th>Type</th>
                    <th class="text-center">Health Score</th>
                    <th class="text-center">Keywords</th>
                    <th class="text-center">Est. Traffic</th>
                    <th class="text-end">Generated</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    @php $data = $report->summary_data ?? []; @endphp
                    <tr>
                        <td>
                            <div class="fw-bold text-white fs-6">{{ $report->title }}</div>
                            <span class="text-body-secondary font-mono small">{{ $data['domain'] ?? $activeProject->domain }}</span>
                        </td>
                        <td>
                            <span class="badge bg-dark text-info border border-info border-opacity-25 font-mono">
                                {{ ucwords(str_replace('_', ' ', $report->report_type)) }}
                            </span>
                        </td>
                        <td class="text-center font-mono text-success fw-bold">
                            {{ $data['health_score'] ?? 'N/A' }}%
                        </td>
                        <td class="text-center font-mono">
                            {{ $data['total_keywords'] ?? '0' }}
                        </td>
                        <td class="text-center font-mono text-info">
                            {{ number_format($data['organic_traffic'] ?? 0) }}
                        </td>
                        <td class="text-end font-mono small text-body-secondary text-nowrap">
                            {{ $report->created_at->format('M d, Y') }}
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('openseo.reports.show', $report->share_token) }}" target="_blank" class="btn btn-sm btn-gradient font-mono py-1 px-2.5">
                                <i class="bi bi-eye me-1"></i> View Report
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-body-secondary font-mono py-4">
                            No reports generated yet. Click "Generate Report Now" above to create your first report.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reports->hasPages())
        <div class="p-3 border-top border-secondary border-opacity-25">
            {{ $reports->links() }}
        </div>
    @endif
</div>
@endsection
