@php
    $adsEnabled = \App\Models\Setting::get('ads_enabled', '1') === '1';
    $adCode = \App\Models\Setting::get('ad_slot_' . $slotName);
    $showPlaceholders = \App\Models\Setting::get('ads_placeholders', '1') === '1';
    $adsensePub = \App\Models\Setting::get('adsense_publisher_id');
@endphp

@if($adsEnabled)
    @if(!empty($adCode))
        <!-- Ad Slot: {{ $slotName }} -->
        <div class="ad-slot-wrapper text-center {{ $class ?? 'my-4' }}" data-slot="{{ $slotName }}">
            <div class="ad-slot-badge text-body-tertiary text-uppercase mb-1" style="font-size: 0.65rem; letter-spacing: 1px; font-weight: 600;">
                <i class="bi bi-badge-ad me-1 text-primary"></i> Advertisement
            </div>
            <div class="ad-slot-body mx-auto overflow-hidden d-flex justify-content-center">
                {!! $adCode !!}
            </div>
        </div>
    @elseif($showPlaceholders)
        <!-- Ad Slot Placeholder: {{ $slotName }} -->
        <div class="ad-slot-placeholder-box text-center {{ $class ?? 'my-4' }}" data-slot="{{ $slotName }}">
            <div class="p-3 p-md-4 rounded-4 border border-dashed bg-body-tertiary d-flex flex-column align-items-center justify-content-center shadow-xs position-relative overflow-hidden" style="min-height: {{ $minHeight ?? '100px' }}; border-color: rgba(99, 102, 241, 0.25) !important;">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-0.5" style="font-size: 0.68rem;">
                        <i class="bi bi-badge-ad me-1"></i> Google AdSense Space
                    </span>
                    <span class="text-body-tertiary small" style="font-size: 0.75rem;">{{ $label ?? ucfirst(str_replace('_', ' ', $slotName)) }}</span>
                </div>
                <div class="text-body-secondary small" style="font-size: 0.78rem;">
                    @if($adsensePub)
                        <i class="bi bi-broadcast text-success me-1"></i> Connected to AdSense Client (<code class="small">{{ $adsensePub }}</code>)
                    @else
                        Configure custom AdSense unit code in <a href="{{ route('admin.settings.index') }}" class="text-primary text-decoration-none fw-semibold">Website Settings</a>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endif
