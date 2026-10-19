<x-app-layout>
    <x-slot name="header">Users</x-slot>
    <form method="GET" class="flex flex-wrap gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/70">
        <input name="q" value="{{ request('q') }}" placeholder="Search name, email, student ID" class="min-w-[200px] flex-1 rounded-lg border-slate-300 text-sm">
        <select name="role" class="rounded-lg border-slate-300 text-sm">
            <option value="">All roles</option>
            @foreach (['student', 'staff', 'admin'] as $r)<option value="{{ $r }}" @selected(request('role') === $r)>{{ ucfirst($r) }}</option>@endforeach
        </select>
        <button class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Search</button>
    </form>

    <div class="mt-5 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr><th class="px-5 py-3">User</th><th class="px-5 py-3">Role</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Actions</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($users as $u)
                        <tr>
                            <td class="px-5 py-3"><p class="font-semibold text-slate-900">{{ $u->name }}</p><p class="text-xs text-slate-500">{{ $u->email }}@if($u->student_id) &middot; {{ $u->student_id }}@endif</p></td>
                            <td class="px-5 py-3"><span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold capitalize text-indigo-700">{{ $u->role }}</span></td>
                            <td class="px-5 py-3"><span class="text-xs font-semibold {{ $u->is_active ? 'text-emerald-600' : 'text-slate-400' }}">{{ $u->is_active ? 'Active' : 'Deactivated' }}</span></td>
                            <td class="px-5 py-3">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.users.edit', $u) }}" class="font-semibold text-indigo-600 hover:underline">Edit</a>
                                    @if (! $u->is(auth()->user()))
                                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Delete this user and all of their requests?')">
                                            @csrf @method('DELETE')<button class="font-semibold text-rose-600 hover:underline">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-5">{{ $users->links() }}</div>
</x-app-layout>
