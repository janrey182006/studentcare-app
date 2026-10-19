<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    <div class="mb-6 flex flex-col gap-4 rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 p-6 text-white shadow-lg sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold sm:text-2xl">Hello, {{ explode(' ', auth()->user()->name)[0] }}!</h2>
            <p class="mt-1 text-sm text-indigo-100">
                @if (auth()->user()->isStudent()) Track your requests or submit a new one. @else There are {{ $stats['pending'] }} pending request(s) waiting for review. @endif
            </p>
        </div>
        @if (auth()->user()->isStudent())
            <a href="{{ route('requests.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-indigo-700 shadow hover:bg-indigo-50"><x-icon name="plus" class="h-4 w-4" /> New request</a>
        @else
            <a href="{{ route('requests.index', ['status' => 'pending']) }}" class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-indigo-700 shadow hover:bg-indigo-50">Review pending</a>
        @endif
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat-card label="Total requests" :value="$stats['total']" tone="indigo" />
        <x-stat-card label="Pending" :value="$stats['pending']" tone="amber" />
        <x-stat-card label="In progress" :value="$stats['active']" tone="sky" />
        <x-stat-card label="Resolved" :value="$stats['resolved']" tone="emerald" :hint="$stats['rating'] ? 'Avg. rating ' . $stats['rating'] . ' / 5' : null" />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70 lg:col-span-2">
            <h3 class="font-bold text-slate-900">Requests per month</h3>
            <p class="text-xs text-slate-400">Last 6 months</p>
            <div class="mt-4 h-64"><canvas id="monthlyChart"></canvas></div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
            <h3 class="font-bold text-slate-900">By status</h3>
            <div class="mt-4 h-64"><canvas id="statusChart"></canvas></div>
        </div>
    </div>

    @if (auth()->user()->isStaff())
        <div class="mt-6 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
            <h3 class="font-bold text-slate-900">Requests by service</h3>
            <div class="mt-4 h-72"><canvas id="categoryChart"></canvas></div>
        </div>
    @endif

    <div class="mt-6 rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70">
        <div class="flex items-center justify-between px-5 py-4">
            <h3 class="font-bold text-slate-900">Recent requests</h3>
            <a href="{{ route('requests.index') }}" class="text-sm font-semibold text-indigo-600 hover:underline">View all</a>
        </div>
        <ul class="divide-y divide-slate-100">
            @forelse ($recent as $r)
                <li>
                    <a href="{{ route('requests.show', $r) }}" class="flex flex-col gap-2 px-5 py-3.5 hover:bg-slate-50 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-slate-800">{{ $r->subject }}</p>
                            <p class="text-xs text-slate-500">{{ $r->reference_no }} &middot; {{ $r->category->name }}@if(auth()->user()->isStaff()) &middot; {{ $r->user->name }}@endif &middot; {{ $r->created_at->diffForHumans() }}</p>
                        </div>
                        <x-status-badge :status="$r->status" />
                    </a>
                </li>
            @empty
                <li class="px-5 py-10 text-center text-sm text-slate-500">No requests yet.</li>
            @endforelse
        </ul>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
        <script>
            const charts = @json($charts);
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = '#64748b';

            new Chart(document.getElementById('monthlyChart'), {
                type: 'bar',
                data: { labels: charts.monthLabels, datasets: [{ label: 'Requests', data: charts.monthData, backgroundColor: '#6366f1', borderRadius: 8, maxBarThickness: 48 }] },
                options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { grid: { display: false } } } }
            });

            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: { labels: charts.statusLabels, datasets: [{ data: charts.statusData, backgroundColor: ['#f59e0b', '#0ea5e9', '#6366f1', '#10b981', '#f43f5e', '#94a3b8'], borderWidth: 0 }] },
                options: { maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true } } } }
            });

            const cat = document.getElementById('categoryChart');
            if (cat) {
                new Chart(cat, {
                    type: 'bar',
                    data: { labels: charts.catLabels, datasets: [{ label: 'Requests', data: charts.catData, backgroundColor: '#8b5cf6', borderRadius: 6 }] },
                    options: { indexAxis: 'y', maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { precision: 0 } }, y: { grid: { display: false } } } }
                });
            }
        </script>
    @endpush
</x-app-layout>
