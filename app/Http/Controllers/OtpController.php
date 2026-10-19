<?php

namespace App\Http\Controllers;

use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OtpController extends Controller
{
    public function __construct(private OtpService $otp)
    {
    }

    public function show(Request $request)
    {
        if (! $this->otp->enabled() || $request->session()->get('otp_verified')) {
            return redirect()->route('dashboard');
        }

        return view('otp.verify');
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'digits:6']], [
            'code.digits' => 'The code must be exactly 6 digits.',
        ]);

        if (! $this->otp->verify($request->user(), $request->code)) {
            throw ValidationException::withMessages(['code' => 'That code is invalid or has expired.']);
        }

        return redirect()->intended(route('dashboard'))->with('success', 'Verification successful. Welcome!');
    }

    public function resend(Request $request): RedirectResponse
    {
        $this->otp->send($request->user());

        return back()->with('success', 'A new code has been sent.');
    }
}
