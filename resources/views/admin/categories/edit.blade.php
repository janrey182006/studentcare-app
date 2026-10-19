<x-app-layout>
    <x-slot name="header">Edit Service</x-slot>
    @php $field = 'mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500'; @endphp
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="mx-auto max-w-xl space-y-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
        @csrf @method('PUT')
        <div><x-input-label for="name" value="Name *" /><input id="name" name="name" value="{{ old('name', $category->name) }}" required class="{{ $field }}"><x-input-error :messages="$errors->get('name')" class="mt-1" /></div>
        <div><x-input-label for="office" value="Office" /><input id="office" name="office" value="{{ old('office', $category->office) }}" class="{{ $field }}"></div>
        <div><x-input-label for="description" value="Description" /><textarea id="description" name="description" rows="3" class="{{ $field }}">{{ old('description', $category->description) }}</textarea></div>
        <label class="flex items-center gap-2 text-sm text-slate-700">
            <input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active)) class="rounded border-slate-300 text-indigo-600"> Active (students can choose this service)
        </label>
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.categories.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600">Cancel</a>
            <button class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Save</button>
        </div>
    </form>
</x-app-layout>
