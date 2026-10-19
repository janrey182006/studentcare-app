@php $me = auth()->user(); $field = 'mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500'; @endphp
<x-app-layout>
    <x-slot name="header">{{ $serviceRequest->reference_no }}</x-slot>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- LEFT: details + conversation --}}
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900">{{ $serviceRequest->subject }}</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $serviceRequest->category->name }} &middot; {{ $serviceRequest->category->office }}</p>
                    </div>
                    <div class="flex items-center gap-2"><x-priority-badge :priority="$serviceRequest->priority" /><x-status-badge :status="$serviceRequest->status" /></div>
                </div>
                <p class="mt-5 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $serviceRequest->description }}</p>

                <dl class="mt-6 grid gap-4 border-t border-slate-100 pt-5 text-sm sm:grid-cols-3">
                    <div><dt class="text-xs font-semibold uppercase text-slate-400">Submitted by</dt><dd class="mt-1 font-medium">{{ $serviceRequest->user->name }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase text-slate-400">Submitted</dt><dd class="mt-1 font-medium">{{ $serviceRequest->created_at->format('M d, Y g:i A') }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase text-slate-400">Handled by</dt><dd class="mt-1 font-medium">{{ $serviceRequest->assignee?->name ?? 'Not yet assigned' }}</dd></div>
                    @if ($serviceRequest->preferred_date)
                        <div><dt class="text-xs font-semibold uppercase text-slate-400">Preferred date</dt><dd class="mt-1 font-medium">{{ $serviceRequest->preferred_date->format('M d, Y') }}</dd></div>
                    @endif
                    @if ($serviceRequest->attachment_path)
                        <div><dt class="text-xs font-semibold uppercase text-slate-400">Attachment</dt><dd class="mt-1"><a class="font-semibold text-indigo-600 hover:underline" href="{{ route('requests.attachment', $serviceRequest) }}">Download file</a></dd></div>
                    @endif
                </dl>

                <div class="mt-6 flex flex-wrap gap-2 border-t border-slate-100 pt-5">
                    <a href="{{ route('requests.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Back</a>
                    @can('update', $serviceRequest)
                        <a href="{{ route('requests.edit', $serviceRequest) }}" class="rounded-lg bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">Edit</a>
                    @endcan
                    @can('cancel', $serviceRequest)
                        <form method="POST" action="{{ route('requests.cancel', $serviceRequest) }}" onsubmit="return confirm('Cancel this request? This cannot be undone.')">
                            @csrf <button class="rounded-lg bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700 hover:bg-amber-100">Cancel request</button>
                        </form>
                    @endcan
                    @can('delete', $serviceRequest)
                        <button x-data x-on:click.prevent="$dispatch('open-modal', 'delete-request')" class="rounded-lg bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-100">Delete</button>
                    @endcan
                </div>
            </div>

            {{-- Rating (student, after resolved) --}}
            @can('rate', $serviceRequest)
                <form method="POST" action="{{ route('requests.rate', $serviceRequest) }}" class="rounded-2xl border border-emerald-200 bg-emerald-50/60 p-6">
                    @csrf
                    <h3 class="font-bold text-emerald-900">How was our service?</h3>
                    <div class="mt-3 flex gap-2">
                        @foreach (range(1, 5) as $n)
                            <label class="cursor-pointer">
                                <input type="radio" name="rating" value="{{ $n }}" class="peer sr-only" @checked(old('rating') == $n)>
                                <span class="flex h-11 w-11 items-center justify-center rounded-lg border border-slate-200 bg-white text-sm font-bold text-slate-500 peer-checked:border-amber-400 peer-checked:bg-amber-50 peer-checked:text-amber-600">{{ $n }}&#9733;</span>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('rating')" class="mt-2" />
                    <textarea name="feedback" rows="2" maxlength="500" placeholder="Optional comments" class="{{ $field }} mt-3">{{ old('feedback') }}</textarea>
                    <button class="mt-3 rounded-lg bg-emerald-600 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Send feedback</button>
                </form>
            @elseif ($serviceRequest->rating)
                <div class="rounded-2xl bg-white p-5 text-sm shadow-sm ring-1 ring-slate-200/70">
                    <p class="font-semibold text-slate-800">Rating: <span class="text-amber-500">{{ str_repeat('★', $serviceRequest->rating) }}</span><span class="text-slate-300">{{ str_repeat('★', 5 - $serviceRequest->rating) }}</span></p>
                    @if ($serviceRequest->feedback)<p class="mt-1 text-slate-600">"{{ $serviceRequest->feedback }}"</p>@endif
                </div>
            @endif

            {{-- Add comment --}}
            @unless ($serviceRequest->isFinal() && $me->isStudent())
                <form method="POST" action="{{ route('requests.comment', $serviceRequest) }}" class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                    @csrf
                    <h3 class="font-bold text-slate-900">Add a comment</h3>
                    <textarea name="message" rows="3" maxlength="1000" required placeholder="Write a message..." class="{{ $field }}">{{ old('message') }}</textarea>
                    <x-input-error :messages="$errors->get('message')" class="mt-2" />
                    <div class="mt-3 flex items-center justify-between">
                        @if ($me->isStaff())
                            <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="is_internal" value="1" class="rounded border-slate-300 text-indigo-600"> Internal note (hidden from student)</label>
                        @else <span></span> @endif
                        <button class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Post</button>
                    </div>
                </form>
            @endunless
        </div>

        {{-- RIGHT: staff panel + timeline --}}
        <div class="space-y-6">
            @can('manage', $serviceRequest)
                <form method="POST" action="{{ route('requests.status', $serviceRequest) }}" class="rounded-2xl bg-white p-6 shadow-sm ring-2 ring-indigo-100">
                    @csrf @method('PATCH')
                    <h3 class="font-bold text-slate-900">Update request</h3>
                    <div class="mt-4 space-y-3">
                        <div>
                            <x-input-label for="status" value="Status" />
                            <select id="status" name="status" class="{{ $field }}">
                                @foreach (\App\Models\ServiceRequest::STATUSES as $key => $label)
                                    <option value="{{ $key }}" @selected(old('status', $serviceRequest->status) === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="assigned_to" value="Assign to" />
                            <select id="assigned_to" name="assigned_to" class="{{ $field }}">
                                <option value="">Unassigned</option>
                                @foreach ($staffMembers as $s)
                                    <option value="{{ $s->id }}" @selected(old('assigned_to', $serviceRequest->assigned_to) == $s->id)>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="status_message" value="Message to student" />
                            <textarea id="status_message" name="message" rows="3" maxlength="1000" class="{{ $field }}" placeholder="Required when rejecting">{{ old('message') }}</textarea>
                            <x-input-error :messages="$errors->get('message')" class="mt-1" />
                        </div>
                        <button class="w-full rounded-lg bg-indigo-600 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Save update</button>
                    </div>
                </form>
            @endcan

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
                <h3 class="font-bold text-slate-900">Activity timeline</h3>
                <ol class="mt-5 space-y-5 border-l-2 border-indigo-100 pl-5">
                    @foreach ($updates as $u)
                        <li class="relative">
                            <span class="absolute -left-[27px] top-1 h-3 w-3 rounded-full ring-4 ring-white {{ $u->is_internal ? 'bg-amber-400' : 'bg-indigo-500' }}"></span>
                            @if ($u->status)<x-status-badge :status="$u->status" class="mb-1" />@endif
                            @if ($u->is_internal)<span class="mb-1 ml-1 rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-bold uppercase text-amber-700">Internal</span>@endif
                            <p class="text-sm text-slate-700">{{ $u->message }}</p>
                            <p class="mt-0.5 text-xs text-slate-400">{{ $u->user->name }} &middot; {{ $u->created_at->format('M d, g:i A') }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>

    {{-- Delete confirmation modal (Breeze x-modal) --}}
    @can('delete', $serviceRequest)
        <x-modal name="delete-request" maxWidth="md">
            <form method="POST" action="{{ route('requests.destroy', $serviceRequest) }}" class="p-6">
                @csrf @method('DELETE')
                <h2 class="text-lg font-bold text-slate-900">Delete this request?</h2>
                <p class="mt-2 text-sm text-slate-600">{{ $serviceRequest->reference_no }} and its history will be permanently removed. This cannot be undone.</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close')" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600">Keep it</button>
                    <button class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Yes, delete</button>
                </div>
            </form>
        </x-modal>
    @endcan
</x-app-layout>
