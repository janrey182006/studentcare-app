<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Recaptcha implements ValidationRule
{
    /** Rules to use on forms: required + verified when enabled, ignored when disabled (e.g. tests). */
    public static function rules(): array
    {
        return config('services.recaptcha.enabled') ? ['required', new self()] : ['nullable'];
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $response = Http::asForm()->timeout(8)->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => config('services.recaptcha.secret'),
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        if (! $response->successful() || ! $response->json('success')) {
            $fail('reCAPTCHA verification failed. Please try again.');
        }
    }
}
