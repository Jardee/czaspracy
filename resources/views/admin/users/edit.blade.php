<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edytuj Użytkownika') }}: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-100 dark:bg-red-700 text-red-700 dark:text-red-100 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PATCH')

                        <div>
                            <x-input-label for="name" :value="__('Imię i Nazwisko')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="role" :value="__('Rola')" />
                            <select id="role" name="role" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required {{ (Auth::id() === $user->id && $user->role === App\Models\User::ROLE_ADMIN && App\Models\User::where('role', App\Models\User::ROLE_ADMIN)->count() <= 1) ? 'disabled' : '' }}>
                                @foreach ($roles as $roleValue => $roleName)
                                    <option value="{{ $roleValue }}" {{ old('role', $user->role) == $roleValue ? 'selected' : '' }}>
                                        {{ $roleName }}
                                    </option>
                                @endforeach
                            </select>
                            @if (Auth::id() === $user->id && $user->role === App\Models\User::ROLE_ADMIN && App\Models\User::where('role', App\Models\User::ROLE_ADMIN)->count() <= 1)
                                <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">{{ __('Nie można zmienić roli jedynemu administratorowi.') }}</p>
                            @endif
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>
                        
                        <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Zmień hasło (pozostaw puste, jeśli nie chcesz zmieniać)') }}</p>
                            <div class="mt-4">
                                <x-input-label for="password" :value="__('Nowe Hasło')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="password_confirmation" :value="__('Potwierdź Nowe Hasło')" />
                                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>


                        <div class="flex items-center justify-end mt-6">
                             <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 ms-4">
                                {{ __('Anuluj') }}
                            </a>
                            <x-primary-button class="ms-4">
                                {{ __('Zaktualizuj Użytkownika') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>