<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                @if(auth()->user()->hasRole('admin'))
                    {{ __('Всі заявки') }}
                @else
                    {{ __('Мої заявки') }}
                @endif
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
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

                @if($offers->isEmpty())
                    <div class="text-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">Заявок не знайдено</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            @if(auth()->user()->hasRole('admin'))
                                В системі ще немає жодної заявки на проекти.
                            @else
                                Ви ще не подали жодної заявки на проекти.
                            @endif
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('events.current') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white btn-gradient hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                Переглянути активні події
                            </a>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($offers as $offer)
                            <x-offer-card :offer="$offer" />
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $offers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
