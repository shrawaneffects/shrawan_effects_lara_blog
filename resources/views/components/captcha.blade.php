<div class="mb-3 captcha-container">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <label class="form-label fw-semibold small mb-0">
            <i class="bi bi-shield-lock text-success me-1"></i> Security Verification
        </label>
        <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none captcha-refresh-btn text-success" style="font-size: 0.78rem;" title="Get new question">
            <i class="bi bi-arrow-clockwise me-1"></i> Refresh
        </button>
    </div>
    <div class="input-group">
        <span class="input-group-text font-mono fw-bold captcha-question-badge" style="min-width: 105px; justify-content: center; letter-spacing: 1px; font-size: 0.95rem;">
            {{ $captcha['question'] ?? '8 + 5' }} = ?
        </span>
        <input type="number" name="captcha_answer" class="form-control font-mono fw-bold captcha-answer-input" placeholder="Answer" required autocomplete="off" style="font-size: 0.95rem;">
    </div>
    <input type="hidden" name="captcha_token" class="captcha-token-input" value="{{ $captcha['token'] ?? '' }}">
    <input type="hidden" name="captcha_timestamp" class="captcha-timestamp-input" value="{{ $captcha['timestamp'] ?? time() }}">
    @error('captcha_answer')
        <div class="text-danger small mt-1 font-mono">
            <i class="bi bi-exclamation-circle me-1"></i> {{ $message }}
        </div>
    @enderror
</div>

<script>
    (function() {
        document.addEventListener('DOMContentLoaded', function() {
            const containers = document.querySelectorAll('.captcha-container');
            containers.forEach(function(container) {
                const refreshBtn = container.querySelector('.captcha-refresh-btn');
                const badge = container.querySelector('.captcha-question-badge');
                const tokenInput = container.querySelector('.captcha-token-input');
                const timeInput = container.querySelector('.captcha-timestamp-input');
                const answerInput = container.querySelector('.captcha-answer-input');

                if (refreshBtn) {
                    refreshBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const icon = refreshBtn.querySelector('i');
                        if (icon) icon.classList.add('bi-spin');

                        fetch('{{ route("captcha.refresh") }}', {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(function(res) { return res.json(); })
                        .then(function(data) {
                            if (data.success && data.captcha) {
                                if (badge) badge.textContent = data.captcha.question + ' = ?';
                                if (tokenInput) tokenInput.value = data.captcha.token;
                                if (timeInput) timeInput.value = data.captcha.timestamp;
                                if (answerInput) {
                                    answerInput.value = '';
                                    answerInput.focus();
                                }
                            }
                        })
                        .catch(function(err) {
                            // Fallback local math challenge if network error occurs
                            const n1 = Math.floor(Math.random() * 12) + 3;
                            const n2 = Math.floor(Math.random() * 9) + 2;
                            const ans = n1 + n2;
                            const now = Math.floor(Date.now() / 1000);
                            const token = btoa(ans + ':' + now + ':shrawan_free_captcha');
                            if (badge) badge.textContent = n1 + ' + ' + n2 + ' = ?';
                            if (tokenInput) tokenInput.value = token;
                            if (timeInput) timeInput.value = now;
                            if (answerInput) { answerInput.value = ''; answerInput.focus(); }
                        })
                        .finally(function() {
                            if (icon) icon.classList.remove('bi-spin');
                        });
                    });
                }
            });
        });
    })();
</script>
