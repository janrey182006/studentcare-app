<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        // Students only see their own numbers; staff/admin see everything.
        $base = ServiceRequest::query()->when($user->isStudent(), fn ($q) => $q->where('user_id', $user->id));

        $byStatus = (clone $base)->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status');

        $stats = [
            'total'    => (clone $base)->count(),
            'pending'  => $byStatus['pending'] ?? 0,
            'active'   => ($byStatus['in_review'] ?? 0) + ($byStatus['in_progress'] ?? 0),
            'resolved' => $byStatus['resolved'] ?? 0,
            'rating'   => round((float) (clone $base)->whereNotNull('rating')->avg('rating'), 1),
        ];

        // Last 6 months (grouped in PHP so it works on MySQL and SQLite alike)
        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i)->startOfMonth());
        $monthly = (clone $base)->where('created_at', '>=', $months->first())
            ->get(['created_at'])
            ->groupBy(fn ($r) => $r->created_at->format('Y-m'))
            ->map->count();

        $charts = [
            'monthLabels'  => $months->map(fn ($m) => $m->format('M Y'))->values(),
            'monthData'    => $months->map(fn ($m) => $monthly[$m->format('Y-m')] ?? 0)->values(),
            'statusLabels' => collect(ServiceRequest::STATUSES)->values(),
            'statusData'   => collect(ServiceRequest::STATUSES)->keys()->map(fn ($k) => $byStatus[$k] ?? 0)->values(),
        ];

        if ($user->isStaff()) {
            $cats = ServiceCategory::withCount('requests')->orderByDesc('requests_count')->get();
            $charts['catLabels'] = $cats->pluck('name');
            $charts['catData']   = $cats->pluck('requests_count');
        }

        return view('dashboard', [
            'stats'  => $stats,
            'charts' => $charts,
            'recent' => (clone $base)->with(['category', 'user'])->latest()->take(6)->get(),
        ]);
    }
}
