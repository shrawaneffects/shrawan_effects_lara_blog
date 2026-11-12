@php
    $hpTsData = \App\Services\Security\CaptchaService::generateFormTimestamp();
@endphp
<div style="position: absolute; left: -9999px; top: -9999px; width: 1px; height: 1px; overflow: hidden; opacity: 0;" aria-hidden="true">
    <label>Leave this field blank</label>
    <input type="text" name="_hp_token" value="" tabindex="-1" autocomplete="off">
</div>
<input type="hidden" name="captcha_timestamp" value="{{ $hpTsData['ts'] }}">
<input type="hidden" name="_form_ts_sig" value="{{ $hpTsData['sig'] }}">
