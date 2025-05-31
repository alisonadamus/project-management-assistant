@props(['project'])

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg hover:shadow-lg transition-shadow duration-300 flex flex-col h-full">
    <div class="p-4 flex-grow">
        <div class="flex justify-between items-start mb-2">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $project->name }}</h3>
            @if($project->assigned_to)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                    Затверджено
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                    Не затверджено
                </span>
            @endif
        </div>
        
        @if($project->technologies->isNotEmpty())
            <div class="mt-2 flex flex-wrap gap-1">
                @foreach($project->technologies as $tech)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        {{ $tech->name }}
                    </span>
                @endforeach
            </div>
        @endif
        
        <div class="mt-4 text-sm text-gray-600 dark:text-gray-300 line-clamp-3">
            {{ Str::limit($project->body, 150) ?? 'Немає опису' }}
        </div>
        
        <div class="mt-4">
            @if($project->supervisor)
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Керівник: {{ $project->supervisor->user->name }}</span>
                </div>
            @endif
            
            @if($project->assigned_to)
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Студент: {{ $project->assignedTo->name }}</span>
                </div>
            @endif
            
            @if($project->event)
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Подія: {{ $project->event->name }}</span>
                </div>
            @endif
        </div>
    </div>
    
    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 text-right">
        <a href="{{ route('projects.show', $project) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white btn-gradient hover:shadow-md focus:outline-none focus:border-primary-700 focus:ring focus:ring-primary-200 transition ease-in-out duration-150">
            Детальніше
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
        </a>
    </div>
</div>
