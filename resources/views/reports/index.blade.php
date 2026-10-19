<x-app-layout>
    <x-slot name="header">Reports</x-slot>

    <form method="GET" class="flex flex-wrap items-end gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/70 print:hidden">
        <label class="text-xs font-semibold text-slate-500">From<input type="date" name="from" value="{{ $from }}" class="mt-1 block rounded-lg border-slate-300 text-sm"></label>
        <label class="text-xs font-semibold text-slate-500">To<input type="date" name="to" value="{{ $to }}" class="mt-1 block rounded-lg border-slate-300 text-sm"></label>
        <button class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Apply</button>
        <div class="ml-auto flex gap-2">
            <a href="{{ route('reports.export', request()->query()) }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Export CSV</a>
            <button type="button" onclick="window.print()" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Print / Save PDF</button>
        </div>
    </form>
    <x-input-error :messages="$errors->get('to')" class="mt-2" />

    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-stat-card label="Requests in range" :value="$total" tone="indigo" />
        <x-stat-card label="Avg. resolution time" :value="$avgHours !== null ? $avgHours . ' hrs' : 'N/A'" tone="sky" />
        <x-stat-card label="Avg. satisfaction" :value="$avgRating ? $avgRating . ' / 5' : 'N/A'" tone="emerald" />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70">
            <h3 class="px-5 py-4 font-bold text-slate-900">Requests by service</h3>
            <table class="min-w-full text-sm">
                <tbody class="divide-y divide-slate-100">
                    @foreach ($byCategory as $c)
                        <tr><td class="px-5 py-3">{{ $c->name }}</td><td class="px-5 py-3 text-right font-semibold">{{ $c->total }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70">
            <h3 class="px-5 py-4 font-bold text-slate-900">Requests by status</h3>
            <table class="min-w-full text-sm">
                <tbody class="divide-y divide-slate-100">
                    @foreach (\App\Models\ServiceRequest::STATUSES as $key => $label)
                        <tr><td class="px-5 py-3"><x-status-badge :status="$key" /></td><td class="px-5 py-3 text-right font-semibold">{{ $byStatus[$key] ?? 0 }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
