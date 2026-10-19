<x-app-layout>
    <x-slot name="header">Edit Request</x-slot>
    <div class="mx-auto max-w-3xl rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70 sm:p-8">
        <h2 class="text-xl font-extrabold text-slate-900">Edit {{ $serviceRequest->reference_no }}</h2>
        <p class="mb-6 mt-1 text-sm text-slate-500">You can edit a request only while it is still pending.</p>
        <form method="POST" action="{{ route('requests.update', $serviceRequest) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('requests._form')
            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('requests.show', $serviceRequest) }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Cancel</a>
                <button class="rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-700">Save changes</button>
            </div>
        </form>
    </div>
</x-app-layout>
