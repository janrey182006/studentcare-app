<x-guest-layout>
    <h1 class="text-2xl font-extrabold text-slate-900">Create your account</h1>
    <p class="mt-1 text-sm text-slate-500">Use your real name and student ID so staff can find your records.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <x-input-label for="name" value="Full name" />
            <x-text-input id="name" class="mt-1 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="student_id" value="Student ID" />
                <x-text-input id="student_id" class="mt-1 block w-full" type="text" name="student_id" :value="old('student_id')" required placeholder="2026-0001" />
                <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="course" value="Course / Program" />
                <x-text-input id="course" class="mt-1 block w-full" type="text" name="course" :value="old('course')" required placeholder="BS Information Technology" />
                <x-input-error :messages="$errors->get('course')" class="mt-2" />
            </div>
        </div>
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="password" value="Password" />
                <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="password_confirmation" value="Confirm password" />
                <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>
        </div>

        <x-recaptcha />

        <button class="w-full rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 py-3 font-semibold text-white shadow-lg shadow-indigo-200 hover:opacity-95">Create account</button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600">Already registered? <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:underline">Log in</a></p>
</x-guest-layout>
