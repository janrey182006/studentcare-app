<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $base = $this->filtered($request);

        $total = (clone $base)->count();
        $resolved = (clone $base)->whereNotNull('resolved_at')->where('status', 'resolved')->get(['created_at', 'resolved_at']);

        $avgHours = $resolved->isEmpty()
            ? null
            : round($resolved->avg(fn ($r) => abs($r->created_at->diffInHours($r->resolved_at))), 1);

        $byCategory = ServiceCategory::withCount(['requests as total' => fn ($q) => $this->applyDates($q, $request)])
            ->orderByDesc('total')->get();

        $byStatus = (clone $base)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('reports.index', [
            'total'      => $total,
            'avgHours'   => $avgHours,
            'avgRating'  => round((float) (clone $base)->whereNotNull('rating')->avg('rating'), 1),
            'byCategory' => $byCategory,
            'byStatus'   => $byStatus,
            'from'       => $request->from,
            'to'         => $request->to,
        ]);
    }

    public function export(Request $request)
    {
        $rows = $this->filtered($request)->with(['user', 'category', 'assignee'])->orderBy('created_at')->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Reference', 'Student', 'Category', 'Subject', 'Priority', 'Status', 'Assigned To', 'Submitted', 'Resolved'], ',', '"', '');

            foreach ($rows as $r) {
                fputcsv($out, array_map([$this, 'safe'], [
                    $r->reference_no, $r->user->name, $r->category->name, $r->subject,
                    ucfirst($r->priority), $r->statusLabel(), $r->assignee?->name ?? '-',
                    $r->created_at->format('Y-m-d H:i'), $r->resolved_at?->format('Y-m-d H:i') ?? '-',
                ]), ',', '"', '');
            }
            fclose($out);
        }, 'service-requests-' . now()->format('Ymd-His') . '.csv', ['Content-Type' => 'text/csv']);
    }

    private function filtered(Request $request): Builder
    {
        $request->validate(['from' => ['nullable', 'date'], 'to' => ['nullable', 'date', 'after_or_equal:from']]);

        return $this->applyDates(ServiceRequest::query(), $request);
    }

    private function applyDates($query, Request $request)
    {
        return $query
            ->when($request->from, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->to, fn ($q, $v) => $q->whereDate('created_at', '<=', $v));
    }

    /** Stop spreadsheet formula injection in exported CSV files. */
    private function safe(?string $value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@]/', $value) ? "'" . $value : $value;
    }
}
