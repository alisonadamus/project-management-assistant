@props(['technology'])

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg hover:shadow-lg transition-shadow duration-300 flex flex-col h-full">
    @if($technology->image)
        <div class="relative h-48 overflow-hidden">
            @php
                // Видаляємо будь-який початковий слеш, щоб уникнути подвійних слешів
                $imagePath = ltrim($technology->image, '/');
                // Якщо шлях починається з storage/, замінюємо його на /storage/
                if (str_starts_with($imagePath, 'storage/')) {
                    $imagePath = '/storage/' . substr($imagePath, 8);
                }
            @endphp
            <img src="{{ $imagePath }}" alt="{{ $technology->name }}" class="w-full h-full object-cover">
            <div class="absolute bottom-0 left-0 right-0 p-2 bg-gradient-to-t from-black/70 to-transparent">
                @if($technology->link)
                    <a href="{{ $technology->link }}" target="_blank" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 hover:bg-blue-200 dark:hover:bg-blue-800 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        Документація
                    </a>
                @endif
            </div>
        </div>
    @else
        <div class="h-24 flex items-center justify-center card-gradient">
            <span class="text-xl font-bold text-white">
                {{ $technology->name }}
            </span>
            @if($technology->link)
                <a href="{{ $technology->link }}" target="_blank" class="absolute top-2 right-2 inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-white text-blue-800 hover:bg-gray-100 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Документація
                </a>
            @endif
        </div>
    @endif

    <div class="p-4 flex-grow">
        <div class="flex justify-between items-start mb-2">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $technology->name }}</h3>
        </div>

        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 line-clamp-3">
            {{ $technology->description ?? 'Немає опису' }}
        </p>

        <div class="mt-auto">
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>Проектів: {{ $technology->projects->count() }}</span>
            </div>
        </div>
    </div>

    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 text-right">
        <a href="{{ route('technologies.show', $technology) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white btn-gradient hover:shadow-md focus:outline-none focus:border-primary-700 focus:ring focus:ring-primary-200 transition ease-in-out duration-150">
            Детальніше
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
        </a>
    </div>
</div>
