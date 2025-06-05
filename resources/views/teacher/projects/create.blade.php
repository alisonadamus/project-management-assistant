<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Створення нового проекту') }}
            </h2>
            <a href="{{ route('events.show', $event) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Назад до події
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg page-container-gradient">
                <div class="p-6">
                    <form action="{{ route('teacher.projects.store', $event) }}" method="POST">
                        @csrf
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Подія: {{ $event->name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Науковий керівник: {{ auth()->user()->name }}</p>
                        </div>

                        <div class="mb-6">
                            <x-label for="name" value="{{ __('Назва проекту') }}" />
                            <x-input id="name" class="block mt-1 w-full input-gradient" type="text" name="name" :value="old('name')" required autofocus />
                            @error('name')
                                <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <x-label for="body" value="{{ __('Опис проекту') }}" />
                            <x-markdown-editor
                                name="body"
                                :value="old('body')"
                                placeholder="Введіть опис проекту (підтримується Markdown)"
                                id="project-body-editor"
                            />
                            @error('body')
                                <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <x-label for="appendix" value="{{ __('Посилання на додаткові матеріали (необов\'язково)') }}" />
                            <x-input id="appendix" class="block mt-1 w-full input-gradient" type="url" name="appendix" :value="old('appendix')" placeholder="https://example.com" />
                            @error('appendix')
                                <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <x-label for="technologies" value="{{ __('Технології') }}" />
                            <div class="mt-2 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                @foreach($technologies as $technology)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="technologies[]" value="{{ $technology->id }}" class="checkbox-gradient"
                                            {{ in_array($technology->id, old('technologies', [])) ? 'checked' : '' }}>
                                        <span class="ml-2 text-gray-700 dark:text-gray-300">{{ $technology->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('technologies')
                                <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <x-button>
                                {{ __('Створити проект') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
