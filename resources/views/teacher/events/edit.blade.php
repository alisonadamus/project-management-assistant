<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Редагування події') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('teacher.events.supervisors', $event) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Керівники
                </a>
                <a href="{{ route('events.show', $event) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
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
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 page-container-gradient">
                <form action="{{ route('teacher.events.update', $event) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Назва події -->
                        <div>
                            <x-label for="name" value="{{ __('Назва події') }}" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $event->name)" required autofocus />
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Категорія -->
                        <div>
                            <x-label for="category_id" value="{{ __('Категорія') }}" />
                            <select id="category_id" name="category_id" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
                                <option value="">Виберіть категорію</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Дата початку -->
                        <div>
                            <x-label for="start_date" value="{{ __('Дата початку') }}" />
                            <x-input id="start_date" class="block mt-1 w-full" type="datetime-local" name="start_date" :value="old('start_date', $event->start_date ? date('Y-m-d\TH:i', strtotime($event->start_date)) : '')" required />
                            @error('start_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Дата завершення -->
                        <div>
                            <x-label for="end_date" value="{{ __('Дата завершення') }}" />
                            <x-input id="end_date" class="block mt-1 w-full" type="datetime-local" name="end_date" :value="old('end_date', $event->end_date ? date('Y-m-d\TH:i', strtotime($event->end_date)) : '')" />
                            @error('end_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Колір фону -->
                        <div>
                            <x-label for="bg_color" value="{{ __('Колір фону') }}" />
                            <div class="flex mt-1">
                                <x-input id="bg_color" class="block w-full" type="color" name="bg_color" :value="old('bg_color', $event->bg_color ?? '#4F46E5')" />
                            </div>
                            @error('bg_color')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Колір тексту -->
                        <div>
                            <x-label for="fg_color" value="{{ __('Колір тексту') }}" />
                            <div class="flex mt-1">
                                <x-input id="fg_color" class="block w-full" type="color" name="fg_color" :value="old('fg_color', $event->fg_color ?? '#FFFFFF')" />
                            </div>
                            @error('fg_color')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Поточне зображення -->
                        @if($event->image)
                            <div class="md:col-span-2">
                                <x-label value="{{ __('Поточне зображення') }}" />
                                <div class="mt-2">
                                    <img src="{{ $event->image }}" alt="{{ $event->name }}" class="h-40 object-cover rounded-md">
                                </div>
                            </div>
                        @endif

                        <!-- Зображення -->
                        <div class="md:col-span-2">
                            <x-label for="image" value="{{ __('Нове зображення (залиште порожнім, щоб зберегти поточне)') }}" />
                            <input id="image" type="file" name="image" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-800" />
                            @error('image')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Опис -->
                        <div class="md:col-span-2">
                            <x-label for="description" value="{{ __('Опис') }}" />
                            <x-markdown-editor
                                name="description"
                                :value="old('description', $event->description)"
                                placeholder="Введіть опис події (підтримується Markdown)"
                                id="event-description-editor"
                            />
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-between mt-6">
                        <div>
                            <button type="button" onclick="if(confirm('Ви впевнені, що хочете видалити цю подію? Ця дія незворотна.')) { document.getElementById('delete-form').submit(); }" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                {{ __('Видалити подію') }}
                            </button>
                        </div>

                        <x-button>
                            {{ __('Зберегти зміни') }}
                        </x-button>
                    </div>
                </form>
            </div>

            <!-- Форма для видалення події (прихована) -->
            <form id="delete-form" action="{{ route('teacher.events.destroy', $event) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</x-app-layout>
