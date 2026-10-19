@props(['priority'])
@php
$styles = [
    'low'    => 'bg-slate-100 text-slate-600',
    'normal' => 'bg-blue-50 text-blue-700',
    'high'   => 'bg-orange-50 text-orange-700',
    'urgent' => 'bg-red-100 text-red-700',
];
@endphp
<span {{ $attributes->merge(['class' => 'inline-block rounded-md px-2 py-0.5 text-xs font-semibold ' . ($styles[$priority] ?? $styles['normal'])]) }}>
    {{ ucfirst($priority) }}
</span>
