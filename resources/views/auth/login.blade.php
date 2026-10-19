<x-guest-layout>
    <h1 class="text-2xl font-extrabold text-slate-900">Welcome back</h1>
    <p class="mt-1 text-sm text-slate-500">Log in to submit or manage service requests.</p>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-6">
        @csrf
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div class="mt-4">
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div class="mt-4 flex items-center justify-between">
            <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"> Remember me
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-indigo-600 hover:underline" href="{{ route('password.request') }}">Forgot password?</a>
            @endif
        </div>

        <x-recaptcha />

        <button class="mt-6 w-full rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 py-3 font-semibold text-white shadow-lg shadow-indigo-200 transition hover:opacity-95">Log in</button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600">New student? <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:underline">Create an account</a></p>

    @if (count(config('services.otp.demo_emails')))
        <div class="mt-6 rounded-xl border border-dashed border-indigo-200 bg-indigo-50/60 p-4 text-xs text-slate-600">
            <p class="font-bold text-indigo-700">Demo accounts (fictional) &mdash; password: Password123!</p>
            <p class="mt-1">Admin: admin@studentcare.test<br>Staff: staff1@studentcare.test<br>Student: student@studentcare.test</p>
            <p class="mt-1 text-slate-500">On demo accounts the OTP code is shown on screen so evaluators can sign in without email access.</p>
        </div>
    @endif
</x-guest-layout>
