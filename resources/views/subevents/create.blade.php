<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Створення підподії для події: ') . $event->name }}
            </h2>
            <a href="{{ route('events.show', $event) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Назад до події
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 page-container-gradient">
                <form action="{{ route('teacher.subevents.store', $event) }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-label for="name" value="{{ __('Назва підподії') }}" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="depends_on" value="{{ __('Залежить від') }}" />
                            <x-select id="depends_on" name="depends_on" class="block mt-1 w-full">
                                <option value="">Немає залежності</option>
                                @foreach($availableSubevents as $subevent)
                                    <option value="{{ $subevent->id }}" {{ old('depends_on') == $subevent->id ? 'selected' : '' }}>
                                        {{ $subevent->name }}
                                    </option>
                                @endforeach
                            </x-select>
                            @error('depends_on')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="start_date" value="{{ __('Дата початку') }}" />
                            <x-input id="start_date" class="block mt-1 w-full" type="datetime-local" name="start_date" :value="old('start_date')" required />
                            @error('start_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Мінімальна дата: {{ $event->start_date->format('d.m.Y H:i') }}
                            </p>
                        </div>

                        <div>
                            <x-label for="end_date" value="{{ __('Дата завершення (необов\'язково)') }}" />
                            <x-input id="end_date" class="block mt-1 w-full" type="datetime-local" name="end_date" :value="old('end_date')" />
                            @error('end_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            @if($event->end_date)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Максимальна дата: {{ $event->end_date->format('d.m.Y H:i') }}
                                </p>
                            @endif
                        </div>

                        <div>
                            <x-label for="bg_color" value="{{ __('Колір фону') }}" />
                            <input id="bg_color" class="block mt-1 w-full h-10 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" type="color" name="bg_color" value="{{ old('bg_color', '#4F46E5') }}" />
                            @error('bg_color')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="fg_color" value="{{ __('Колір тексту') }}" />
                            <input id="fg_color" class="block mt-1 w-full h-10 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" type="color" name="fg_color" value="{{ old('fg_color', '#FFFFFF') }}" />
                            @error('fg_color')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-label for="description" value="{{ __('Опис (необов\'язково)') }}" />
                        <x-markdown-editor
                            name="description"
                            :value="old('description')"
                            placeholder="Введіть опис підподії"
                            id="subevent-description-editor"
                        />
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end mt-6 space-x-3">
                        <a href="{{ route('events.show', $event) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Скасувати
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 btn-gradient border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Створити підподію
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Встановлення мінімальних та максимальних дат
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const form = document.querySelector('form');

            // Встановлення мінімальної дати початку
            const eventStartDate = new Date('{{ $event->start_date->format('Y-m-d\TH:i') }}');
            startDateInput.min = eventStartDate.toISOString().slice(0, 16);

            @if($event->end_date)
                // Встановлення максимальної дати завершення
                const eventEndDate = new Date('{{ $event->end_date->format('Y-m-d\TH:i') }}');
                endDateInput.max = eventEndDate.toISOString().slice(0, 16);
                startDateInput.max = eventEndDate.toISOString().slice(0, 16);
            @endif

            // Оновлення мінімальної дати завершення при зміні дати початку
            startDateInput.addEventListener('change', function() {
                if (this.value) {
                    endDateInput.min = this.value;
                }
            });

            // Валідація перед відправкою форми
            form.addEventListener('submit', function(e) {
                const startDate = new Date(startDateInput.value);
                const endDate = endDateInput.value ? new Date(endDateInput.value) : null;

                // Перевірка дати початку
                if (startDate < eventStartDate) {
                    e.preventDefault();
                    alert('Дата початку не може бути раніше дати початку події: ' + eventStartDate.toLocaleString('uk-UA'));
                    return;
                }

                @if($event->end_date)
                    // Перевірка дати завершення
                    if (startDate > eventEndDate) {
                        e.preventDefault();
                        alert('Дата початку не може бути пізніше дати завершення події: ' + eventEndDate.toLocaleString('uk-UA'));
                        return;
                    }

                    if (endDate && endDate > eventEndDate) {
                        e.preventDefault();
                        alert('Дата завершення не може бути пізніше дати завершення події: ' + eventEndDate.toLocaleString('uk-UA'));
                        return;
                    }
                @endif

                // Перевірка, що дата завершення не раніше дати початку
                if (endDate && endDate < startDate) {
                    e.preventDefault();
                    alert('Дата завершення не може бути раніше дати початку');
                    return;
                }
            });
        });
    </script>
</x-app-layout>
