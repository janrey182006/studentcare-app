<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\Recaptcha;
use App\Services\OtpService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request, OtpService $otp): RedirectResponse
    {
        $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'student_id' => ['required', 'string', 'max:30', 'unique:users,student_id'],
            'course'     => ['required', 'string', 'max:100'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
            'g-recaptcha-response' => Recaptcha::rules(),
        ], [
            'g-recaptcha-response.required' => 'Please tick the "I\'m not a robot" box.',
        ]);

        $user = User::create([
            'name'       => $request->name,
            'student_id' => $request->student_id,
            'course'     => $request->course,
            'email'      => $request->email,
            'password'   => $request->password, // hashed automatically by the model cast
            'role'       => 'student',          // never taken from user input
        ]);

        event(new Registered($user));
        Auth::login($user);
        $otp->send($user);

        return $otp->enabled()
            ? redirect()->route('otp.show')
            : redirect()->route('dashboard');
    }
}
