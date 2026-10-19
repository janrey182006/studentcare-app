@props(['label', 'value', 'tone' => 'indigo', 'hint' => null])
@php
$tones = [
    'indigo'  => 'from-indigo-500 to-violet-500',
    'amber'   => 'from-amber-400 to-orange-500',
    'sky'     => 'from-sky-400 to-blue-500',
    'emerald' => 'from-emerald-400 to-teal-500',
];
@endphp
<div class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200/70">
    <div class="absolute -right-6 -top-6 h-20 w-20 rounded-full bg-gradient-to-br {{ $tones[$tone] ?? $tones['indigo'] }} opacity-15"></div>
    <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
    <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">{{ $value }}</p>
    @if ($hint)<p class="mt-1 text-xs text-slate-400">{{ $hint }}</p>@endif
</div>
