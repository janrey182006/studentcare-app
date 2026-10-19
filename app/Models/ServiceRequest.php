<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ServiceRequest extends Model
{
    public const STATUSES = [
        'pending'     => 'Pending',
        'in_review'   => 'In Review',
        'in_progress' => 'In Progress',
        'resolved'    => 'Resolved',
        'rejected'    => 'Rejected',
        'cancelled'   => 'Cancelled',
    ];

    public const PRIORITIES = [
        'low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent',
    ];

    protected $fillable = [
        'reference_no', 'user_id', 'service_category_id', 'assigned_to', 'subject', 'description',
        'priority', 'status', 'preferred_date', 'attachment_path', 'rating', 'feedback', 'resolved_at',
    ];

    protected function casts(): array
    {
        return ['preferred_date' => 'date', 'resolved_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        // Auto-generate a readable tracking number, e.g. SSR-2026-K7QX2M
        static::creating(function (self $request) {
            if (! $request->reference_no) {
                $request->reference_no = 'SSR-' . now()->format('Y') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(RequestUpdate::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function isFinal(): bool
    {
        return in_array($this->status, ['resolved', 'rejected', 'cancelled'], true);
    }

    /** Keyword search across reference number, subject and description. */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, function (Builder $q) use ($term) {
            $q->where(function (Builder $q) use ($term) {
                $q->where('reference_no', 'like', "%{$term}%")
                  ->orWhere('subject', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
            });
        });
    }
}
