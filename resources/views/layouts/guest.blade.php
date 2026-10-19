<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'StudentCare') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased text-slate-800" style="font-family:'Inter',ui-sans-serif,system-ui,sans-serif">
<div class="grid min-h-screen lg:grid-cols-2">
    <div class="relative hidden overflow-hidden bg-gradient-to-br from-indigo-700 via-violet-700 to-fuchsia-700 p-12 text-white lg:flex lg:flex-col lg:justify-between">
        <div class="absolute -left-20 -top-20 h-72 w-72 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-24 -right-16 h-96 w-96 rounded-full bg-white/10"></div>
        <a href="{{ route('home') }}" class="relative flex items-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/15 text-xl font-extrabold ring-1 ring-white/30">S</span>
            <span class="text-xl font-extrabold">StudentCare</span>
        </a>
        <div class="relative">
            <h2 class="text-4xl font-extrabold leading-tight">One place for every<br>student service request.</h2>
            <p class="mt-4 max-w-md text-indigo-100">Submit a concern, track its progress in real time, and get notified the moment staff respond. No more long lines.</p>
            <ul class="mt-8 space-y-3 text-sm text-indigo-50">
                <li class="flex items-center gap-2"><x-icon name="check" class="h-5 w-5 text-emerald-300" /> Track requests with a reference number</li>
                <li class="flex items-center gap-2"><x-icon name="check" class="h-5 w-5 text-emerald-300" /> Email notifications on every update</li>
                <li class="flex items-center gap-2"><x-icon name="check" class="h-5 w-5 text-emerald-300" /> Secure sign-in with email verification code</li>
            </ul>
        </div>
        <p class="relative text-xs text-indigo-200">&copy; {{ date('Y') }} StudentCare &middot; SIA101 Final Project</p>
    </div>

    <div class="flex items-center justify-center bg-slate-50 px-5 py-10">
        <div class="w-full max-w-md">
            <a href="{{ route('home') }}" class="mb-6 flex items-center gap-2 lg:hidden">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 font-extrabold text-white">S</span>
                <span class="text-lg font-extrabold text-slate-900">StudentCare</span>
            </a>
            <div class="rounded-2xl bg-white p-7 shadow-xl shadow-slate-200/60 ring-1 ring-slate-200/70 sm:p-8">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
</body>
</html>
