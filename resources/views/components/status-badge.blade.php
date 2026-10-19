@props(['status'])
@php
$styles = [
    'pending'     => 'bg-amber-50 text-amber-700 ring-amber-200',
    'in_review'   => 'bg-sky-50 text-sky-700 ring-sky-200',
    'in_progress' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
    'resolved'    => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    'rejected'    => 'bg-rose-50 text-rose-700 ring-rose-200',
    'cancelled'   => 'bg-slate-100 text-slate-600 ring-slate-200',
];
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset ' . ($styles[$status] ?? $styles['cancelled'])]) }}>
    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ \App\Models\ServiceRequest::STATUSES[$status] ?? $status }}
</span>
