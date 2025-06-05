<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Мої проекти') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 page-container-gradient">
                <!-- Фільтри та сортування -->
                <div class="mb-6">
                    <form action="{{ route('projects.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <x-label for="search" value="{{ __('Пошук за назвою або описом') }}" />
                            <x-input id="search" class="block mt-1 w-full" type="text" name="search" :value="request('search')" placeholder="Введіть назву або опис..." />
                        </div>

                        <div>
                            <x-label for="event" value="{{ __('Подія') }}" />
                            <x-select id="event" name="event" class="block mt-1 w-full">
                                <option value="">Всі події</option>
                                @foreach($events as $event)
                                    <option value="{{ $event->id }}" {{ request('event') == $event->id ? 'selected' : '' }}>{{ $event->name }}</option>
                                @endforeach
                            </x-select>
                        </div>

                        <div>
                            <x-label for="technology" value="{{ __('Технологія') }}" />
                            <x-select id="technology" name="technology" class="block mt-1 w-full">
                                <option value="">Всі технології</option>
                                @foreach($technologies as $technology)
                                    <option value="{{ $technology->id }}" {{ request('technology') == $technology->id ? 'selected' : '' }}>{{ $technology->name }}</option>
                                @endforeach
                            </x-select>
                        </div>

                        <div>
                            <x-label for="status" value="{{ __('Статус') }}" />
                            <x-select id="status" name="status" class="block mt-1 w-full">
                                <option value="">Всі статуси</option>
                                <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Призначені</option>
                                <option value="unassigned" {{ request('status') == 'unassigned' ? 'selected' : '' }}>Не призначені</option>
                            </x-select>
                        </div>

                        <div class="flex items-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 btn-gradient border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Фільтрувати
                            </button>
                            @if(request()->anyFilled(['search', 'event', 'technology', 'status']))
                                <a href="{{ route('projects.index') }}" class="ml-2 inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Скинути
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Сортування -->
                <div class="mb-6 flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Сортувати за:</span>
                        <div class="flex space-x-2">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_direction' => request('sort_direction') === 'desc' ? 'asc' : 'desc']) }}"
                               class="inline-flex items-center px-3 py-1 text-sm {{ request('sort_by') === 'name' ? 'btn-gradient text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }} rounded-md hover:shadow-md transition ease-in-out duration-150">
                                Назвою
                                @if(request('sort_by') === 'name')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        @if(request('sort_direction') === 'desc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                        @endif
                                    </svg>
                                @endif
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_direction' => request('sort_direction') === 'desc' ? 'asc' : 'desc']) }}"
                               class="inline-flex items-center px-3 py-1 text-sm {{ request('sort_by') === 'created_at' || !request('sort_by') ? 'btn-gradient text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }} rounded-md hover:shadow-md transition ease-in-out duration-150">
                                Датою створення
                                @if(request('sort_by') === 'created_at' || !request('sort_by'))
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        @if(request('sort_direction') === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                        @endif
                                    </svg>
                                @endif
                            </a>
                        </div>
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Знайдено: {{ $projects->total() }} {{ trans_choice('проект|проекти|проектів', $projects->total()) }}
                    </div>
                </div>

                <!-- Список проектів -->
                @if($projects->isEmpty())
                    <div class="text-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">Проектів не знайдено</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            @if(request()->anyFilled(['search', 'event', 'technology', 'status']))
                                Спробуйте змінити параметри фільтрації або перегляньте пізніше.
                            @else
                                @if(auth()->user()->hasRole('teacher'))
                                    Ви ще не створили жодного проекту або не є науковим керівником жодного проекту.
                                @else
                                    Ви ще не призначені до жодного проекту.
                                @endif
                            @endif
                        </p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($projects as $project)
                            <x-extended-project-card :project="$project" />
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $projects->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
