<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $project->name }}
            </h2>
            <div class="flex space-x-2">
                @php
                    $isSupervisor = $project->supervisor && $project->supervisor->user_id == auth()->id();
                    $canEditProject = auth()->check() && auth()->user()->can('edit projects');
                    $isAdmin = auth()->check() && auth()->user()->hasRole('admin');
                @endphp

                @if(auth()->check() && ($isAdmin || $isSupervisor) && $canEditProject)
                    <div class="flex space-x-2">
                        @if($isSupervisor && !$project->assigned_to)
                            @php
                                $offersCount = \Alison\ProjectManagementAssistant\Models\Offer::where('project_id', $project->id)->count();
                            @endphp
                            <a href="{{ route('teacher.offers.project', $project) }}"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Заявки ({{ $offersCount }})
                            </a>
                        @endif

                        <a href="{{ route('teacher.projects.edit', $project) }}"
                           class="inline-flex items-center px-4 py-2 btn-gradient border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Редагувати
                        </a>

                        <form action="{{ route('teacher.projects.destroy', $project) }}" method="POST" class="inline-block"
                              onsubmit="return confirm('Ви впевнені, що хочете видалити цей проект? Ця дія незворотна.')">
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
                    </div>
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
                <div class="p-6">
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
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Опис проекту</h2>
                            <div class="prose dark:prose-invert max-w-none">
                                <p class="text-gray-600 dark:text-gray-300">
                                    {{ $project->body ?? 'Опис відсутній' }}
                                </p>
                            </div>

                            @if($project->appendix)
                                <div class="mt-6">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Додаткові матеріали</h3>
                                    <a href="{{ $project->appendix }}" target="_blank" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:underline">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        Перейти до матеріалів
                                    </a>
                                </div>
                            @endif

                            @if($project->technologies->isNotEmpty())
                                <div class="mt-6">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Технології</h3>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($project->technologies as $tech)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ $tech->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Деталі проекту</h2>

                                <div class="space-y-3">
                                    @if($project->event)
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Подія</p>
                                                <a href="{{ route('events.show', $project->event) }}" class="text-gray-900 dark:text-white hover:underline">
                                                    {{ $project->event->name }}
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    @if($project->supervisor)
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            <div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Науковий керівник</p>
                                                <p class="text-gray-900 dark:text-white">{{ $project->supervisor->user->name }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Статус</p>
                                            @if($project->assigned_to)
                                                <p class="text-green-600 dark:text-green-400">Затверджено за студентом</p>
                                            @else
                                                <p class="text-yellow-600 dark:text-yellow-400">Не затверджено</p>
                                            @endif
                                        </div>
                                    </div>

                                    @if($project->assigned_to)
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-400" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            <div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Студент</p>
                                                <p class="text-gray-900 dark:text-white">{{ $project->assignedTo->name }}</p>
                                            </div>
                                        </div>
                                    @elseif(auth()->check() && $isSupervisor && !$project->assigned_to)
                                        @php
                                            $offersCount = \Alison\ProjectManagementAssistant\Models\Offer::where('project_id', $project->id)->count();
                                        @endphp
                                        <div class="mt-4">
                                            <a href="{{ route('teacher.offers.project', $project) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white btn-gradient hover:shadow-md focus:outline-none focus:border-primary-700 focus:ring focus:ring-primary-200 transition ease-in-out duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                                Переглянути заявки ({{ $offersCount }})
                                            </a>
                                        </div>
                                    @elseif(auth()->check() && auth()->user()->hasRole('student') && auth()->user()->course_number == $project->event->category->course_number)
                                        @php
                                            $hasOffer = \Alison\ProjectManagementAssistant\Models\Offer::where('project_id', $project->id)
                                                ->where('student_id', auth()->id())
                                                ->exists();

                                            $hasProjectInEvent = \Alison\ProjectManagementAssistant\Models\Project::where('event_id', $project->event_id)
                                                ->where('assigned_to', auth()->id())
                                                ->exists();

                                            $supervisor = $project->supervisor;
                                            $assignedProjectsCount = \Alison\ProjectManagementAssistant\Models\Project::where('supervisor_id', $supervisor->id)
                                                ->where('event_id', $project->event_id)
                                                ->whereNotNull('assigned_to')
                                                ->count();
                                            $slotsAvailable = $supervisor->slot_count - $assignedProjectsCount;
                                        @endphp

                                        <div class="mt-4">
                                            @if($hasProjectInEvent)
                                                <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-md">
                                                    <p class="text-yellow-800 dark:text-yellow-200 text-sm">
                                                        Ви вже призначені до іншого проекту в цій події.
                                                    </p>
                                                </div>
                                            @elseif($slotsAvailable <= 0)
                                                <div class="bg-red-100 dark:bg-red-900 p-3 rounded-md">
                                                    <p class="text-red-800 dark:text-red-200 text-sm">
                                                        У наукового керівника немає вільних місць.
                                                    </p>
                                                </div>
                                            @elseif($hasOffer)
                                                <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-md">
                                                    <p class="text-blue-800 dark:text-blue-200 text-sm">
                                                        Ви вже подали заявку на цей проект.
                                                    </p>
                                                </div>
                                                <form action="{{ route('offers.destroy', $project) }}" method="POST" class="mt-2">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        Скасувати заявку
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('offers.store', $project) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-4 py-2 btn-gradient border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        Подати заявку на проект
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Компонент чату проекту -->
                <x-project-chat :project="$project" />
            </div>
        </div>
    </div>
</x-app-layout>
