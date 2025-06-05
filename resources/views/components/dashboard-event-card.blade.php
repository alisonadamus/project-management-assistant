@props(['event', 'projectsInfo'])

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg hover:shadow-lg transition-shadow duration-300 h-full card-border-gradient">
    <!-- Event Header -->
    <div class="relative">
        @if($event->image_url)
            <div class="h-32 overflow-hidden">
                <img src="{{ $event->image_url }}" alt="{{ $event->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('storage/events/' . basename($event->image)) }}';">
            </div>
        @else
            <div class="h-32 card-gradient flex items-center justify-center">
                <h3 class="text-lg font-bold text-white text-center px-4">{{ $event->name }}</h3>
            </div>
        @endif
        
        <!-- Event Status Badge -->
        <div class="absolute top-2 right-2">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium badge-gradient text-white">
                Активна
            </span>
        </div>
    </div>

    <div class="p-6">
        <!-- Event Title (if image exists) -->
        @if($event->image)
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                {{ $event->name }}
            </h3>
        @endif

        <!-- Event Dates -->
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-4">
            <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
            </svg>
            {{ $event->start_date->format('d.m.Y') }} - {{ $event->end_date->format('d.m.Y') }}
        </div>

        <!-- Category Info -->
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-4">
            <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h12a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1V8z" clip-rule="evenodd"></path>
            </svg>
            {{ $event->category->name }} ({{ $event->category->course_number }} курс)
        </div>

        <!-- Role-specific Information -->
        @if($projectsInfo['type'] === 'student')
            @php
                $userProjects = $projectsInfo['assigned_projects']->where('event_id', $event->id);
                $userOffers = $projectsInfo['pending_offers']->where('project.event_id', $event->id);
            @endphp
            
            @if($userProjects->count() > 0)
                <!-- Student has assigned project -->
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 mb-4">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                Проект призначено: {{ $userProjects->first()->name }}
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($userOffers->count() > 0)
                <!-- Student has pending offers -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3 mb-4">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                Заявок на розгляді: {{ $userOffers->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Student has no project or offers -->
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 mb-4">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                Доступно {{ $event->projects->whereNull('assigned_to')->count() }} проектів
                            </p>
                        </div>
                    </div>
                </div>
            @endif

        @elseif($projectsInfo['type'] === 'teacher')
            <!-- Teacher View -->
            @php
                $eventProjects = $projectsInfo['supervised_projects']->where('event_id', $event->id);
                $eventOffers = $projectsInfo['pending_offers']->where('project.event_id', $event->id);
            @endphp
            
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $eventProjects->count() }}</p>
                        <p class="text-xs text-blue-600 dark:text-blue-400">Проектів</p>
                    </div>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $eventOffers->count() }}</p>
                        <p class="text-xs text-green-600 dark:text-green-400">Заявок</p>
                    </div>
                </div>
            </div>

        @elseif($projectsInfo['type'] === 'admin')
            <!-- Admin View -->
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-3">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $event->projects->count() }}</p>
                        <p class="text-xs text-purple-600 dark:text-purple-400">Всього проектів</p>
                    </div>
                </div>
                <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-3">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $event->supervisors->count() }}</p>
                        <p class="text-xs text-indigo-600 dark:text-indigo-400">Керівників</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Action Button -->
        <div class="mt-4">
            <a href="{{ route('events.show', $event) }}" 
               class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white btn-gradient hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200">
                Переглянути подію
                <svg class="ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </a>
        </div>
    </div>
</div>
