<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Raporty Godzin Pracy') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="GET" action="{{ route('reports.index') }}" class="mb-6">
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
                                    <x-input-label for="employee_id" :value="__('Pracownik (opcjonalnie)')" />
                                    <select id="employee_id" name="employee_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="">{{ __('-- Wszyscy Pracownicy --') }}</option>
                                        @dump($employees_for_filter)
                                        @if (isset($employees_for_filter))
                                            @foreach ($employees_for_filter as $employee)
                                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            @endif

                            <div class="flex space-x-2">
                                <x-primary-button name="action" value="generate" class="w-full md:w-auto justify-center">
                                    {{ __('Generuj Raport') }}
                                </x-primary-button>
                                <a href="{{ route('reports.index') }}" class="inline-flex items-center ml-3 px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 w-full md:w-auto justify-center">
                                    {{ __('Wyczyść') }}
                                </a>
                            </div>
                        </div>
                    </form>

                    @if (isset($reportData) && $reportData->isNotEmpty())
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            {{ __('Podsumowanie Godzin Pracy') }}
                            @if(request('date_from'))
                                {{ __('od') }} {{ request('date_from') }}
                            @endif
                            @if(request('date_to'))
                                {{ __('do') }} {{ request('date_to') }}
                            @endif
                            @if (isset($reportData) && (Auth::user()->role === App\Models\User::ROLE_ADMIN || Auth::user()->role === App\Models\User::ROLE_SUPERVISOR) && request('employee_id') && $reportData->first() && property_exists($reportData->first(), 'employee_name') )
                                {{ __('dla') }} {{ $reportData->first()->employee_name }}
                            @endif
                        </h3>

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
                                            Suma Przepracowanych Godzin
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($reportData as $data)
                                        <tr>
                                            @if (Auth::user()->role === App\Models\User::ROLE_ADMIN || Auth::user()->role === App\Models\User::ROLE_SUPERVISOR)
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $data->employee_name ?? __('Brak nazwy') }}
                                                </td>
                                            @endif
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ number_format($data->total_hours, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach

                                    @if (isset($overallTotalHours) && (Auth::user()->role === App\Models\User::ROLE_ADMIN || Auth::user()->role === App\Models\User::ROLE_SUPERVISOR) && (count($reportData) > 1 || !request('employee_id')))

                                        <tr class="bg-gray-50 dark:bg-gray-700 font-semibold">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ __('Suma Całkowita') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ number_format($overallTotalHours, 2) }}
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @elseif (request()->hasAny(['date_from', 'date_to', 'employee_id']))
                        <p>{{ __('Brak danych do wyświetlenia dla wybranych kryteriów.') }}</p>
                    @else
                        <p>{{ __('Wybierz filtry aby wygenerować raport.') }}</p>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>