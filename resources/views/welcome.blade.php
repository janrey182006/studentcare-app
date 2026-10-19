<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StudentCare &middot; Student Services Request System</title>
    <meta name="description" content="Submit and track student service requests online.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-slate-800 antialiased" style="font-family:'Inter',ui-sans-serif,system-ui,sans-serif">

{{-- Navigation (includes a mobile menu) --}}
<header x-data="{ open: false }" class="sticky top-0 z-30 border-b border-slate-100 bg-white/85 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-600 to-violet-600 font-extrabold text-white">S</span>
            <span class="text-lg font-extrabold text-slate-900">StudentCare</span>
        </a>
        <nav class="hidden items-center gap-8 text-sm font-medium text-slate-600 md:flex">
            <a href="#features" class="hover:text-indigo-600">Features</a>
            <a href="#how" class="hover:text-indigo-600">How it works</a>
            <a href="#services" class="hover:text-indigo-600">Services</a>
            <a href="#track" class="hover:text-indigo-600">Track a request</a>
        </nav>
        <div class="hidden items-center gap-3 md:flex">
            @auth
                <a href="{{ route('dashboard') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">Go to dashboard</a>
            @else
                <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-indigo-600">Log in</a>
                <a href="{{ route('register') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">Get started</a>
            @endauth
        </div>
        <button @click="open = !open" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 md:hidden" aria-label="Toggle menu"><x-icon name="menu" /></button>
    </div>
    <div x-show="open" x-transition style="display:none" class="border-t border-slate-100 bg-white px-5 py-4 md:hidden">
        <div class="flex flex-col gap-3 text-sm font-medium text-slate-700">
            <a href="#features" @click="open = false">Features</a><a href="#how" @click="open = false">How it works</a>
            <a href="#services" @click="open = false">Services</a><a href="#track" @click="open = false">Track a request</a>
            @auth <a href="{{ route('dashboard') }}" class="font-semibold text-indigo-600">Dashboard</a>
            @else <a href="{{ route('login') }}">Log in</a><a href="{{ route('register') }}" class="font-semibold text-indigo-600">Create an account</a> @endauth
        </div>
    </div>
</header>

{{-- Hero --}}
<section class="relative overflow-hidden bg-gradient-to-br from-indigo-700 via-violet-700 to-fuchsia-700 text-white">
    <div class="absolute -left-24 top-10 h-80 w-80 rounded-full bg-white/10 blur-2xl"></div>
    <div class="absolute -right-24 bottom-0 h-96 w-96 rounded-full bg-fuchsia-400/20 blur-3xl"></div>
    <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-5 py-20 lg:grid-cols-2 lg:py-28">
        <div>
            <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-xs font-semibold ring-1 ring-white/25">&#9679; Online service desk for students</span>
            <h1 class="mt-6 text-4xl font-extrabold leading-tight tracking-tight sm:text-5xl lg:text-6xl">Skip the line.<br>Send your request <span class="text-amber-300">online.</span></h1>
            <p class="mt-6 max-w-xl text-lg text-indigo-100">Transcripts, scholarships, IT help, counseling, facilities and more. Submit once, track every step, and get notified when staff respond.</p>
            <div class="mt-9 flex flex-wrap gap-3">
                <a href="{{ auth()->check() ? route('requests.create') : route('register') }}" class="rounded-xl bg-white px-6 py-3 text-sm font-bold text-indigo-700 shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl">Submit a request</a>
                <a href="#track" class="rounded-xl bg-white/10 px-6 py-3 text-sm font-semibold ring-1 ring-white/30 transition hover:bg-white/20">Track my request</a>
            </div>
        </div>

        {{-- Decorative preview card --}}
        <div class="relative mx-auto w-full max-w-md">
            <div class="rounded-3xl bg-white p-6 text-slate-800 shadow-2xl ring-1 ring-white/20">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Request SSR-{{ date('Y') }}-A1B2C3</p>
                    <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">In Progress</span>
                </div>
                <h3 class="mt-3 text-lg font-bold">Request for Transcript of Records</h3>
                <ol class="mt-5 space-y-4 text-sm">
                    <li class="flex gap-3"><span class="mt-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500 text-white"><x-icon name="check" class="h-3 w-3" /></span><span><b>Submitted</b><br><span class="text-slate-500">Your request was received.</span></span></li>
                    <li class="flex gap-3"><span class="mt-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500 text-white"><x-icon name="check" class="h-3 w-3" /></span><span><b>Reviewed by staff</b><br><span class="text-slate-500">Assigned to the Registrar's Office.</span></span></li>
                    <li class="flex gap-3"><span class="mt-0.5 h-5 w-5 rounded-full border-2 border-indigo-500 bg-indigo-100"></span><span><b>Being processed</b><br><span class="text-slate-500">Expected within 3 working days.</span></span></li>
                </ol>
            </div>
        </div>
    </div>
