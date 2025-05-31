@props(['message'])

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg hover:shadow-lg transition-shadow duration-300 h-full">
    <!-- Message Header -->
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <!-- Sender Avatar -->
                <div class="flex-shrink-0">
                    @if($message->sender->profile_photo_path)
                        <img class="h-8 w-8 rounded-full object-cover"
                             src="{{ $message->sender->profile_photo_url }}"
                             alt="{{ $message->sender->name }}">
                    @else
                        <div class="h-8 w-8 rounded-full card-gradient flex items-center justify-center">
                            <span class="text-sm font-medium text-white">
                                {{ substr($message->sender->name, 0, 1) }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Sender Info -->
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ $message->sender->name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $message->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>

            <!-- Unread Badge -->
            @if(!$message->is_read)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium badge-gradient text-white">
                    Нове
                </span>
            @endif
        </div>
    </div>

    <!-- Message Content -->
    <div class="p-4">
        <!-- Project Info -->
        <div class="mb-3">
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h12a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1V8z" clip-rule="evenodd"></path>
                </svg>
                Проект: {{ $message->project->name }}
            </div>
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mt-1">
                <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                </svg>
                Подія: {{ $message->project->event->name }}
            </div>
        </div>

        <!-- Message Text -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 mb-4">
            <p class="text-sm text-gray-900 dark:text-gray-100 leading-relaxed">
                {{ Str::limit($message->message, 150) }}
            </p>
        </div>

        <!-- Action Button -->
        <div class="flex justify-end">
            <a href="{{ route('projects.show', $message->project) }}#messages"
               class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white btn-gradient hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200">
                Відповісти
                <svg class="ml-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </a>
        </div>
    </div>
</div>
