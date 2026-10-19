<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public function enabled(): bool
    {
        return (bool) config('services.otp.enabled');
    }

    /** Generate a 6-digit code, store it hashed, and email it. */
    public function send(User $user): void
    {
        if (! $this->enabled()) {
            session()->put('otp_verified', true);
            return;
        }

        session()->put('otp_verified', false);
        session()->forget('otp_hint');

        $code = (string) random_int(100000, 999999);

        $user->forceFill([
            'otp_code' => Hash::make($code),
            'otp_expires_at' => now()->addMinutes(10),
        ])->save();

        // Demo accounts (fictional, listed in .env) get the code shown on screen so an
        // evaluator can log in without owning the mailbox. Real accounts always use email.
        if (in_array($user->email, config('services.otp.demo_emails'), true)) {
            session()->put('otp_hint', $code);
            return;
        }

        rescue(fn () => Mail::to($user->email)->send(new OtpMail($code, $user->name)));
    }

    public function verify(User $user, string $code): bool
    {
        if (! $user->otp_code || ! $user->otp_expires_at || $user->otp_expires_at->isPast()) {
            return false;
        }

        if (! Hash::check($code, $user->otp_code)) {
            return false;
        }

        $user->forceFill(['otp_code' => null, 'otp_expires_at' => null])->save();
        session()->put('otp_verified', true);
        session()->forget('otp_hint');

        return true;
    }
}
