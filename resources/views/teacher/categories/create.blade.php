<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Створення нової категорії') }}
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
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('teacher.categories.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Назва категорії -->
                        <div>
                            <x-label for="name" value="{{ __('Назва категорії') }}" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Курс -->
                        <div>
                            <x-label for="course_number" value="{{ __('Курс') }}" />
                            <x-select id="course_number" name="course_number" class="block mt-1 w-full">
                                <option value="">Не вказано</option>
                                <option value="1" {{ old('course_number') == '1' ? 'selected' : '' }}>1 курс</option>
                                <option value="2" {{ old('course_number') == '2' ? 'selected' : '' }}>2 курс</option>
                                <option value="3" {{ old('course_number') == '3' ? 'selected' : '' }}>3 курс</option>
                                <option value="4" {{ old('course_number') == '4' ? 'selected' : '' }}>4 курс</option>
                            </x-select>
                            @error('course_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Період -->
                        <div>
                            <x-label for="period" value="{{ __('Період (днів)') }}" />
                            <x-input id="period" class="block mt-1 w-full" type="number" name="period" :value="old('period')" min="1" required />
                            @error('period')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Період заморозки -->
                        <div>
                            <x-label for="freezing_period" value="{{ __('Період заморозки (днів)') }}" />
                            <x-input id="freezing_period" class="block mt-1 w-full" type="number" name="freezing_period" :value="old('freezing_period')" min="1" />
                            @error('freezing_period')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Предмети -->
                        <div class="md:col-span-2">
                            <x-label for="subjects" value="{{ __('Пов\'язані предмети') }}" />
                            <div class="mt-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach($subjects as $subject)
                                    <div class="flex items-center">
                                        <x-checkbox id="subject_{{ $subject->id }}" name="subjects[]" value="{{ $subject->id }}" :checked="in_array($subject->id, old('subjects', []))" />
                                        <label for="subject_{{ $subject->id }}" class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $subject->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                            @error('subjects')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <x-button>
                            {{ __('Створити категорію') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
