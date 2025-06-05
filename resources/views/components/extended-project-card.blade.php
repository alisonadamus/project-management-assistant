@props(['project'])

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg hover:shadow-lg transition-shadow duration-300 flex flex-col h-full card-border-gradient">
    <div class="p-4 flex-grow">
        <!-- Індикатори статусу та повідомлень -->
        <div class="mb-2 flex flex-wrap gap-2">
            @if($project->assigned_to)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium status-approved">
                    Затверджено
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium status-pending">
                    Не затверджено
                </span>
            @endif

            @if($project->has_unread_messages)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                    {{ $project->unread_messages_count }} нових
                </span>
            @endif
        </div>

        <!-- Назва проекту -->
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $project->name }}</h3>
        
        @if($project->technologies->isNotEmpty())
            <div class="mt-2 flex flex-wrap gap-1">
                @foreach($project->technologies as $tech)
                    <a href="{{ route('technologies.show', $tech) }}" 
                       class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium badge-gradient hover:shadow-md transition-all duration-200 transform hover:scale-105">
                        {{ $tech->name }}
                    </a>
                @endforeach
            </div>
        @endif
        
        <div class="mt-4 text-sm text-gray-600 dark:text-gray-300 line-clamp-3">
            {{ $project->body_preview ?? 'Немає опису' }}
        </div>
        
        <div class="mt-4">
            @if($project->supervisor)
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Керівник: {{ $project->supervisor->user->full_name }}</span>
                </div>
            @endif
            
            @if($project->assigned_to)
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Студент: {{ $project->assignedTo->full_name }}</span>
                </div>
            @endif
            
            @if($project->event)
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Подія: {{ $project->event->name }} ({{ $project->event_projects_count }} проектів)</span>
                </div>
            @endif

            @if(!$project->assigned_to && $project->offers()->count() > 0)
                <div class="flex items-center text-sm mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <a href="{{ route('teacher.offers.project', $project) }}" class="text-primary-600 hover:text-primary-700 hover:underline transition-colors duration-200">
                        Переглянути заявки ({{ $project->offers()->count() }})
                    </a>
                </div>
            @endif
        </div>
    </div>
    
    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 text-right">
        <a href="{{ route('projects.show', $project) }}" class="inline-flex items-center px-4 py-2 btn-gradient border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Детальніше
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
        </a>
    </div>
</div>
