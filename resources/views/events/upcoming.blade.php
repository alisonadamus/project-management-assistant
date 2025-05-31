<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Майбутні події') }}
            </h2>
            @if(auth()->check() && auth()->user()->can('create events'))
                <a href="{{ route('teacher.events.create') }}" class="inline-flex items-center px-4 py-2 btn-gradient border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Створити подію') }}
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <!-- Фільтри та пошук -->
                <div class="mb-6">
                    <form action="{{ route('events.upcoming') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Пошук</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm"
                                   placeholder="Назва події...">
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Категорія</label>
                            <select name="category" id="category"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                <option value="">Всі категорії</option>
                                @foreach(\Alison\ProjectManagementAssistant\Models\Category::all() as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-1 flex items-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 btn-gradient border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Фільтрувати
                            </button>

                            @if(request('search') || request('category'))
                                <a href="{{ route('events.upcoming') }}" class="ml-2 inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Скинути
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                @if($events->isEmpty())
                    <div class="text-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">Майбутніх подій не знайдено</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Наразі немає запланованих майбутніх подій. Перегляньте активні події або перевірте пізніше.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($events as $event)
                            <x-event-card :event="$event" />
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $events->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
