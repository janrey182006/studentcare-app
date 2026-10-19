<x-app-layout>
    <x-slot name="header">Service Categories</x-slot>
    @php $field = 'mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500'; @endphp

    <div class="grid gap-6 lg:grid-cols-3">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="h-fit rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
            @csrf
            <h3 class="font-bold text-slate-900">Add a service</h3>
            <div class="mt-4 space-y-3">
                <div><x-input-label for="name" value="Name *" /><input id="name" name="name" value="{{ old('name') }}" required class="{{ $field }}"><x-input-error :messages="$errors->get('name')" class="mt-1" /></div>
                <div><x-input-label for="office" value="Office" /><input id="office" name="office" value="{{ old('office') }}" class="{{ $field }}"><x-input-error :messages="$errors->get('office')" class="mt-1" /></div>
                <div><x-input-label for="description" value="Description" /><textarea id="description" name="description" rows="3" class="{{ $field }}">{{ old('description') }}</textarea><x-input-error :messages="$errors->get('description')" class="mt-1" /></div>
                <button class="w-full rounded-lg bg-indigo-600 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Add service</button>
            </div>
        </form>

        <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70 lg:col-span-2">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr><th class="px-5 py-3">Service</th><th class="px-5 py-3">Requests</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Actions</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($categories as $c)
                            <tr>
                                <td class="px-5 py-3"><p class="font-semibold text-slate-900">{{ $c->name }}</p><p class="text-xs text-slate-500">{{ $c->office }}</p></td>
                                <td class="px-5 py-3">{{ $c->requests_count }}</td>
                                <td class="px-5 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $c->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $c->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td class="px-5 py-3">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('admin.categories.edit', $c) }}" class="font-semibold text-indigo-600 hover:underline">Edit</a>
                                        <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" onsubmit="return confirm('Delete {{ addslashes($c->name) }}?')">
                                            @csrf @method('DELETE')<button class="font-semibold text-rose-600 hover:underline">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
