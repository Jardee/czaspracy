<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dodaj Nowy Wpis Czasu Pracy') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('work-entries.store') }}">
                        @csrf

                        <div>
                            <x-input-label for="user_id" :value="__('Pracownik')" />
                            <select id="user_id" name="user_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                <option value="">{{ __('-- Wybierz pracownika --') }}</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('user_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }} ({{ $employee->email }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="date_of_work" :value="__('Data Pracy')" />
                            <x-text-input id="date_of_work" class="block mt-1 w-full" type="date" name="date_of_work" :value="old('date_of_work', now()->toDateString())" required />
                            <x-input-error :messages="$errors->get('date_of_work')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="hours_worked" :value="__('Przepracowane Godziny')" />
                            <x-text-input id="hours_worked" class="block mt-1 w-full" type="number" name="hours_worked" :value="old('hours_worked')" required step="0.01" min="0.01" max="24" />
                            <x-input-error :messages="$errors->get('hours_worked')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('work-entries.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 ms-4">
                                {{ __('Anuluj') }}
                            </a>
                            <x-primary-button class="ms-4">
                                {{ __('Zapisz Wpis') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>