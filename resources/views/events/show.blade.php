<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $event->name }}
            </h2>
            <div class="flex space-x-2">
                @php
                    $isSupervisor = $event->supervisors()->where('user_id', auth()->id())->exists();
                @endphp

                @if(auth()->check() && ($isSupervisor || auth()->user()->can('edit events')))
                    <a href="{{ route('teacher.events.edit', $event) }}"
                       class="inline-flex items-center px-4 py-2 btn-gradient border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Редагувати
                    </a>
                    <a href="{{ route('teacher.events.supervisors', $event) }}"
                       class="inline-flex items-center px-4 py-2 btn-gradient border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Керівники
                    </a>
                    <form action="{{ route('teacher.events.destroy', $event) }}" method="POST" class="inline-block"
                          onsubmit="return confirm('Ви впевнені, що хочете видалити цю подію? Ця дія незворотна.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            {{ __('Видалити') }}
                        </button>
                    </form>
                @endif


                <a href="{{ url()->previous() }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Назад
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg page-container-gradient">
                <!-- Заголовок події -->
                <div class="relative">
                    @if($event->image_url)
                        <div class="h-64 w-full overflow-hidden">
                            <img src="{{ $event->image_url }}" alt="{{ $event->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('storage/events/' . basename($event->image)) }}';">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                        </div>
                    @else
                        <div class="h-64 w-full" style="background-color: {{ $event->bg_color ?? '#4F46E5' }}"></div>
                    @endif

                    <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                        <div class="flex items-center mb-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                  style="background-color: {{ $event->bg_color ?? '#4F46E5' }}; color: {{ $event->fg_color ?? '#ffffff' }}">
                                {{ $event->category->name }}
                            </span>
                        </div>
                        <h1 class="text-3xl font-bold">{{ $event->name }}</h1>
                    </div>
                </div>

                <!-- Діаграма Ганта для підподій -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Підподії</h2>
                        @php
                            $isSupervisor = $event->supervisors()->where('user_id', auth()->id())->exists();
                        @endphp
                        @if(auth()->check() && $isSupervisor)
                            <a href="{{ route('teacher.subevents.create', $event) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white btn-gradient hover:shadow-md focus:outline-none focus:border-primary-700 focus:ring focus:ring-primary-200 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Додати підподію
                            </a>
                        @endif
                    </div>

                    <div id="gantt-container" class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-4" style="min-height: 400px;">
                        <!-- Панель управління діаграмою -->
                        <div id="gantt-controls" class="mb-4 flex flex-wrap items-center justify-between gap-4 hidden">
                            <!-- Кнопки перемикання режимів перегляду -->
                            <div class="flex space-x-2">
                                <button onclick="changeGanttViewMode('Day')" class="gantt-view-btn px-3 py-1 text-sm bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 rounded hover:bg-primary-200 dark:hover:bg-primary-800 transition-colors">День</button>
                                <button onclick="changeGanttViewMode('Week')" class="gantt-view-btn px-3 py-1 text-sm bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 rounded hover:bg-primary-200 dark:hover:bg-primary-800 transition-colors">Тиждень</button>
                                <button onclick="changeGanttViewMode('Month')" class="gantt-view-btn px-3 py-1 text-sm bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 rounded hover:bg-primary-200 dark:hover:bg-primary-800 transition-colors">Місяць</button>
                            </div>

                            <!-- Контроли масштабування -->
                            <div class="flex items-center space-x-3">
                                <label class="text-sm text-gray-600 dark:text-gray-400">Масштаб:</label>
                                <div class="flex items-center space-x-2">
                                    <button onclick="adjustGanttScale(-10)" class="px-2 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">−</button>
                                    <span id="scale-indicator" class="text-sm text-gray-600 dark:text-gray-400 min-w-[60px] text-center">100%</span>
                                    <button onclick="adjustGanttScale(10)" class="px-2 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">+</button>
                                </div>
                                <button onclick="resetGanttScale()" class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Скинути</button>
                            </div>
                        </div>

                        <div id="gantt-chart" style="min-height: 350px; width: 100%; overflow-x: auto; overflow-y: visible;"></div>
                        <div id="no-subevents" class="text-center py-8 hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">Немає підподій</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Для цієї події ще не створено жодної підподії.
                            </p>
                        </div>
                    </div>

                    <!-- Список описів підподій -->
                    <div id="subevents-descriptions" class="mt-6 hidden">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Опис підподій</h3>
                        <div class="space-y-4" id="subevents-list">
                            <!-- Список підподій буде заповнений JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- Інформація про подію -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Опис</h2>
                            @if($event->description)
                                <x-markdown :content="$event->description" />
                            @else
                                <p class="text-gray-600 dark:text-gray-300">Опис відсутній</p>
                            @endif

                            <div class="flex justify-between items-center mt-8 mb-4">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Проекти</h2>
                                @php
                                    $isSupervisor = $event->supervisors()->where('user_id', auth()->id())->exists();
                                @endphp

                                @if(auth()->check() && $isSupervisor)
                                    <a href="{{ route('teacher.projects.create', $event) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white btn-gradient hover:shadow-md focus:outline-none focus:border-primary-700 focus:ring focus:ring-primary-200 transition ease-in-out duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Додати проект
                                    </a>
                                @endif
                            </div>

                            @if($event->projects->isEmpty())
                                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">Немає проектів</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Для цієї події ще не створено жодного проекту.
                                    </p>
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($event->projects as $project)
                                        <x-project-card :project="$project" />
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Деталі події</h2>

                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Початок</p>
                                            <p class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($event->start_date)->format('d.m.Y H:i') }}</p>
                                        </div>
                                    </div>

                                    @if($event->end_date)
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Завершення</p>
                                                <p class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($event->end_date)->format('d.m.Y H:i') }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Категорія</p>
                                            <p class="text-gray-900 dark:text-white">{{ $event->category->name }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Кількість проектів</p>
                                            <p class="text-gray-900 dark:text-white">{{ $event->projects->count() }}</p>
                                        </div>
                                    </div>
                                </div>

                                @if($event->supervisors->isNotEmpty())
                                    <h3 class="text-md font-semibold text-gray-900 dark:text-white mt-6 mb-3">
                                        Керівники</h3>
                                    <div class="space-y-2">
                                        @foreach($event->supervisors as $supervisor)
                                            <div class="flex items-center">
                                                <div
                                                    class="h-8 w-8 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-600 dark:text-primary-300 mr-2">
                                                    {{ substr($supervisor->user->full_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $supervisor->user->full_name }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $supervisor->note ?? 'Немає примітки' }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Підключення Frappe Gantt -->
    <link rel="stylesheet" href="{{ asset('css/frappe-gantt/frappe-gantt.css') }}">
    <link rel="stylesheet" href="{{ asset('css/gantt-custom.css') }}">
    <script src="{{ asset('js/frappe-gantt/frappe-gantt.umd.js') }}"></script>

    <script>
        // Глобальні змінні для діаграми Ганта
        let ganttInstance = null;
        let currentScale = 100;
        let currentViewMode = 'Day';
        let originalSubeventsData = []; // Зберігаємо оригінальні дані

        // Функція для пошуку оригінальних даних підподії за ID
        function getOriginalSubeventData(taskId) {
            return originalSubeventsData.find(item => item.id === taskId) || null;
        }

        // Функція для створення динамічних CSS стилів для підподій
        function createSubeventStyles(subevents) {
            // Видаляємо попередні стилі, якщо вони існують
            const existingStyle = document.getElementById('subevent-styles');
            if (existingStyle) {
                existingStyle.remove();
            }

            // Створюємо новий style елемент
            const style = document.createElement('style');
            style.id = 'subevent-styles';

            let css = '';
            subevents.forEach(subevent => {
                const className = subevent.custom_class;
                const bgColor = subevent.bg_color || '#4F46E5';
                const fgColor = subevent.fg_color || '#FFFFFF';

                // Стилі для основного бару підподії
                css += `
                    .gantt .bar-wrapper.${className} .bar {
                        fill: ${bgColor} !important;
                        stroke: ${bgColor} !important;
                    }

                    .gantt .bar-wrapper.${className} .bar-progress {
                        fill: ${adjustColorBrightness(bgColor, -20)} !important;
                    }

                    .gantt .bar-wrapper.${className} .bar-label {
                        fill: ${fgColor} !important;
                        font-weight: 500 !important;
                    }

                    .gantt .bar-wrapper.${className}:hover .bar {
                        stroke-width: 2 !important;
                        filter: brightness(1.1);
                    }

                    /* Стилі для тексту назви підподії (ліворуч від діаграми) */
                    .gantt .task-label[data-task-id="${subevent.id}"] {
                        fill: ${fgColor} !important;
                        font-weight: 500 !important;
                    }
                `;
            });

            style.textContent = css;
            document.head.appendChild(style);
        }

        // Функція для зміни яскравості кольору
        function adjustColorBrightness(color, amount) {
            const usePound = color[0] === '#';
            const col = usePound ? color.slice(1) : color;
            const num = parseInt(col, 16);
            let r = (num >> 16) + amount;
            let g = (num >> 8 & 0x00FF) + amount;
            let b = (num & 0x0000FF) + amount;
            r = r > 255 ? 255 : r < 0 ? 0 : r;
            g = g > 255 ? 255 : g < 0 ? 0 : g;
            b = b > 255 ? 255 : b < 0 ? 0 : b;
            return (usePound ? '#' : '') + (r << 16 | g << 8 | b).toString(16).padStart(6, '0');
        }

        // Функція для зміни режиму перегляду діаграми
        function changeGanttViewMode(mode) {
            if (ganttInstance && ganttInstance.change_view_mode) {
                currentViewMode = mode;
                ganttInstance.change_view_mode(mode);

                // Оновлюємо активну кнопку
                document.querySelectorAll('.gantt-view-btn').forEach(btn => {
                    btn.classList.remove('bg-primary-500', 'text-white');
                    btn.classList.add('bg-primary-100', 'dark:bg-primary-900', 'text-primary-700', 'dark:text-primary-300');
                });

                event.target.classList.remove('bg-primary-100', 'dark:bg-primary-900', 'text-primary-700', 'dark:text-primary-300');
                event.target.classList.add('bg-primary-500', 'text-white');

                // Оновлюємо розміри після зміни режиму та повторно застосовуємо кольори
                setTimeout(() => {
                    updateGanttDimensions();
                    if (originalSubeventsData.length > 0) {
                        applyCustomTextColors(originalSubeventsData);
                    }
                }, 100);
            }
        }

        // Функція для оновлення розмірів діаграми
        function updateGanttDimensions() {
            const ganttChart = document.getElementById('gantt-chart');
            const svg = ganttChart?.querySelector('svg');

            if (svg) {
                svg.style.width = 'auto';
                svg.style.minWidth = '100%';
                svg.style.maxWidth = 'none';
                svg.style.height = 'auto';
                svg.style.display = 'block';

                // Застосовуємо поточний масштаб
                if (currentScale !== 100) {
                    updateGanttScale();
                }
            }
        }

        // Функція для зміни масштабу діаграми
        function adjustGanttScale(delta) {
            const newScale = Math.max(50, Math.min(200, currentScale + delta));
            if (newScale !== currentScale) {
                currentScale = newScale;
                updateGanttScale();
            }
        }

        // Функція для скидання масштабу
        function resetGanttScale() {
            currentScale = 100;
            updateGanttScale();
        }

        // Функція для застосування масштабу
        function updateGanttScale() {
            const ganttChart = document.getElementById('gantt-chart');
            const scaleIndicator = document.getElementById('scale-indicator');

            if (ganttChart) {
                const svg = ganttChart.querySelector('svg');
                if (svg) {
                    svg.style.transform = `scale(${currentScale / 100})`;
                    svg.style.transformOrigin = 'top left';

                    // Оновлюємо висоту контейнера для правильного відображення
                    const scaledHeight = (350 * currentScale / 100);
                    ganttChart.style.minHeight = `${scaledHeight}px`;

                    // Забезпечуємо правильну ширину для горизонтальної прокрутки
                    const originalWidth = svg.getAttribute('width') || svg.getBoundingClientRect().width;
                    const scaledWidth = originalWidth * (currentScale / 100);
                    ganttChart.style.width = '100%';
                    ganttChart.style.overflowX = 'auto';

                    // Повторно застосовуємо кольори після масштабування
                    setTimeout(() => {
                        if (originalSubeventsData.length > 0) {
                            applyCustomTextColors(originalSubeventsData);
                        }
                    }, 50);
                }
            }

            if (scaleIndicator) {
                scaleIndicator.textContent = `${currentScale}%`;
            }
        }

        // Функція для створення списку описів підподій
        function createSubeventsDescriptionsList(subevents) {
            const descriptionsContainer = document.getElementById('subevents-descriptions');
            const subeventsList = document.getElementById('subevents-list');

            if (!descriptionsContainer || !subeventsList) {
                return;
            }

            // Фільтруємо підподії, які мають опис
            const subeventsWithDescription = subevents.filter(subevent =>
                subevent.description && subevent.description.trim() !== ''
            );

            if (subeventsWithDescription.length === 0) {
                descriptionsContainer.classList.add('hidden');
                return;
            }

            // Очищуємо попередній вміст
            subeventsList.innerHTML = '';

            // Створюємо елементи для кожної підподії з описом
            subeventsWithDescription.forEach(subevent => {
                const subeventElement = document.createElement('div');
                subeventElement.className = 'bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600';

                // Форматуємо дати
                let startDateStr = 'Невідомо';
                let endDateStr = 'Невідомо';

                try {
                    if (subevent.start_datetime || subevent.start) {
                        const startDate = new Date(subevent.start_datetime || subevent.start);
                        if (!isNaN(startDate.getTime())) {
                            startDateStr = startDate.toLocaleDateString('uk-UA', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        }
                    }

                    if (subevent.end_datetime || subevent.end) {
                        const endDate = new Date(subevent.end_datetime || subevent.end);
                        if (!isNaN(endDate.getTime())) {
                            endDateStr = endDate.toLocaleDateString('uk-UA', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        }
                    }
                } catch (error) {
                    console.error('Error formatting dates for subevent:', subevent.id, error);
                }

                // Створюємо градієнтну рамку
                const gradientId = `gradient-${subevent.id}`;
                const bgColor = subevent.bg_color || '#4F46E5';
                const fgColor = subevent.fg_color || '#FFFFFF';

                // Додаємо градієнт до head, якщо його ще немає
                if (!document.getElementById(gradientId)) {
                    const style = document.createElement('style');
                    style.id = gradientId;
                    style.textContent = `
                        .gradient-border-${subevent.id} {
                            background: linear-gradient(135deg, ${bgColor}, ${adjustColorBrightness(bgColor, 30)});
                            padding: 2px;
                            border-radius: 12px;
                        }
                        .gradient-border-${subevent.id} > div {
                            background: white;
                            border-radius: 10px;
                        }
                        .dark .gradient-border-${subevent.id} > div {
                            background: #374151;
                        }
                    `;
                    document.head.appendChild(style);
                }

                subeventElement.className = `gradient-border-${subevent.id} subevent-description-card`;
                subeventElement.innerHTML = `
                    <div class="p-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-4 h-4 rounded-full" style="background-color: ${bgColor}"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">${subevent.name}</h4>
                                <div class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                                    <div class="flex flex-wrap gap-4">
                                        <span><strong>Початок:</strong> ${startDateStr}</span>
                                        <span><strong>Завершення:</strong> ${endDateStr}</span>
                                    </div>
                                </div>
                                <div class="prose prose-sm dark:prose-invert max-w-none">
                                    <div class="text-gray-700 dark:text-gray-300">${subevent.description}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                subeventsList.appendChild(subeventElement);
            });

            // Показуємо контейнер з описами
            descriptionsContainer.classList.remove('hidden');
        }

        // Функція для оновлення стилів діаграми при зміні теми
        function updateGanttTheme() {
            const ganttContainer = document.getElementById('gantt-chart');
            if (!ganttContainer) return;

            const isDarkTheme = document.documentElement.classList.contains('dark');
            const svg = ganttContainer.querySelector('svg');

            if (svg) {
                // Оновлюємо стилі для темної/світлої теми
                if (isDarkTheme) {
                    svg.style.backgroundColor = '#1f2937';
                } else {
                    svg.style.backgroundColor = 'transparent';
                }
            }
        }

        // Функція для застосування кольорів тексту підподій
        function applyCustomTextColors(subevents) {
            const ganttContainer = document.getElementById('gantt-chart');
            if (!ganttContainer) return;

            const svg = ganttContainer.querySelector('svg');
            if (!svg) return;

            // Застосовуємо кольори для кожної підподії
            subevents.forEach(subevent => {
                const customClass = `subevent-${subevent.id.replace(/[#-]/g, '_')}`;
                const fgColor = subevent.fg_color || '#FFFFFF';

                // Знаходимо всі елементи з класом цієї підподії
                const barWrappers = svg.querySelectorAll(`.bar-wrapper.${customClass}`);
                barWrappers.forEach(wrapper => {
                    // Застосовуємо кольори для тексту на барах
                    const barLabels = wrapper.querySelectorAll('.bar-label');
                    barLabels.forEach(label => {
                        label.style.fill = fgColor;
                        label.style.fontWeight = '500';
                        label.style.textShadow = '0 1px 2px rgba(0, 0, 0, 0.3)';
                    });
                });

                // Знаходимо текстові елементи для назв задач (ліворуч від діаграми)
                const taskLabels = svg.querySelectorAll('.task-label');
                taskLabels.forEach(label => {
                    // Перевіряємо, чи це текст для нашої підподії
                    if (label.textContent && label.textContent.trim() === subevent.name) {
                        label.style.fill = fgColor;
                        label.style.fontWeight = '500';
                        label.style.textShadow = '0 1px 2px rgba(0, 0, 0, 0.2)';
                    }
                });

                // Також шукаємо за data-атрибутами, якщо вони є
                const elementsWithDataId = svg.querySelectorAll(`[data-task-id="${subevent.id}"]`);
                elementsWithDataId.forEach(element => {
                    if (element.tagName === 'text') {
                        element.style.fill = fgColor;
                        element.style.fontWeight = '500';
                    }
                });
            });
        }

        // Спостерігач за змінами теми
        const themeObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    updateGanttTheme();
                    // Повторно застосовуємо кольори при зміні теми
                    setTimeout(() => {
                        if (originalSubeventsData.length > 0) {
                            applyCustomTextColors(originalSubeventsData);
                        }
                    }, 100);
                }
            });
        });

        // Спостерігач за змінами в SVG діаграми для повторного застосування кольорів
        let ganttObserver = null;

        function setupGanttObserver() {
            const ganttContainer = document.getElementById('gantt-chart');
            if (!ganttContainer) return;

            const svg = ganttContainer.querySelector('svg');
            if (!svg) return;

            // Відключаємо попередній спостерігач, якщо він існує
            if (ganttObserver) {
                ganttObserver.disconnect();
            }

            ganttObserver = new MutationObserver(function(mutations) {
                let shouldReapplyColors = false;
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList' || mutation.type === 'attributes') {
                        shouldReapplyColors = true;
                    }
                });

                if (shouldReapplyColors && originalSubeventsData.length > 0) {
                    setTimeout(() => {
                        applyCustomTextColors(originalSubeventsData);
                    }, 50);
                }
            });

            ganttObserver.observe(svg, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['class', 'style']
            });
        }

        // Запускаємо спостерігач за темою
        themeObserver.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        });

        document.addEventListener('DOMContentLoaded', function() {
            const ganttContainer = document.getElementById('gantt-chart');
            const noSubeventsMessage = document.getElementById('no-subevents');
            const isSupervisor = {{ $isSupervisor ? 'true' : 'false' }};

            // Перевірка, чи завантажена бібліотека Gantt
            if (typeof window.Gantt === 'undefined') {
                console.error('Frappe Gantt library not loaded');
                ganttContainer.innerHTML = '<p class="text-red-500 text-center py-4">Помилка: бібліотека діаграми Ганта не завантажена</p>';
                return;
            }

            // Завантаження даних для діаграми Ганта
            fetch('{{ route('subevents.gantt-data', $event) }}')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Gantt data received:', data);

                    // Детальне логування кожного елемента
                    if (Array.isArray(data) && data.length > 0) {
                        console.log('First subevent data:', data[0]);
                        data.forEach((item, index) => {
                            console.log(`Subevent ${index}:`, {
                                id: item.id,
                                name: item.name,
                                description: item.description,
                                start: item.start,
                                end: item.end,
                                start_datetime: item.start_datetime,
                                end_datetime: item.end_datetime,
                                bg_color: item.bg_color,
                                fg_color: item.fg_color
                            });
                        });
                    }

                    if (!Array.isArray(data) || data.length === 0) {
                        // Показуємо повідомлення про відсутність підподій
                        ganttContainer.style.display = 'none';
                        noSubeventsMessage.classList.remove('hidden');
                        return;
                    }

                    // Валідація даних
                    const validData = data.filter(item => {
                        const isValid = item.id && item.name && item.start && item.end;
                        if (!isValid) {
                            console.warn('Invalid subevent data:', item);
                        }
                        return isValid;
                    });

                    console.log('Valid subevent data:', validData);

                    if (validData.length === 0) {
                        ganttContainer.innerHTML = '<p class="text-red-500 text-center py-4">Помилка: некоректні дані підподій</p>';
                        return;
                    }

                    try {
                        // Зберігаємо оригінальні дані
                        originalSubeventsData = validData;

                        // Створюємо динамічні CSS стилі для кожної підподії
                        createSubeventStyles(validData);

                        // Ініціалізуємо діаграму Ганта
                        ganttInstance = new window.Gantt(ganttContainer, validData, {
                            view_mode: currentViewMode,
                            date_format: 'YYYY-MM-DD',
                            bar_height: 20,
                            bar_corner_radius: 3,
                            arrow_curve: 5,
                            padding: 18,
                            column_width: 30,
                            header_height: 50,
                            container_height: 'auto',
                            popup: function(task) {
                                console.log('Popup task data:', task); // Для діагностики
                                console.log('Task keys:', Object.keys(task)); // Показуємо всі доступні ключі

                                // Отримуємо оригінальні дані підподії
                                const originalData = getOriginalSubeventData(task.id);
                                console.log('Original subevent data:', originalData);

                                // Використовуємо оригінальні дані якщо доступні, інакше дані з task
                                const taskName = originalData?.name || task.name || task.title || task.label || 'Без назви';
                                const taskDescription = originalData?.description || task.description || '';

                                // Безпечне парсинг дат - використовуємо оригінальні дані
                                let startDateStr = 'Невідомо';
                                let endDateStr = 'Невідомо';

                                try {
                                    let startDateSource = null;
                                    let endDateSource = null;

                                    if (originalData) {
                                        // Використовуємо оригінальні дані
                                        startDateSource = originalData.start_datetime || originalData.start;
                                        endDateSource = originalData.end_datetime || originalData.end;
                                    } else {
                                        // Fallback до даних з task
                                        if (task._start) {
                                            startDateSource = task._start;
                                        } else if (task.start_datetime) {
                                            startDateSource = task.start_datetime;
                                        } else if (task.start) {
                                            startDateSource = task.start;
                                        }

                                        if (task._end) {
                                            endDateSource = task._end;
                                        } else if (task.end_datetime) {
                                            endDateSource = task.end_datetime;
                                        } else if (task.end) {
                                            endDateSource = task.end;
                                        }
                                    }

                                    console.log('Date sources:', { startDateSource, endDateSource });

                                    if (startDateSource) {
                                        const start_date = new Date(startDateSource);
                                        if (!isNaN(start_date.getTime())) {
                                            startDateStr = start_date.toLocaleDateString('uk-UA', {
                                                year: 'numeric',
                                                month: 'long',
                                                day: 'numeric',
                                                hour: '2-digit',
                                                minute: '2-digit'
                                            });
                                        }
                                    }

                                    if (endDateSource) {
                                        const end_date = new Date(endDateSource);
                                        if (!isNaN(end_date.getTime())) {
                                            endDateStr = end_date.toLocaleDateString('uk-UA', {
                                                year: 'numeric',
                                                month: 'long',
                                                day: 'numeric',
                                                hour: '2-digit',
                                                minute: '2-digit'
                                            });
                                        }
                                    }
                                } catch (dateError) {
                                    console.error('Error parsing dates:', dateError);
                                }

                                // Безпечне отримання кольорів
                                const bgColor = originalData?.bg_color || task.bg_color || task.color || '#4F46E5';
                                const fgColor = originalData?.fg_color || task.fg_color || '#FFFFFF';

                                console.log('Final popup data:', { taskName, taskDescription, startDateStr, endDateStr, bgColor, fgColor });

                                // Перевіряємо, чи активна темна тема
                                const isDarkTheme = document.documentElement.classList.contains('dark');
                                const popupBgColor = isDarkTheme ? '#374151' : bgColor;
                                const popupTextColor = isDarkTheme ? '#f3f4f6' : fgColor;
                                const borderColor = isDarkTheme ? 'rgba(255,255,255,0.2)' : 'rgba(255,255,255,0.2)';
                                const shadowColor = isDarkTheme ? 'rgba(0,0,0,0.5)' : 'rgba(0,0,0,0.15)';

                                return `
                                    <div class="details-container" style="background: ${popupBgColor}; color: ${popupTextColor}; padding: 12px; border-radius: 6px; box-shadow: 0 4px 12px ${shadowColor}; min-width: 250px; border: 1px solid ${isDarkTheme ? '#4b5563' : 'transparent'};">
                                        <h5 style="margin: 0 0 8px 0; font-weight: bold; font-size: 14px;">${taskName}</h5>
                                        ${taskDescription ? `<p style="margin: 0 0 8px 0; font-size: 12px; opacity: 0.9;">${taskDescription}</p>` : ''}
                                        <div style="border-top: 1px solid ${borderColor}; padding-top: 8px; margin-top: 8px;">
                                            <p style="margin: 2px 0; font-size: 12px;"><strong>Початок:</strong> ${startDateStr}</p>
                                            <p style="margin: 2px 0; font-size: 12px;"><strong>Завершення:</strong> ${endDateStr}</p>
                                        </div>
                                        ${isSupervisor ? '<p style="margin: 8px 0 0 0; font-size: 11px; opacity: 0.8; font-style: italic;"><small>Клікніть двічі для редагування</small></p>' : ''}
                                    </div>
                                `;
                            },
                            on_click: function(task) {
                                console.log('Клік на підподію:', task);
                            },
                            on_double_click: function(task) {
                                if (isSupervisor) {
                                    // Перенаправлення на сторінку редагування підподії
                                    window.location.href = `{{ route('teacher.subevents.edit', [$event, ':subeventId']) }}`.replace(':subeventId', task.id);
                                }
                            }
                        });

                        // Забезпечуємо правильне відображення SVG
                        setTimeout(() => {
                            updateGanttDimensions();
                            updateGanttTheme(); // Оновлюємо тему після ініціалізації
                            applyCustomTextColors(validData); // Застосовуємо кольори тексту
                            setupGanttObserver(); // Налаштовуємо спостерігач за змінами в діаграмі
                        }, 100);

                        // Показуємо панель управління
                        const ganttControls = document.getElementById('gantt-controls');
                        if (ganttControls) {
                            ganttControls.classList.remove('hidden');
                        }

                        // Встановлюємо активну кнопку режиму перегляду
                        const dayButton = document.querySelector('button[onclick="changeGanttViewMode(\'Day\')"]');
                        if (dayButton) {
                            dayButton.classList.remove('bg-primary-100', 'dark:bg-primary-900', 'text-primary-700', 'dark:text-primary-300');
                            dayButton.classList.add('bg-primary-500', 'text-white');
                        }

                        // Додаємо обробник зміни розміру вікна
                        window.addEventListener('resize', () => {
                            setTimeout(() => {
                                updateGanttDimensions();
                            }, 100);
                        });

                        // Створюємо список описів підподій
                        createSubeventsDescriptionsList(validData);

                        console.log('Gantt chart initialized successfully');
                    } catch (ganttError) {
                        console.error('Error initializing Gantt chart:', ganttError);
                        ganttContainer.innerHTML = '<p class="text-red-500 text-center py-4">Помилка ініціалізації діаграми Ганта</p>';
                    }
                })
                .catch(error => {
                    console.error('Помилка завантаження даних діаграми Ганта:', error);
                    ganttContainer.innerHTML = '<p class="text-red-500 text-center py-4">Помилка завантаження діаграми Ганта</p>';
                });
        });
    </script>
</x-app-layout>
