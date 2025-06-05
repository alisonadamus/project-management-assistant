<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Створення нового предмету') }}
            </h2>
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Назад
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 page-container-gradient">
                <form action="{{ route('teacher.subjects.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Назва предмету -->
                        <div>
                            <x-label for="name" value="{{ __('Назва предмету') }}" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <x-label for="slug" value="{{ __('Slug') }}" />
                            <x-input id="slug" class="block mt-1 w-full" type="text" name="slug" :value="old('slug')" required />
                            @error('slug')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Курс -->
                        <div>
                            <x-label for="course_number" value="{{ __('Курс') }}" />
                            <x-select id="course_number" name="course_number" class="block mt-1 w-full" required>
                                <option value="">Виберіть курс</option>
                                <option value="1" {{ old('course_number') == '1' ? 'selected' : '' }}>1 курс</option>
                                <option value="2" {{ old('course_number') == '2' ? 'selected' : '' }}>2 курс</option>
                                <option value="3" {{ old('course_number') == '3' ? 'selected' : '' }}>3 курс</option>
                                <option value="4" {{ old('course_number') == '4' ? 'selected' : '' }}>4 курс</option>
                            </x-select>
                            @error('course_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Зображення -->
                        <div>
                            <x-label for="image" value="{{ __('Зображення') }}" />
                            <x-file-input id="image" name="image" />
                            @error('image')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Опис -->
                        <div class="md:col-span-2">
                            <x-label for="description" value="{{ __('Опис') }}" />
                            <x-markdown-editor
                                name="description"
                                :value="old('description')"
                                placeholder="Введіть опис предмету (підтримується Markdown)"
                                id="subject-description-editor"
                            />
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Категорії -->
                        <div class="md:col-span-2">
                            <x-label for="categories" value="{{ __('Пов\'язані категорії') }}" />
                            <div class="mt-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach($categories as $category)
                                    <div class="flex items-center">
                                        <input type="checkbox" id="category_{{ $category->id }}" name="categories[]" value="{{ $category->id }}" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 dark:focus:ring-offset-gray-800" {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                                        <label for="category_{{ $category->id }}" class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $category->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                            @error('categories')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <x-button>
                            {{ __('Створити предмет') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Автоматичне генерування slug з назви
        document.getElementById('name').addEventListener('input', function() {
            const name = this.value;
            const slug = name.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
            document.getElementById('slug').value = slug;
        });
    </script>
</x-app-layout>
