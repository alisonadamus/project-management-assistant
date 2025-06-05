<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Заявки на проекти') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 page-container-gradient">
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

                @if($offers->isEmpty())
                    <div class="text-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">Заявок не знайдено</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            На ваші проекти ще не подано жодної заявки.
                        </p>
                    </div>
                @else
                    <div class="space-y-8">
                        @foreach($offers as $projectId => $projectData)
                            @php
                                $project = $projectData->project;
                                $projectOffers = $projectData->offers;
                                $supervisor = $project->supervisor;
                                $event = $project->event;
                                $assignedProjectsCount = \Alison\ProjectManagementAssistant\Models\Project::where('supervisor_id', $supervisor->id)
                                    ->where('event_id', $event->id)
                                    ->whereNotNull('assigned_to')
                                    ->count();
                                $slotsAvailable = $supervisor->slot_count - $assignedProjectsCount;
                            @endphp

                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg">
                                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $project->name }}</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Подія: {{ $event->name }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $slotsAvailable > 0 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                {{ $slotsAvailable > 0 ? "Доступно місць: $slotsAvailable" : "Немає вільних місць" }}
                                            </span>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Заявок: {{ $projectOffers->count() }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4">
                                    <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-2">Заявки студентів:</h4>
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
                                                @foreach($projectOffers as $offer)
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $offer->student->full_name }}</div>
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
                                </div>

                                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 flex justify-between items-center">
                                    <div class="flex space-x-2">
                                        @if($slotsAvailable > 0 && !$project->assigned_to && $projectOffers->count() > 1)
                                            <form action="{{ route('teacher.offers.assign-random', ['project' => $project->id]) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit"
                                                        class="inline-flex items-center px-4 py-2 btn-gradient border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                                        onclick="return confirm('Ви впевнені, що хочете випадково призначити студента до цього проекту?')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                    Рандом
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    <a href="{{ route('projects.show', $project) }}" class="inline-flex items-center px-4 py-2 btn-gradient border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Деталі проекту
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if(isset($paginator))
                        <div class="mt-6">
                            {{ $paginator->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
