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
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
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

                <!-- Інформація про подію -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Опис</h2>
                            <div class="prose dark:prose-invert max-w-none">
                                <p class="text-gray-600 dark:text-gray-300">
                                    {{ $event->description ?? 'Опис відсутній' }}
                                </p>
                            </div>

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
                                                    {{ substr($supervisor->user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $supervisor->user->name }}</p>
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
</x-app-layout>
