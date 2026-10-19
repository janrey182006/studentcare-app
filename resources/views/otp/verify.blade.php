<x-guest-layout>
    <div class="text-center">
        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-indigo-50 text-indigo-600"><x-icon name="check" class="h-7 w-7" /></span>
        <h1 class="mt-4 text-2xl font-extrabold text-slate-900">Check your email</h1>
        <p class="mt-1 text-sm text-slate-500">We sent a 6-digit code to <b>{{ auth()->user()->email }}</b>. It expires in 10 minutes.</p>
    </div>

    @if (session('success'))
        <p class="mt-4 rounded-lg bg-emerald-50 p-3 text-center text-sm text-emerald-700">{{ session('success') }}</p>
    @endif

    @if (session('otp_hint'))
        <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-3 text-center text-sm text-amber-800">
            Demo account: your code is <span class="font-mono text-lg font-bold tracking-widest">{{ session('otp_hint') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('otp.verify') }}" class="mt-6">
        @csrf
        <input name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6" autofocus required placeholder="______"
               class="block w-full rounded-xl border-slate-300 py-4 text-center font-mono text-3xl tracking-[0.6em] shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <x-input-error :messages="$errors->get('code')" class="mt-2 text-center" />
        <button class="mt-5 w-full rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 py-3 font-semibold text-white shadow-lg shadow-indigo-200">Verify and continue</button>
    </form>

    <div class="mt-5 flex items-center justify-between text-sm">
        <form method="POST" action="{{ route('otp.resend') }}">@csrf <button class="font-medium text-indigo-600 hover:underline">Resend code</button></form>
        <form method="POST" action="{{ route('logout') }}">@csrf <button class="text-slate-500 hover:underline">Cancel &amp; log out</button></form>
    </div>
</x-guest-layout>
