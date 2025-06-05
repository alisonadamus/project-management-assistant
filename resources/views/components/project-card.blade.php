@props(['project'])

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg hover:shadow-lg transition-shadow duration-300 flex flex-col h-full card-border-gradient">
    <div class="p-4 flex-grow">
        <!-- Статус проекту -->
        <div class="mb-2">
            @if($project->assigned_to)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium status-approved">
                    Затверджено
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium status-pending">
                    Не затверджено
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
