<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Останні повідомлення -->
            <div class="section-gradient overflow-hidden shadow-xl sm:rounded-lg page-container-gradient">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                            <svg class="mr-2 h-5 w-5 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                            </svg>
                            Останні повідомлення
                        </h3>
                        @if($unreadMessages->count() > 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium badge-gradient text-white">
                                {{ $unreadMessages->count() }} нових
                            </span>
                        @endif
                    </div>

                    @if($unreadMessages->count() > 0)
                        <x-carousel :items="$unreadMessages->map(function($message) {
                            return view('components.message-card', ['message' => $message])->render();
                        })->toArray()"
                        id="messages-carousel"
                        :autoplay="true"
                        :interval="6000" />
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8V4a1 1 0 00-1-1H7a1 1 0 00-1 1v1m8 0V4.5"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Немає нових повідомлень</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Коли з'являться нові повідомлення, вони будуть відображені тут.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Події -->
            <div class="section-gradient overflow-hidden shadow-xl sm:rounded-lg page-container-gradient">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                            <svg class="mr-2 h-5 w-5 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            Активні події
                        </h3>
                        @if($activeEvents->count() > 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium badge-gradient text-white">
                                {{ $activeEvents->count() }} активних
                            </span>
                        @endif
                    </div>

                    @if($activeEvents->count() > 0)
                        <x-carousel :items="$activeEvents->map(function($event) use ($projectsInfo) {
                            return view('components.dashboard-event-card', ['event' => $event, 'projectsInfo' => $projectsInfo])->render();
                        })->toArray()"
                        id="events-carousel"
                        :autoplay="true"
                        :interval="8000" />
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8a4 4 0 11-8 0v-5a4 4 0 014-4h4a4 4 0 014 4v5a4 4 0 11-8 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Немає активних подій</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Наразі немає подій, доступних для вас.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Додаткова статистика для адміністраторів -->
            @if($projectsInfo['type'] === 'admin')
                <div class="section-gradient overflow-hidden shadow-xl sm:rounded-lg page-container-gradient">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6 flex items-center">
                            <svg class="mr-2 h-5 w-5 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                                <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                            </svg>
                            Загальна статистика
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <div class="text-center">
                                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $projectsInfo['statistics']['total_projects'] }}</p>
                                    <p class="text-sm text-blue-600 dark:text-blue-400">Всього проектів</p>
                                </div>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                                <div class="text-center">
                                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $projectsInfo['statistics']['assigned_projects'] }}</p>
                                    <p class="text-sm text-green-600 dark:text-green-400">Призначених</p>
                                </div>
                            </div>
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                <div class="text-center">
                                    <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $projectsInfo['statistics']['pending_offers'] }}</p>
                                    <p class="text-sm text-yellow-600 dark:text-yellow-400">Заявок</p>
                                </div>
                            </div>
                            <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
                                <div class="text-center">
                                    <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $projectsInfo['statistics']['active_events'] }}</p>
                                    <p class="text-sm text-purple-600 dark:text-purple-400">Активних подій</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
