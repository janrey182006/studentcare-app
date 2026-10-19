<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Rules\Recaptcha;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request, OtpService $otp): RedirectResponse
    {
        // Security feature #1: reCAPTCHA on the login form
        $request->validate(
            ['g-recaptcha-response' => Recaptcha::rules()],
            ['g-recaptcha-response.required' => 'Please tick the "I\'m not a robot" box.']
        );

        $request->authenticate();

        if (! $request->user()->is_active) {
            Auth::guard('web')->logout();
            return back()->withErrors(['email' => 'This account has been deactivated. Contact the administrator.']);
        }

        $request->session()->regenerate();

        // Security feature #2: email OTP
        $otp->send($request->user());

        return $otp->enabled()
            ? redirect()->route('otp.show')
            : redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
