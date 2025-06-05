<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-mark class="block h-9 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Головна') }}
                    </x-nav-link>

                    <!-- Події Dropdown -->
                    <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <x-nav-link href="#" :active="request()->routeIs('events.*')" class="inline-flex items-center">
                                    <div>{{ __('Події') }}</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </x-nav-link>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link href="{{ route('events.current') }}">
                                    {{ __('Активні події') }}
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('events.upcoming') }}">
                                    {{ __('Майбутні події') }}
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('events.archived') }}">
                                    {{ __('Архівовані події') }}
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('events.index') }}">
                                    {{ __('Всі події') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>

                    <!-- Проекти Dropdown -->
                    <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <x-nav-link href="#" :active="request()->routeIs('projects.*')" class="inline-flex items-center">
                                    <div>{{ __('Проекти') }}</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </x-nav-link>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link href="{{ route('projects.index') }}">
                                    {{ __('Мої проекти') }}
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ auth()->user()->hasRole('teacher') ? route('teacher.offers.index') : route('offers.index') }}">
                                    {{ __('Мої заявки') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>

                    <!-- Матеріали Dropdown -->
                    <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <x-nav-link href="#" :active="request()->routeIs('categories.*') || request()->routeIs('subjects.*') || request()->routeIs('technologies.*')" class="inline-flex items-center">
                                    <div>{{ __('Матеріали') }}</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </x-nav-link>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link href="{{ route('categories.index') }}">
                                    {{ __('Категорії') }}
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('subjects.index') }}">
                                    {{ __('Предмети') }}
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('technologies.index') }}">
                                    {{ __('Технології') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Theme Switchers -->
                <div class="ms-3 relative flex items-center space-x-3">
                    <!-- Color Theme Switcher -->
                    <div class="relative">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition duration-150 ease-in-out">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="p-3">
                                    <div class="text-xs text-gray-400 mb-2">{{ __('Кольорова тема') }}</div>
                                    <x-color-theme-switcher />
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    <!-- Dark/Light Theme Switcher -->
                    <div class="relative">
                        <x-theme-switcher />
                    </div>
                </div>

                <!-- Teams Dropdown -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="ms-3 relative">
                        <x-dropdown align="right" width="60">
                            <x-slot name="trigger">
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                        {{ Auth::user()->currentTeam->name }}

                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </button>
                                </span>
                            </x-slot>

                            <x-slot name="content">
                                <div class="w-60">
                                    <!-- Team Management -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Manage Team') }}
                                    </div>

                                    <!-- Team Settings -->
                                    <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                        {{ __('Team Settings') }}
                                    </x-dropdown-link>

                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        <x-dropdown-link href="{{ route('teams.create') }}">
                                            {{ __('Create New Team') }}
                                        </x-dropdown-link>
                                    @endcan

                                    <!-- Team Switcher -->
                                    @if (Auth::user()->allTeams()->count() > 1)
                                        <div class="border-t border-gray-200 dark:border-gray-600"></div>

                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            {{ __('Switch Teams') }}
                                        </div>

                                        @foreach (Auth::user()->allTeams() as $team)
                                            <x-switchable-team :team="$team" />
                                        @endforeach
                                    @endif
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif

                <!-- Settings Dropdown -->
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                    <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->full_name }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                        {{ Auth::user()->full_name }}

                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Управління акаунтом') }}
                            </div>

                            <x-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Профіль') }}
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    {{ __('API Токени') }}
                                </x-dropdown-link>
                            @endif

                            @if (auth()->user()->hasRole('admin'))
                                <x-dropdown-link href="/admin">
                                    {{ __('Адмін панель') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-200 dark:border-gray-600"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}"
                                         @click.prevent="$root.submit();">
                                    {{ __('Вийти') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                {{ __('Головна') }}
            </x-responsive-nav-link>

            <!-- Події розділ -->
            <div x-data="{ open: false }" class="border-t border-gray-200 dark:border-gray-600">
                <button @click="open = ! open" class="w-full flex items-center ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out">
                    <div>{{ __('Події') }}</div>
                    <div class="ms-auto">
                        <svg class="fill-current h-4 w-4 transform" :class="{'rotate-180': open}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </button>
                <div x-show="open" class="mt-2 space-y-1 ps-4">
                    <x-responsive-nav-link href="{{ route('events.current') }}" :active="request()->routeIs('events.current')">
                        {{ __('Активні події') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('events.upcoming') }}" :active="request()->routeIs('events.upcoming')">
                        {{ __('Майбутні події') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('events.archived') }}" :active="request()->routeIs('events.archived')">
                        {{ __('Архівовані події') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('events.index') }}" :active="request()->routeIs('events.index')">
                        {{ __('Всі події') }}
                    </x-responsive-nav-link>
                </div>
            </div>

            <!-- Проекти розділ -->
            <div x-data="{ open: false }" class="border-t border-gray-200 dark:border-gray-600">
                <button @click="open = ! open" class="w-full flex items-center ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out">
                    <div>{{ __('Проекти') }}</div>
                    <div class="ms-auto">
                        <svg class="fill-current h-4 w-4 transform" :class="{'rotate-180': open}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </button>
                <div x-show="open" class="mt-2 space-y-1 ps-4">
                    <x-responsive-nav-link href="{{ route('projects.index') }}" :active="request()->routeIs('projects.index')">
                        {{ __('Мої проекти') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ auth()->user()->hasRole('teacher') ? route('teacher.offers.index') : route('offers.index') }}" :active="request()->routeIs(['offers.index', 'teacher.offers.index'])">
                        {{ __('Мої заявки') }}
                    </x-responsive-nav-link>
                </div>
            </div>

            <!-- Матеріали розділ -->
            <div x-data="{ open: false }" class="border-t border-gray-200 dark:border-gray-600">
                <button @click="open = ! open" class="w-full flex items-center ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out">
                    <div>{{ __('Матеріали') }}</div>
                    <div class="ms-auto">
                        <svg class="fill-current h-4 w-4 transform" :class="{'rotate-180': open}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </button>
                <div x-show="open" class="mt-2 space-y-1 ps-4">
                    <x-responsive-nav-link href="{{ route('categories.index') }}" :active="request()->routeIs('categories.index')">
                        {{ __('Категорії') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('subjects.index') }}" :active="request()->routeIs('subjects.index')">
                        {{ __('Предмети') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('technologies.index') }}" :active="request()->routeIs('technologies.index')">
                        {{ __('Технології') }}
                    </x-responsive-nav-link>
                </div>
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 me-3">
                        <img class="size-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->full_name }}" />
                    </div>
                @endif

                <div>
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->full_name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Theme Switchers -->
                <div class="px-4 py-2">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ __('Тема') }}:</div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400 mr-2">{{ __('Світла/Темна') }}:</span>
                            <x-theme-switcher />
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ __('Кольорова тема') }}:</div>
                        <x-color-theme-switcher />
                    </div>
                </div>

                <!-- Account Management -->
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ __('Профіль') }}
                </x-responsive-nav-link>

                @if (auth()->user()->hasRole('admin'))
                    <x-responsive-nav-link href="/admin">
                        {{ __('Адмін панель') }}
                    </x-responsive-nav-link>
                @endif

                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
                        {{ __('API Токени') }}
                    </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf

                    <x-responsive-nav-link href="{{ route('logout') }}"
                                   @click.prevent="$root.submit();">
                        {{ __('Вийти') }}
                    </x-responsive-nav-link>
                </form>

                <!-- Team Management -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="border-t border-gray-200 dark:border-gray-600"></div>

                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Team') }}
                    </div>

                    <!-- Team Settings -->
                    <x-responsive-nav-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" :active="request()->routeIs('teams.show')">
                        {{ __('Team Settings') }}
                    </x-responsive-nav-link>

                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                        <x-responsive-nav-link href="{{ route('teams.create') }}" :active="request()->routeIs('teams.create')">
                            {{ __('Create New Team') }}
                        </x-responsive-nav-link>
                    @endcan

                    <!-- Team Switcher -->
                    @if (Auth::user()->allTeams()->count() > 1)
                        <div class="border-t border-gray-200 dark:border-gray-600"></div>

                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Switch Teams') }}
                        </div>

                        @foreach (Auth::user()->allTeams() as $team)
                            <x-switchable-team :team="$team" component="responsive-nav-link" />
                        @endforeach
                    @endif
                @endif
            </div>
        </div>
    </div>
</nav>
