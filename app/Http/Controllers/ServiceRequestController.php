<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Notifications\RequestStatusChanged;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ServiceRequestController extends Controller
{
    /** List + search + filter. Students see only their own rows. */
    public function index(Request $request)
    {
        $user = $request->user();

        $requests = ServiceRequest::with(['category', 'user', 'assignee'])
            ->when($user->isStudent(), fn ($q) => $q->where('user_id', $user->id))
            ->search($request->q)
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when($request->category, fn ($q, $v) => $q->where('service_category_id', $v))
            ->when($request->priority, fn ($q, $v) => $q->where('priority', $v))
            ->when($request->from, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->to, fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('requests.index', [
            'requests'   => $requests,
            'categories' => ServiceCategory::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('requests.create', [
            'categories' => ServiceCategory::where('is_active', true)->orderBy('name')->get(),
            'serviceRequest' => new ServiceRequest(['priority' => 'normal']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        if ($request->hasFile('attachment')) {
            // Stored on the PRIVATE disk; downloaded only through an authorised route.
            $data['attachment_path'] = $request->file('attachment')->store('attachments', 'local');
        }
        unset($data['attachment']);

        $serviceRequest = $request->user()->serviceRequests()->create($data);

        $serviceRequest->updates()->create([
            'user_id' => $request->user()->id,
            'status'  => 'pending',
            'message' => 'Request submitted.',
        ]);

        return redirect()->route('requests.show', $serviceRequest)
            ->with('success', "Request submitted! Your tracking number is {$serviceRequest->reference_no}.");
    }

    public function show(Request $request, ServiceRequest $serviceRequest)
    {
        Gate::authorize('view', $serviceRequest);

        $serviceRequest->load(['category', 'user', 'assignee']);

        $updates = $serviceRequest->updates()
            ->with('user')
            ->when(! $request->user()->isStaff(), fn ($q) => $q->where('is_internal', false))
            ->latest()
            ->get();

        return view('requests.show', [
            'serviceRequest' => $serviceRequest,
            'updates'        => $updates,
            'staffMembers'   => User::whereIn('role', ['staff', 'admin'])->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function edit(ServiceRequest $serviceRequest)
    {
        Gate::authorize('update', $serviceRequest);

        return view('requests.edit', [
            'serviceRequest' => $serviceRequest,
            'categories' => ServiceCategory::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        Gate::authorize('update', $serviceRequest);

        $data = $this->validated($request);

        if ($request->hasFile('attachment')) {
            if ($serviceRequest->attachment_path) {
                Storage::disk('local')->delete($serviceRequest->attachment_path);
            }
            $data['attachment_path'] = $request->file('attachment')->store('attachments', 'local');
        }
        unset($data['attachment']);

        $serviceRequest->update($data);

        return redirect()->route('requests.show', $serviceRequest)->with('success', 'Request updated.');
    }

    public function destroy(ServiceRequest $serviceRequest): RedirectResponse
    {
        Gate::authorize('delete', $serviceRequest);

        if ($serviceRequest->attachment_path) {
            Storage::disk('local')->delete($serviceRequest->attachment_path);
        }
        $serviceRequest->delete();

        return redirect()->route('requests.index')->with('success', 'Request deleted.');
    }

    /** Staff action: change status, assign handler, add a message. */
    public function updateStatus(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        Gate::authorize('manage', $serviceRequest);

        $data = $request->validate([
            'status'      => ['required', Rule::in(array_keys(ServiceRequest::STATUSES))],
            'assigned_to' => ['nullable', Rule::exists('users', 'id')->whereIn('role', ['staff', 'admin'])],
            'message'     => ['required_if:status,rejected', 'nullable', 'string', 'max:1000'],
        ], [
            'message.required_if' => 'Please give the student a reason when rejecting a request.',
        ]);

        $changed = $serviceRequest->status !== $data['status'];

        $serviceRequest->update([
            'status'      => $data['status'],
            'assigned_to' => $data['assigned_to'] ?? $serviceRequest->assigned_to,
            'resolved_at' => in_array($data['status'], ['resolved', 'rejected'], true) ? ($serviceRequest->resolved_at ?? now()) : null,
        ]);

        $serviceRequest->updates()->create([
            'user_id' => $request->user()->id,
            'status'  => $data['status'],
            'message' => $data['message'] ?: 'Status changed to ' . $serviceRequest->statusLabel() . '.',
        ]);

        if ($changed) {
            // rescue() = never let a mail problem break the page
            rescue(fn () => $serviceRequest->user->notify(new RequestStatusChanged($serviceRequest)));
        }

        return back()->with('success', 'Request updated to ' . $serviceRequest->statusLabel() . '.');
    }

    /** Add a comment (students) or comment / internal note (staff). */
    public function comment(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        Gate::authorize('view', $serviceRequest);

        $data = $request->validate(['message' => ['required', 'string', 'max:1000']]);

        $serviceRequest->updates()->create([
            'user_id'     => $request->user()->id,
            'message'     => $data['message'],
            'is_internal' => $request->user()->isStaff() && $request->boolean('is_internal'),
        ]);

        return back()->with('success', 'Comment added.');
    }

    public function cancel(ServiceRequest $serviceRequest): RedirectResponse
    {
        Gate::authorize('cancel', $serviceRequest);

        $serviceRequest->update(['status' => 'cancelled', 'resolved_at' => now()]);
        $serviceRequest->updates()->create([
            'user_id' => auth()->id(), 'status' => 'cancelled', 'message' => 'Request cancelled by the student.',
        ]);

        return back()->with('success', 'Request cancelled.');
    }

    public function rate(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        Gate::authorize('rate', $serviceRequest);

        $data = $request->validate([
            'rating'   => ['required', 'integer', 'between:1,5'],
            'feedback' => ['nullable', 'string', 'max:500'],
        ]);

        $serviceRequest->update($data);

        return back()->with('success', 'Thanks for your feedback!');
    }

    public function attachment(ServiceRequest $serviceRequest)
    {
        Gate::authorize('view', $serviceRequest);
        abort_unless($serviceRequest->attachment_path && Storage::disk('local')->exists($serviceRequest->attachment_path), 404);

        return Storage::disk('local')->download($serviceRequest->attachment_path);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'service_category_id' => ['required', Rule::exists('service_categories', 'id')->where('is_active', true)],
            'subject'        => ['required', 'string', 'max:120'],
            'description'    => ['required', 'string', 'min:20', 'max:2000'],
            'priority'       => ['required', Rule::in(array_keys(ServiceRequest::PRIORITIES))],
            'preferred_date' => ['nullable', 'date', 'after_or_equal:today'],
            'attachment'     => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ], [
            'service_category_id.required' => 'Please choose the service you need.',
            'service_category_id.exists'   => 'That service is not available.',
            'description.min'              => 'Please describe your request in at least 20 characters so staff can help you faster.',
            'attachment.max'               => 'The attachment must not be larger than 2 MB.',
            'attachment.mimes'             => 'Only PDF, JPG, or PNG files are allowed.',
        ]);
    }
}
