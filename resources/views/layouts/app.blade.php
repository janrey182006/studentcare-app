@php
    $user = auth()->user();
    $nav = [
        ['route' => 'dashboard',      'match' => 'dashboard',  'label' => 'Dashboard',  'icon' => 'home',  'show' => true],
        ['route' => 'requests.index', 'match' => 'requests.index|requests.show|requests.edit', 'label' => $user->isStudent() ? 'My Requests' : 'All Requests', 'icon' => 'inbox', 'show' => true],
        ['route' => 'requests.create','match' => 'requests.create', 'label' => 'New Request', 'icon' => 'plus', 'show' => $user->isStudent()],
        ['route' => 'reports.index',  'match' => 'reports.*',  'label' => 'Reports',    'icon' => 'chart', 'show' => $user->isStaff()],
        ['route' => 'admin.categories.index', 'match' => 'admin.categories.*', 'label' => 'Services', 'icon' => 'tag', 'show' => $user->isAdmin()],
        ['route' => 'admin.users.index', 'match' => 'admin.users.*', 'label' => 'Users', 'icon' => 'users', 'show' => $user->isAdmin()],
    ];
@endphp
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
<body class="bg-slate-50 text-slate-800 antialiased" style="font-family:'Inter',ui-sans-serif,system-ui,sans-serif">
<div x-data="{ sidebar: false }" class="min-h-screen">

    {{-- Mobile backdrop --}}
    <div x-show="sidebar" x-transition.opacity @click="sidebar = false" class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden" style="display:none"></div>

    {{-- Sidebar --}}
    <aside :class="sidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-gradient-to-b from-indigo-700 via-indigo-700 to-violet-800 text-white transition-transform duration-200 print:hidden">
        <a href="{{ route('home') }}" class="flex items-center gap-3 px-6 py-6">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15 text-lg font-extrabold ring-1 ring-white/30">S</span>
            <span><span class="block text-lg font-extrabold leading-tight">StudentCare</span><span class="block text-xs text-indigo-200">Service Request System</span></span>
        </a>

        <nav class="flex-1 space-y-1 px-3">
            @foreach ($nav as $item)
                @if ($item['show'])
                    @php $active = request()->routeIs(...explode('|', $item['match'])); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ $active ? 'bg-white text-indigo-700 shadow' : 'text-indigo-100 hover:bg-white/10' }}">
                        <x-icon :name="$item['icon']" /> {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="m-3 rounded-2xl bg-white/10 p-4">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-sm font-bold text-indigo-700">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold">{{ $user->name }}</p>
                    <p class="text-xs capitalize text-indigo-200">{{ $user->role }}</p>
                </div>
            </div>
            <div class="mt-3 flex gap-2">
                <a href="{{ route('profile.edit') }}" class="flex-1 rounded-lg bg-white/10 py-1.5 text-center text-xs font-medium hover:bg-white/20">Profile</a>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button class="w-full rounded-lg bg-white/10 py-1.5 text-xs font-medium hover:bg-white/20">Log out</button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main --}}
    <div class="lg:pl-64">
        <header class="sticky top-0 z-20 flex items-center gap-3 border-b border-slate-200 bg-white/80 px-4 py-3 backdrop-blur sm:px-8 print:hidden">
            <button @click="sidebar = true" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden" aria-label="Open menu"><x-icon name="menu" /></button>
            <div class="min-w-0 flex-1">
                @isset($header)
                    <h1 class="truncate text-lg font-bold text-slate-900 sm:text-xl">{{ $header }}</h1>
                @endisset
            </div>
            <span class="hidden rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold capitalize text-indigo-700 sm:inline">{{ $user->role }}</span>
        </header>

        <main class="px-4 py-6 sm:px-8">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" class="mb-5 flex items-start justify-between gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    <span>{{ session('success') }}</span><button @click="show = false" class="font-bold">&times;</button>
                </div>
            @endif
            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" class="mb-5 flex items-start justify-between gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <span>{{ session('error') }}</span><button @click="show = false" class="font-bold">&times;</button>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
