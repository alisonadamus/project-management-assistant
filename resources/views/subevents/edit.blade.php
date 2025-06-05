<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Редагування підподії: ') . $subevent->name }}
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
                <form action="{{ route('teacher.subevents.update', [$event, $subevent]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-label for="name" value="{{ __('Назва підподії') }}" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $subevent->name)" required autofocus />
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="depends_on" value="{{ __('Залежить від') }}" />
                            <x-select id="depends_on" name="depends_on" class="block mt-1 w-full">
                                <option value="">Немає залежності</option>
                                @foreach($availableSubevents as $availableSubevent)
                                    <option value="{{ $availableSubevent->id }}" {{ (old('depends_on', $subevent->depends_on) == $availableSubevent->id) ? 'selected' : '' }}>
                                        {{ $availableSubevent->name }}
                                    </option>
                                @endforeach
                            </x-select>
                            @error('depends_on')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="start_date" value="{{ __('Дата початку') }}" />
                            <x-input id="start_date" class="block mt-1 w-full" type="datetime-local" name="start_date" :value="old('start_date', $subevent->start_date->format('Y-m-d\TH:i'))" required />
                            @error('start_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Мінімальна дата: {{ $event->start_date->format('d.m.Y H:i') }}
                            </p>
                        </div>

                        <div>
                            <x-label for="end_date" value="{{ __('Дата завершення (необов\'язково)') }}" />
                            <x-input id="end_date" class="block mt-1 w-full" type="datetime-local" name="end_date" :value="old('end_date', $subevent->end_date ? $subevent->end_date->format('Y-m-d\TH:i') : '')" />
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
                            <input id="bg_color" class="block mt-1 w-full h-10 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" type="color" name="bg_color" value="{{ old('bg_color', $subevent->bg_color ?? '#4F46E5') }}" />
                            @error('bg_color')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <x-label for="fg_color" value="{{ __('Колір тексту') }}" />
                            <input id="fg_color" class="block mt-1 w-full h-10 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" type="color" name="fg_color" value="{{ old('fg_color', $subevent->fg_color ?? '#FFFFFF') }}" />
                            @error('fg_color')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-label for="description" value="{{ __('Опис (необов\'язково)') }}" />
                        <x-markdown-editor
                            name="description"
                            :value="old('description', $subevent->description)"
                            placeholder="Введіть опис підподії"
                            id="subevent-description-editor"
                        />
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-between mt-6">
                        <div>
                            <button type="button" onclick="if(confirm('Ви впевнені, що хочете видалити цю підподію?')) { document.getElementById('delete-subevent-form').submit(); }" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Видалити
                            </button>
                        </div>

                        <div class="flex space-x-3">
                            <a href="{{ route('events.show', $event) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Скасувати
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 btn-gradient border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Оновити підподію
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Форма для видалення підподії (прихована) -->
                <form id="delete-subevent-form" action="{{ route('teacher.subevents.destroy', [$event, $subevent]) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>

    <script>
        // Встановлення мінімальних та максимальних дат
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const form = document.querySelector('form:not(#delete-subevent-form)');

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
