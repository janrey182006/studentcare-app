<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOtpVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('services.otp.enabled') && ! $request->session()->get('otp_verified')) {
            return redirect()->route('otp.show');
        }

        return $next($request);
    }
}
