<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Lista Wpisów Czasu Pracy') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 dark:bg-green-700 text-green-700 dark:text-green-100 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-100 dark:bg-red-700 text-red-700 dark:text-red-100 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    @can('create-work-entry')
                        <div class="mb-4">
                            <a href="{{ route('work-entries.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('Dodaj Nowy Wpis') }}
                            </a>
                        </div>
                    @endcan

                    <form method="GET" action="{{ route('work-entries.index') }}" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div>
                                <x-input-label for="date_from" :value="__('Data od')" />
                                <x-text-input id="date_from" class="block mt-1 w-full" type="date" name="date_from" :value="request('date_from')" />
                            </div>
                            <div>
                                <x-input-label for="date_to" :value="__('Data do')" />
                                <x-text-input id="date_to" class="block mt-1 w-full" type="date" name="date_to" :value="request('date_to')" />
                            </div>

                            @if (Auth::user()->role === App\Models\User::ROLE_ADMIN || Auth::user()->role === App\Models\User::ROLE_SUPERVISOR)
                                <div>
                                    <x-input-label for="employee_id" :value="__('Pracownik')" />
                                    <select id="employee_id" name="employee_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">{{ __('-- Wszyscy Pracownicy --') }}</option>
                                        @foreach ($employees_for_filter as $employee)
                                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <div class="flex space-x-2">
                                <x-primary-button class="w-full md:w-auto justify-center">
                                    {{ __('Filtruj') }}
                                </x-primary-button>
                                <a href="{{ route('work-entries.index') }}" class="inline-flex items-center ml-3 px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 w-full md:w-auto justify-center">
                                    {{ __('Wyczyść') }}
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    @if (Auth::user()->role === App\Models\User::ROLE_ADMIN || Auth::user()->role === App\Models\User::ROLE_SUPERVISOR)
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Pracownik
                                        </th>
                                    @endif
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Data
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Godziny
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Wprowadził
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Akcje
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($workEntries as $entry)
                                    <tr>
                                        @if (Auth::user()->role === App\Models\User::ROLE_ADMIN || Auth::user()->role === App\Models\User::ROLE_SUPERVISOR)
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $entry->employee->name }}
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $entry->date_of_work->format('Y-m-d') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ number_format($entry->hours_worked, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $entry->enteredBy->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('work-entries.show', $entry) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200 mr-3">Szczegóły</a>
                                            @can('update-work-entry', $entry)
                                                <a href="{{ route('work-entries.edit', $entry) }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-200 mr-3">Edytuj</a>
                                            @endcan
                                            @can('delete-work-entry', $entry)
                                                <form action="{{ route('work-entries.destroy', $entry) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-200" onclick="return confirm('Czy na pewno chcesz usunąć ten wpis?')">Usuń</button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ (Auth::user()->role === App\Models\User::ROLE_ADMIN || Auth::user()->role === App\Models\User::ROLE_SUPERVISOR) ? 5 : 4 }}" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                                            Brak wpisów czasu pracy spełniających kryteria.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $workEntries->appends(request()->query())->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>