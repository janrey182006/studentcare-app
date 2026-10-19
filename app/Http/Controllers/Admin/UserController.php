<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->q, fn ($q, $t) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$t}%")->orWhere('email', 'like', "%{$t}%")->orWhere('student_id', 'like', "%{$t}%")))
            ->when($request->role, fn ($q, $r) => $q->where('role', $r))
            ->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', Rule::in(['student', 'staff', 'admin'])],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        // Safety: an admin cannot lock themselves out
        if ($user->is($request->user()) && ($data['role'] !== 'admin' || ! $data['is_active'])) {
            return back()->with('error', 'You cannot remove your own admin access or deactivate yourself.');
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', "{$user->name} updated.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'You cannot delete your own account here.');
        }

        $user->delete();

        return back()->with('success', 'User deleted.');
    }
}
