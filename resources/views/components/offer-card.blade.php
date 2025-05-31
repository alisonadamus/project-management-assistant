@props(['offer'])

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg hover:shadow-lg transition-shadow duration-300 flex flex-col h-full">
    <div class="p-4 flex-grow">
        <div class="flex justify-between items-start mb-2">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $offer->project->name }}</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                {{ $offer->created_at->format('d.m.Y') }}
            </span>
        </div>

        @if($offer->project->technologies->isNotEmpty())
            <div class="mt-2 flex flex-wrap gap-1">
                @foreach($offer->project->technologies as $tech)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        {{ $tech->name }}
                    </span>
                @endforeach
            </div>
        @endif

        <div class="mt-4 text-sm text-gray-600 dark:text-gray-300 line-clamp-3">
            {{ Str::limit($offer->project->body, 150) ?? 'Немає опису' }}
        </div>

        <div class="mt-4">
            @if($offer->project->supervisor)
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Керівник: {{ $offer->project->supervisor->user->name }}</span>
                </div>
            @endif

            @if(auth()->user()->hasRole('admin') && isset($offer->student))
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Студент: {{ $offer->student->name }}</span>
                </div>
            @endif

            @if($offer->project->event)
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Подія: {{ $offer->project->event->name }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 flex justify-between items-center">
        <a href="{{ route('projects.show', $offer->project) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white btn-gradient hover:shadow-md focus:outline-none focus:border-primary-700 focus:ring focus:ring-primary-200 transition ease-in-out duration-150">
            Деталі проекту
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
        </a>

        <form action="{{ route('offers.destroy', $offer->project) }}" method="POST" class="inline-block">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Скасувати заявку
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </form>
    </div>
</div>
