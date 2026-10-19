{{-- Google reCAPTCHA v2 checkbox. Renders nothing when RECAPTCHA_ENABLED=false. --}}
@if (config('services.recaptcha.enabled'))
    <div class="mt-4">
        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
        <x-input-error :messages="$errors->get('g-recaptcha-response')" class="mt-2" />
    </div>
    @once
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endonce
@endif
