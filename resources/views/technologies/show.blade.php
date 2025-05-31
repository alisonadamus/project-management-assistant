<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $technology->name }}
            </h2>
            <div class="flex space-x-2">
                @if(auth()->check() && auth()->user()->can('edit technologies'))
                    <a href="{{ route('teacher.technologies.edit', $technology) }}" class="inline-flex items-center px-4 py-2 btn-gradient border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Редагувати
                    </a>
                    <form action="{{ route('teacher.technologies.destroy', $technology) }}" method="POST" onsubmit="return confirm('Ви впевнені, що хочете видалити цю технологію?');" class="inline">
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
                <a href="{{ route('technologies.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
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
                <!-- Заголовок технології -->
                <div class="relative">
                    @if($technology->image)
                        <div class="h-64 w-full overflow-hidden">
                            @php
                                // Remove any leading slash to avoid double slashes
                                $imagePath = ltrim($technology->image, '/');
                                // If path starts with storage/, replace it with /storage/
                                if (str_starts_with($imagePath, 'storage/')) {
                                    $imagePath = '/storage/' . substr($imagePath, 8);
                                }
                            @endphp
                            <img src="{{ $imagePath }}" alt="{{ $technology->name }}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                        </div>
                    @else
                        <div class="h-64 w-full header-gradient"></div>
                    @endif

                    <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                        <h1 class="text-3xl font-bold">{{ $technology->name }}</h1>
                        @if($technology->link)
                            <a href="{{ $technology->link }}" target="_blank" class="inline-flex items-center mt-2 px-3 py-1 btn-gradient hover:shadow-md rounded-md text-sm font-medium text-white transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                Перейти до документації
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Інформація про технологію -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Опис</h2>
                            <div class="prose dark:prose-invert max-w-none">
                                <p class="text-gray-600 dark:text-gray-300">
                                    {{ $technology->description ?? 'Опис відсутній' }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Деталі технології</h2>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Slug</p>
                                            <p class="text-gray-900 dark:text-white">{{ $technology->slug }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Створено</p>
                                            <p class="text-gray-900 dark:text-white">{{ $technology->created_at->format('d.m.Y H:i') }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Оновлено</p>
                                            <p class="text-gray-900 dark:text-white">{{ $technology->updated_at->format('d.m.Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mt-6 mb-4">Пов'язані проекти</h2>
                            @if($technology->projects->isEmpty())
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <p class="text-gray-500 dark:text-gray-400">Немає пов'язаних проектів</p>
                                </div>
                            @else
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <ul class="space-y-2">
                                        @foreach($technology->projects as $project)
                                            <li>
                                                <div class="flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                    </svg>
                                                    <span class="text-gray-900 dark:text-white">{{ $project->name }}</span>
                                                </div>
                                                @if($project->event)
                                                    <div class="ml-5 text-sm text-gray-500 dark:text-gray-400">
                                                        Подія: {{ $project->event->name }}
                                                    </div>
                                                @endif
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
