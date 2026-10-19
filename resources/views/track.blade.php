<x-guest-layout>
    <h1 class="text-2xl font-extrabold text-slate-900">Track a request</h1>
    <form method="GET" action="{{ route('track') }}" class="mt-5 flex gap-2">
        <input name="ref" value="{{ $ref }}" required maxlength="30" placeholder="SSR-2026-XXXXXX" class="min-w-0 flex-1 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <button class="rounded-lg bg-indigo-600 px-4 font-semibold text-white hover:bg-indigo-700">Track</button>
    </form>

    @if ($ref && ! $found)
        <p class="mt-6 rounded-lg bg-rose-50 p-3 text-sm text-rose-700">No request found with that reference number. Please check and try again.</p>
    @endif

    @if ($found)
        <div class="mt-6 rounded-xl bg-slate-50 p-4 ring-1 ring-slate-200">
            <p class="font-mono text-xs text-slate-400">{{ $found->reference_no }}</p>
            <h2 class="mt-1 font-bold text-slate-900">{{ $found->subject }}</h2>
            <p class="text-sm text-slate-500">{{ $found->category->name }} &middot; submitted {{ $found->created_at->format('M d, Y') }}</p>
            <div class="mt-3"><x-status-badge :status="$found->status" /></div>
        </div>
        <ol class="mt-5 space-y-4 border-l-2 border-indigo-100 pl-5">
            @foreach ($found->updates as $u)
                <li class="relative">
                    <span class="absolute -left-[27px] top-1 h-3 w-3 rounded-full bg-indigo-500 ring-4 ring-white"></span>
                    <p class="text-sm font-medium text-slate-800">{{ $u->message }}</p>
                    <p class="text-xs text-slate-400">{{ $u->created_at->format('M d, Y g:i A') }}</p>
                </li>
            @endforeach
        </ol>
    @endif

    <p class="mt-8 text-center text-sm"><a href="{{ route('home') }}" class="text-indigo-600 hover:underline">&larr; Back to home</a></p>
</x-guest-layout>
