<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Szczegóły Wpisu Czasu Pracy') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Informacje o Wpisie') }}</h3>
                            <div class="mt-4 space-y-2">
                                <p><strong>{{ __('Pracownik') }}:</strong> {{ $workEntry->employee->name }}</p>
                                <p><strong>{{ __('Data Pracy') }}:</strong> {{ $workEntry->date_of_work->format('Y-m-d') }}</p>
                                <p><strong>{{ __('Przepracowane Godziny') }}:</strong> {{ number_format($workEntry->hours_worked, 2) }}</p>
                                <p><strong>{{ __('Wprowadził') }}:</strong> {{ $workEntry->enteredBy->name }}</p>
                                <p><strong>{{ __('Data Utworzenia') }}:</strong> {{ $workEntry->created_at->format('Y-m-d H:i:s') }}</p>
                                <p><strong>{{ __('Data Modyfikacji') }}:</strong> {{ $workEntry->updated_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('work-entries.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Wróć do Listy') }}
                        </a>
                        @can('update-work-entry', $workEntry)
                        <a href="{{ route('work-entries.edit', $workEntry) }}" class="inline-flex items-center ml-3 px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Edytuj Wpis') }}
                        </a>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Komentarze') }}</h3>

                    @if (session('success_comment'))
                        <div class="mb-4 p-3 bg-green-100 dark:bg-green-700 text-green-700 dark:text-green-100 rounded-md">
                            {{ session('success_comment') }}
                        </div>
                    @endif
                    @if (session('error_comment'))
                        <div class="mb-4 p-3 bg-red-100 dark:bg-red-700 text-red-700 dark:text-red-100 rounded-md">
                            {{ session('error_comment') }}
                        </div>
                    @endif

                    @can('add-comment', $workEntry)
                        <form method="POST" action="{{ route('comments.store', $workEntry) }}" class="mb-6">
                            @csrf
                            <div>
                                <x-input-label for="comment_text" :value="__('Dodaj Komentarz')" />
                                <textarea id="comment_text" name="comment_text" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>{{ old('comment_text') }}</textarea>
                                <x-input-error :messages="$errors->get('comment_text')" class="mt-2" />
                            </div>
                            <div class="mt-4">
                                <x-primary-button>
                                    {{ __('Dodaj Komentarz') }}
                                </x-primary-button>
                            </div>
                        </form>
                    @endcan

                    <div class="space-y-4">
                        @forelse ($workEntry->comments->sortByDesc('created_at') as $comment)
                            <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-md">
                                <div class="flex justify-between items-center mb-1">
                                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $comment->user->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</p>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ $comment->comment_text }}</p>
                                @if(Gate::allows('update-comment', $comment) || Gate::allows('delete-comment', $comment))
                                <div class="mt-2 text-xs">
                                    @can('update-comment', $comment)
                                    @endcan
                                    @can('delete-comment', $comment)
                                        <form method="POST" action="{{ route('comments.destroy', $comment) }}" class="inline ml-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Czy na pewno chcesz usunąć ten komentarz?')">
                                                {{ __('Usuń') }}
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Brak komentarzy.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>