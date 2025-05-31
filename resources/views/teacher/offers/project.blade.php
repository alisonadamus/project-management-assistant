<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Заявки на проект') }}: {{ $project->name }}
            </h2>
            <a href="{{ route('teacher.offers.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Назад до всіх заявок
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <div class="mb-6">
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Інформація про проект</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Подія:</span> {{ $project->event->name }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Категорія:</span> {{ $project->event->category->name }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Курс:</span> {{ $project->event->category->course_number }}
                                </p>
                            </div>
                            <div>
                                @php
                                    $supervisor = $project->supervisor;
                                    $assignedProjectsCount = \Alison\ProjectManagementAssistant\Models\Project::where('supervisor_id', $supervisor->id)
                                        ->where('event_id', $project->event_id)
                                        ->whereNotNull('assigned_to')
                                        ->count();
                                    $slotsAvailable = $supervisor->slot_count - $assignedProjectsCount;
                                @endphp
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Статус:</span>
                                    @if($project->assigned_to)
                                        <span class="text-green-600 dark:text-green-400">Призначено студента</span>
                                    @else
                                        <span class="text-yellow-600 dark:text-yellow-400">Не призначено</span>
                                    @endif
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Загальна кількість місць:</span> {{ $supervisor->slot_count }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Доступно місць:</span>
                                    <span class="{{ $slotsAvailable > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $slotsAvailable }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($offers->isEmpty())
                    <div class="text-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">Заявок не знайдено</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            На цей проект ще не подано жодної заявки.
                        </p>
                    </div>
                @else
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Заявки студентів</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Студент</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Курс</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Дата заявки</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Дії</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($offers as $offer)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $offer->student->name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $offer->student->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white">{{ $offer->student->course_number }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white">{{ $offer->created_at->format('d.m.Y H:i') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                @if($slotsAvailable > 0 && !$project->assigned_to)
                                                    <form action="{{ route('teacher.offers.approve', ['project' => $project->id, 'studentId' => $offer->student->id]) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white btn-gradient hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                            Призначити
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('teacher.offers.reject', ['project' => $project->id, 'studentId' => $offer->student->id]) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        Відхилити
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
