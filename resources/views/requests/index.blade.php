<x-app-layout>
    <x-slot name="header">{{ auth()->user()->isStudent() ? 'My Requests' : 'All Requests' }}</x-slot>

    {{-- Search & filter --}}
    <form method="GET" action="{{ route('requests.index') }}" class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/70">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
            <div class="relative sm:col-span-2">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400"><x-icon name="search" class="h-4 w-4" /></span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search reference, subject, details..." class="w-full rounded-lg border-slate-300 pl-9 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <select name="status" class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All statuses</option>
                @foreach (\App\Models\ServiceRequest::STATUSES as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="category" class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All services</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            <select name="priority" class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Any priority</option>
                @foreach (\App\Models\ServiceRequest::PRIORITIES as $key => $label)
                    <option value="{{ $key }}" @selected(request('priority') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button class="flex-1 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Filter</button>
                <a href="{{ route('requests.index') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">Reset</a>
            </div>
        </div>
        <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-slate-500">
            <label class="flex items-center gap-2">From <input type="date" name="from" value="{{ request('from') }}" class="rounded-lg border-slate-300 py-1 text-xs"></label>
            <label class="flex items-center gap-2">To <input type="date" name="to" value="{{ request('to') }}" class="rounded-lg border-slate-300 py-1 text-xs"></label>
            <span class="ml-auto">{{ $requests->total() }} result(s)</span>
        </div>
    </form>

    {{-- Desktop table --}}
    <div class="mt-5 hidden overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70 md:block">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                <tr>
                    <th class="px-5 py-3">Request</th>
                    @if (auth()->user()->isStaff()) <th class="px-5 py-3">Student</th> @endif
                    <th class="px-5 py-3">Priority</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Submitted</th>
                    <th class="px-5 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($requests as $r)
                    <tr class="hover:bg-slate-50/70">
                        <td class="px-5 py-3.5">
                            <a href="{{ route('requests.show', $r) }}" class="font-semibold text-slate-900 hover:text-indigo-600">{{ \Illuminate\Support\Str::limit($r->subject, 48) }}</a>
                            <p class="text-xs text-slate-500">{{ $r->reference_no }} &middot; {{ $r->category->name }}</p>
                        </td>
                        @if (auth()->user()->isStaff()) <td class="px-5 py-3.5 text-slate-600">{{ $r->user->name }}</td> @endif
                        <td class="px-5 py-3.5"><x-priority-badge :priority="$r->priority" /></td>
                        <td class="px-5 py-3.5"><x-status-badge :status="$r->status" /></td>
                        <td class="px-5 py-3.5 text-slate-500">{{ $r->created_at->format('M d, Y') }}</td>
                        <td class="px-5 py-3.5 text-right"><a href="{{ route('requests.show', $r) }}" class="font-semibold text-indigo-600 hover:underline">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-14 text-center text-slate-500">No requests match your search.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="mt-5 space-y-3 md:hidden">
        @forelse ($requests as $r)
            <a href="{{ route('requests.show', $r) }}" class="block rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/70">
                <div class="flex items-start justify-between gap-2">
                    <p class="font-semibold text-slate-900">{{ $r->subject }}</p>
                    <x-status-badge :status="$r->status" />
                </div>
                <p class="mt-1 text-xs text-slate-500">{{ $r->reference_no }} &middot; {{ $r->category->name }}</p>
                <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                    <x-priority-badge :priority="$r->priority" /><span>{{ $r->created_at->format('M d, Y') }}</span>
                </div>
            </a>
        @empty
            <p class="rounded-2xl bg-white p-8 text-center text-sm text-slate-500 ring-1 ring-slate-200/70">No requests match your search.</p>
        @endforelse
    </div>

    <div class="mt-5">{{ $requests->links() }}</div>

    @if (auth()->user()->isStudent())
        <a href="{{ route('requests.create') }}" class="fixed bottom-6 right-6 flex h-14 w-14 items-center justify-center rounded-full bg-indigo-600 text-white shadow-xl shadow-indigo-300 hover:bg-indigo-700 md:hidden" aria-label="New request"><x-icon name="plus" class="h-6 w-6" /></a>
    @endif
</x-app-layout>
