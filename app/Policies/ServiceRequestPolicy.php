<?php

namespace App\Policies;

use App\Models\ServiceRequest;
use App\Models\User;

class ServiceRequestPolicy
{
    /** Staff/admin can see everything; students only their own. */
    public function view(User $user, ServiceRequest $request): bool
    {
        return $user->isStaff() || $request->user_id === $user->id;
    }

    /** Only the owner can edit, and only while the request is still pending. */
    public function update(User $user, ServiceRequest $request): bool
    {
        return $request->user_id === $user->id && $request->status === 'pending';
    }

    /** Admin can delete anything; owners can delete their own pending request. */
    public function delete(User $user, ServiceRequest $request): bool
    {
        return $user->isAdmin() || ($request->user_id === $user->id && $request->status === 'pending');
    }

    public function cancel(User $user, ServiceRequest $request): bool
    {
        return $request->user_id === $user->id && in_array($request->status, ['pending', 'in_review'], true);
    }

    public function rate(User $user, ServiceRequest $request): bool
    {
        return $request->user_id === $user->id && $request->status === 'resolved' && $request->rating === null;
    }

    /** Changing status / assignment is a staff action. */
    public function manage(User $user, ServiceRequest $request): bool
    {
        return $user->isStaff();
    }
}
