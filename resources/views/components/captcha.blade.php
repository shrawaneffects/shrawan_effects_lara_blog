@php
    $captchaWidget = \App\Services\Security\CaptchaService::generate();
@endphp

<div class="mb-3 captcha-container">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <label class="form-label fw-semibold small mb-0">
            <i class="bi bi-shield-lock-fill text-success me-1"></i> Security Verification
        </label>
        <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none captcha-refresh-btn text-success" style="font-size: 0.78rem;" title="Load a new security puzzle">
            <i class="bi bi-arrow-clockwise me-1"></i> Refresh
        </button>
    </div>

    <div class="d-flex flex-wrap align-items-stretch gap-2">
        <div class="captcha-img-wrap d-flex align-items-center justify-content-center rounded-3 overflow-hidden border border-success border-opacity-25 flex-shrink-0" style="background: #071206; min-height: 46px;">
            <img src="{{ $captchaWidget['image_data_uri'] }}" alt="Solve the visual math challenge" class="captcha-svg-img" height="46" style="display: block; height: 46px; width: auto; user-select: none; -webkit-user-drag: none;">
        </div>
        <input type="number" name="captcha_answer" class="form-control font-mono fw-bold captcha-answer-input text-center" placeholder="Ans" required autocomplete="off" inputmode="numeric" aria-label="Enter the math challenge answer" style="font-size: 1rem; max-width: 110px;">
    </div>

    <!-- Invisible Anti-Bot Honeypot Trap -->
    <div style="position: absolute; left: -9999px; top: -9999px; width: 1px; height: 1px; overflow: hidden; opacity: 0;" aria-hidden="true">
        <label>Leave this field blank</label>
        <input type="text" name="_hp_token" value="" tabindex="-1" autocomplete="off">
    </div>

    <input type="hidden" name="captcha_token" class="captcha-token-input" value="{{ $captchaWidget['token'] }}">
    <input type="hidden" name="captcha_timestamp" class="captcha-timestamp-input" value="{{ $captchaWidget['timestamp'] }}">
    <input type="hidden" name="captcha_nonce" class="captcha-nonce-input" value="{{ $captchaWidget['nonce'] }}">
    <input type="hidden" name="_form_ts_sig" class="captcha-tssig-input" value="{{ $captchaWidget['form_ts_sig'] }}">

    @error('captcha_answer')
        <div class="text-danger small mt-1 font-mono">
            <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
        </div>
    @enderror
</div>

@once
<script>
    (function() {
        if (window.__shrawanCaptchaInitialized) return;
        window.__shrawanCaptchaInitialized = true;

        document.addEventListener('click', function(e) {
            const refreshBtn = e.target.closest('.captcha-refresh-btn');
            if (!refreshBtn) return;

            e.preventDefault();
            const container = refreshBtn.closest('.captcha-container');
            if (!container) return;

            const icon = refreshBtn.querySelector('i');
            if (icon) icon.style.opacity = '0.5';
            refreshBtn.disabled = true;

            fetch('{{ route("captcha.refresh") }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data && data.success && data.captcha) {
                    const img = container.querySelector('.captcha-svg-img');
                    const tokenInput = container.querySelector('.captcha-token-input');
                    const timeInput = container.querySelector('.captcha-timestamp-input');
                    const nonceInput = container.querySelector('.captcha-nonce-input');
                    const tsSigInput = container.querySelector('.captcha-tssig-input');
                    const answerInput = container.querySelector('.captcha-answer-input');

                    if (img && data.captcha.image_data_uri) img.src = data.captcha.image_data_uri;
                    if (tokenInput) tokenInput.value = data.captcha.token;
                    if (timeInput) timeInput.value = data.captcha.timestamp;
                    if (nonceInput) nonceInput.value = data.captcha.nonce;
                    if (tsSigInput) tsSigInput.value = data.captcha.form_ts_sig;
                    if (answerInput) {
                        answerInput.value = '';
                        answerInput.focus();
                    }
                }
            })
            .catch(function(err) {
                console.error('Failed to refresh security challenge:', err);
            })
            .finally(function() {
                if (icon) icon.style.opacity = '1';
                refreshBtn.disabled = false;
            });
        });
    })();
</script>
@endonce
