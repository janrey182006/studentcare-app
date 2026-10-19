{{-- Shared by create + edit. old() keeps what the user typed after a validation error. --}}
@php
    $field = 'mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500';
@endphp
<div class="grid gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <x-input-label for="service_category_id" value="Service needed *" />
        <select id="service_category_id" name="service_category_id" required class="{{ $field }}">
            <option value="">Choose a service...</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" @selected(old('service_category_id', $serviceRequest->service_category_id) == $c->id)>{{ $c->name }} ({{ $c->office }})</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('service_category_id')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="subject" value="Subject *" />
        <input id="subject" name="subject" maxlength="120" required value="{{ old('subject', $serviceRequest->subject) }}" placeholder="Short title of your concern" class="{{ $field }}">
        <x-input-error :messages="$errors->get('subject')" class="mt-2" />
    </div>

    <div class="sm:col-span-2" x-data="{ count: {{ strlen(old('description', $serviceRequest->description ?? '')) }} }">
        <x-input-label for="description" value="Details *" />
        <textarea id="description" name="description" rows="6" maxlength="2000" required x-on:input="count = $event.target.value.length" placeholder="Explain what you need. Include dates, names, or document types if relevant." class="{{ $field }}">{{ old('description', $serviceRequest->description) }}</textarea>
        <div class="mt-1 flex justify-between text-xs text-slate-400"><span>Minimum 20 characters</span><span><span x-text="count"></span>/2000</span></div>
        <x-input-error :messages="$errors->get('description')" class="mt-1" />
    </div>

    <div>
        <x-input-label value="Priority *" />
        <div class="mt-2 grid grid-cols-4 gap-2">
            @foreach (\App\Models\ServiceRequest::PRIORITIES as $key => $label)
                <label class="cursor-pointer">
                    <input type="radio" name="priority" value="{{ $key }}" class="peer sr-only" @checked(old('priority', $serviceRequest->priority ?? 'normal') === $key)>
                    <span class="block rounded-lg border border-slate-200 py-2 text-center text-xs font-semibold text-slate-600 transition peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700">{{ $label }}</span>
                </label>
            @endforeach
        </div>
        <x-input-error :messages="$errors->get('priority')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="preferred_date" value="Preferred date (optional)" />
        <input id="preferred_date" type="date" name="preferred_date" min="{{ now()->toDateString() }}" value="{{ old('preferred_date', optional($serviceRequest->preferred_date)->toDateString()) }}" class="{{ $field }}">
        <x-input-error :messages="$errors->get('preferred_date')" class="mt-2" />
    </div>

    <div class="sm:col-span-2">
        <x-input-label for="attachment" value="Attachment (optional, PDF/JPG/PNG up to 2 MB)" />
        <input id="attachment" type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full rounded-lg border border-dashed border-slate-300 bg-slate-50 p-3 text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-700">
        @if ($serviceRequest->attachment_path)<p class="mt-1 text-xs text-slate-500">A file is already attached. Uploading a new one replaces it.</p>@endif
        <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
    </div>
</div>