</section>

{{-- Live stats from the database --}}
<section class="border-b border-slate-100 bg-white">
    <div class="mx-auto grid max-w-5xl grid-cols-3 gap-4 px-5 py-10 text-center">
        @foreach ([['Requests handled', $stats['total']], ['Resolved', $stats['resolved']], ['Services offered', $stats['categories']]] as [$label, $num])
            <div><p class="text-3xl font-extrabold text-indigo-600 sm:text-4xl">{{ number_format($num) }}</p><p class="mt-1 text-xs font-medium text-slate-500 sm:text-sm">{{ $label }}</p></div>
        @endforeach
    </div>
</section>

{{-- Features --}}
<section id="features" class="bg-slate-50 py-20">
    <div class="mx-auto max-w-7xl px-5">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-indigo-600">Features</p>
            <h2 class="mt-2 text-3xl font-extrabold text-slate-900 sm:text-4xl">Everything students and staff need</h2>
        </div>
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ([
                ['Easy request form', 'Choose a service, describe your concern, set priority, and attach a PDF or photo.'],
                ['Real-time tracking', 'Follow every status change on a clear timeline using your reference number.'],
                ['Email updates', 'Get notified automatically when staff review, process, or resolve your request.'],
                ['Secure sign-in', 'reCAPTCHA protection and an email verification code (OTP) keep accounts safe.'],
                ['Smart dashboards', 'Charts show requests per month, per status, and per service at a glance.'],
                ['Reports & export', 'Staff can filter by date and export a CSV report for the office records.'],
            ] as [$title, $text])
                <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 transition hover:-translate-y-1 hover:shadow-lg">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-500 text-white"><x-icon name="check" /></span>
                    <h3 class="mt-4 text-lg font-bold text-slate-900">{{ $title }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- How it works --}}
<section id="how" class="py-20">
    <div class="mx-auto max-w-5xl px-5">
        <div class="text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-indigo-600">How it works</p>
            <h2 class="mt-2 text-3xl font-extrabold text-slate-900 sm:text-4xl">Four simple steps</h2>
        </div>
        <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([['Create an account', 'Register with your student ID and verify with a code.'], ['Submit a request', 'Pick the service and describe what you need.'], ['Staff reviews it', 'The office assigns, updates, and messages you.'], ['Get resolved', 'Receive the result, then rate your experience.']] as $i => [$t, $d])
                <div class="text-center">
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-indigo-600 text-xl font-extrabold text-white shadow-lg shadow-indigo-200">{{ $i + 1 }}</span>
                    <h3 class="mt-4 font-bold text-slate-900">{{ $t }}</h3>
                    <p class="mt-1 text-sm text-slate-600">{{ $d }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Services (from database) --}}
<section id="services" class="bg-slate-50 py-20">
    <div class="mx-auto max-w-7xl px-5">
        <div class="text-center">
            <p class="text-sm font-bold uppercase tracking-widest text-indigo-600">Services</p>
            <h2 class="mt-2 text-3xl font-extrabold text-slate-900 sm:text-4xl">What can we help you with?</h2>
        </div>
        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($categories as $c)
                <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200/70">
                    <h3 class="font-bold text-slate-900">{{ $c->name }}</h3>
                    <p class="text-xs font-semibold text-indigo-600">{{ $c->office }}</p>
                    <p class="mt-2 text-sm text-slate-600">{{ $c->description }}</p>
                </div>
            @empty
                <p class="col-span-full text-center text-slate-500">Services will appear here once the administrator adds them.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- Public tracker --}}
<section id="track" class="py-20">
    <div class="mx-auto max-w-2xl px-5 text-center">
        <h2 class="text-3xl font-extrabold text-slate-900">Track a request</h2>
        <p class="mt-2 text-slate-600">Enter the reference number you received, for example <span class="font-mono text-sm">SSR-{{ date('Y') }}-XXXXXX</span>.</p>
        <form action="{{ route('track') }}" method="GET" class="mt-6 flex flex-col gap-3 sm:flex-row">
            <input name="ref" required maxlength="30" placeholder="Reference number" class="flex-1 rounded-xl border-slate-300 px-4 py-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <button class="rounded-xl bg-indigo-600 px-6 py-3 font-semibold text-white shadow hover:bg-indigo-700">Track</button>
        </form>
    </div>
</section>

<footer class="bg-slate-900 py-10 text-center text-sm text-slate-400">
    <p class="font-semibold text-white">StudentCare</p>
    <p class="mt-1">Student Services Request System &middot; SIA101 Final Project &middot; Built with Laravel</p>
</footer>
</body>
</html>
