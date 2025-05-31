@props(['event'])

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg hover:shadow-lg transition-shadow duration-300 flex flex-col h-full">
    @if($event->image_url)
        <div class="relative h-48 overflow-hidden">
            <img src="{{ $event->image_url }}" alt="{{ $event->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('storage/events/' . basename($event->image)) }}';">
            <div class="absolute bottom-0 left-0 right-0 p-2 bg-gradient-to-t from-black/70 to-transparent">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                      style="background-color: {{ $event->bg_color ?? '#4F46E5' }}; color: {{ $event->fg_color ?? '#ffffff' }}">
                    {{ $event->category->name }}
                </span>
            </div>
        </div>
    @else
        <div class="h-24 flex items-center justify-center" style="background-color: {{ $event->bg_color ?? '#4F46E5' }}">
            <span class="text-xl font-bold" style="color: {{ $event->fg_color ?? '#ffffff' }}">
                {{ $event->category->name }}
            </span>
        </div>
    @endif
    
    <div class="p-4 flex-grow">
        <div class="flex justify-between items-start mb-2">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $event->name }}</h3>
        </div>
        
        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 line-clamp-3">
            {{ $event->description ?? 'Немає опису' }}
        </p>
        
        <div class="mt-auto">
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Початок: {{ \Carbon\Carbon::parse($event->start_date)->format('d.m.Y H:i') }}</span>
            </div>
            
            @if($event->end_date)
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Завершення: {{ \Carbon\Carbon::parse($event->end_date)->format('d.m.Y H:i') }}</span>
                </div>
            @endif
            
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Проектів: {{ $event->projects->count() }}</span>
            </div>
        </div>
    </div>
    
    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 text-right">
        <a href="{{ route('events.show', $event) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 active:bg-indigo-700 transition ease-in-out duration-150">
            Детальніше
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
        </a>
    </div>
</div>
