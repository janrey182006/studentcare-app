<x-app-layout>
    <x-slot name="header">Edit User</x-slot>
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mx-auto max-w-xl space-y-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        @csrf @method('PUT')
        <div><p class="text-lg font-bold text-slate-900">{{ $user->name }}</p><p class="text-sm text-slate-500">{{ $user->email }}</p></div>
        <div>
            <x-input-label for="role" value="Role" />
            <select id="role" name="role" class="mt-1 block w-full rounded-lg border-slate-300 text-sm">
                @foreach (['student', 'staff', 'admin'] as $r)<option value="{{ $r }}" @selected(old('role', $user->role) === $r)>{{ ucfirst($r) }}</option>@endforeach
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-1" />
        </div>
        <label class="flex items-center gap-2 text-sm text-slate-700">
            <input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active)) class="rounded border-slate-300 text-indigo-600"> Account is active
        </label>
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600">Cancel</a>
            <button class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Save</button>
        </div>
    </form>
</x-app-layout>
