<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Предмети') }}
            </h2>
            @if(auth()->check() && auth()->user()->can('create subjects'))
                <a href="{{ route('teacher.subjects.create') }}" class="inline-flex items-center px-4 py-2 btn-gradient border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Створити предмет
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <!-- Фільтри -->
                <div class="mb-6">
                    <form action="{{ route('subjects.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <x-label for="search" value="{{ __('Пошук за назвою або описом') }}" />
                            <x-input id="search" class="block mt-1 w-full" type="text" name="search" :value="request('search')" />
                        </div>

                        <div>
                            <x-label for="course_number" value="{{ __('Курс') }}" />
                            <select id="course_number" name="course_number" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="">Всі курси</option>
                                <option value="1" {{ request('course_number') == '1' ? 'selected' : '' }}>1 курс</option>
                                <option value="2" {{ request('course_number') == '2' ? 'selected' : '' }}>2 курс</option>
                                <option value="3" {{ request('course_number') == '3' ? 'selected' : '' }}>3 курс</option>
                                <option value="4" {{ request('course_number') == '4' ? 'selected' : '' }}>4 курс</option>
                            </select>
                        </div>

                        <div>
                            <x-label for="category" value="{{ __('Категорія') }}" />
                            <select id="category" name="category" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="">Всі категорії</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 btn-gradient border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Фільтрувати
                            </button>
                            @if(request()->anyFilled(['search', 'course_number', 'category']))
                                <a href="{{ route('subjects.index') }}" class="ml-2 inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Скинути
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Список предметів -->
                @if($subjects->isEmpty())
                    <div class="text-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">Предметів не знайдено</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Спробуйте змінити параметри фільтрації або перегляньте пізніше.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($subjects as $subject)
                            <x-subject-card :subject="$subject" />
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $subjects->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
