@props(['subject'])

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-lg hover:shadow-lg transition-shadow duration-300 flex flex-col h-full card-border-gradient">
    @if($subject->image)
        <div class="relative h-48 overflow-hidden">
            @php
                // Remove any leading slash to avoid double slashes
                $imagePath = ltrim($subject->image, '/');
                // If path starts with storage/, replace it with /storage/
                if (str_starts_with($imagePath, 'storage/')) {
                    $imagePath = '/storage/' . substr($imagePath, 8);
                }
            @endphp
            <img src="{{ $imagePath }}" alt="{{ $subject->name }}" class="w-full h-full object-cover">
            <div class="absolute bottom-0 left-0 right-0 p-2 bg-gradient-to-t from-black/70 to-transparent">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200">
                    {{ $subject->course_number }} курс
                </span>
            </div>
        </div>
    @else
        <div class="h-24 flex items-center justify-center card-gradient">
            <span class="text-xl font-bold text-white">
                {{ $subject->course_number }} курс
            </span>
        </div>
    @endif

    <div class="p-4 flex-grow">
        <div class="flex justify-between items-start mb-2">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $subject->name }}</h3>
        </div>

        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4 line-clamp-3">
            {{ $subject->description_preview ?? 'Немає опису' }}
        </p>

        <div class="mt-auto">
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <span>Категорій: {{ $subject->categories->count() }}</span>
            </div>
        </div>
    </div>

    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 text-right">
        <a href="{{ route('subjects.show', $subject) }}" class="inline-flex items-center px-4 py-2 btn-gradient border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Детальніше
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
        </a>
    </div>
</div>
