<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $category->name }}
            </h2>
            <div class="flex space-x-2">
                @if(auth()->check() && (auth()->user()->hasRole('teacher') || auth()->user()->hasRole('admin')))
                    <a href="{{ route('teacher.categories.edit', $category) }}" class="inline-flex items-center px-4 py-2 btn-gradient border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Редагувати
                    </a>
                    <form action="{{ route('teacher.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Ви впевнені, що хочете видалити цю категорію?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Видалити
                        </button>
                    </form>
                @endif
                <a href="{{ route('categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Назад
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <!-- Інформація про категорію -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Інформація про категорію</h2>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Назва:</p>
                                        <p class="text-gray-900 dark:text-white font-medium">{{ $category->name }}</p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Курс:</p>
                                        <p class="text-gray-900 dark:text-white font-medium">{{ $category->course_number ?? 'Не вказано' }}</p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Період:</p>
                                        <p class="text-gray-900 dark:text-white font-medium">{{ $category->period }} днів</p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Період заморозки:</p>
                                        <p class="text-gray-900 dark:text-white font-medium">{{ $category->freezing_period ?? 'Не вказано' }}</p>
                                    </div>
                                </div>
                            </div>

                            @if($category->attachments)
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-4">Прикріплені файли</h2>
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <ul class="space-y-2">
                                        @foreach($category->attachments as $name => $url)
                                            <li>
                                                <a href="{{ $url }}" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                                    </svg>
                                                    {{ $name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Пов'язані предмети</h2>
                            @if($category->subjects->isEmpty())
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <p class="text-gray-500 dark:text-gray-400">Немає пов'язаних предметів</p>
                                </div>
                            @else
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <ul class="space-y-2">
                                        @foreach($category->subjects as $subject)
                                            <li>
                                                <a href="{{ route('subjects.show', $subject) }}" class="text-primary-600 dark:text-primary-400 hover:underline flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                    </svg>
                                                    {{ $subject->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
