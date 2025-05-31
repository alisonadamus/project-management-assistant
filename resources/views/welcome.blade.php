<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Project Management Assistant') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>
<body class="font-['Figtree'] antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
<!-- Navigation -->
<nav class="bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-800 dark:to-purple-800 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center space-x-4">
                <x-application-logo class="h-10 w-auto text-white" />
                <span class="text-2xl font-bold text-white">Project Management Assistant</span>
            </div>
            <div class="flex items-center space-x-6">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2 bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-300 rounded-lg font-medium hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-200">
                            {{ __('Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-white font-medium hover:text-indigo-200 transition duration-200">
                            {{ __('Увійти') }}
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-5 py-2 bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-300 rounded-lg font-medium hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-200">
                                {{ __('Зареєструватися') }}
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="relative py-20 md:py-28 bg-gradient-to-b from-indigo-100 to-blue-100 dark:from-gray-800 dark:to-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center gap-8">
        <div class="md:w-1/2 text-center md:text-left">
            <h1 class="text-4xl md:text-4xl font-bold text-indigo-900 dark:text-white leading-tight">
                Платформа Управління Дослідницькою Діяльністю
            </h1>
            <p class="mt-4 text-lg md:text-xl text-gray-700 dark:text-gray-300">
                Співпраця між студентами та викладачами для ефективного керування науковими проєктами, спілкування та відстеження прогресу.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                <a href="{{ route('login') }}" class="px-8 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 dark:hover:bg-indigo-500 transition duration-200">
                    {{ __('Розпочати роботу') }}
                </a>
                <a href="{{ route('register') }}" class="px-8 py-3 bg-transparent border border-indigo-600 text-indigo-600 dark:text-indigo-300 rounded-lg font-medium hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-500 transition duration-200">
                    {{ __('Зареєструватися') }}
                </a>
            </div>
        </div>
        <div class="md:w-1/2 flex justify-center">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg w-full max-w-md">
                <p class="text-center text-gray-600 dark:text-gray-300 font-medium">
                    З’єднуємо студентів та викладачів для успішних наукових проєктів
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-20 bg-white dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-indigo-900 dark:text-white">
                Можливості Платформи
            </h2>
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-300">
                Інструменти для ефективної реалізації ваших наукових ідей
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Feature 1 -->
            <div class="bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-gray-700 dark:to-gray-800 p-6 rounded-xl shadow-md hover:shadow-lg transition duration-300">
                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-indigo-600 text-white mx-auto">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h3 class="mt-4 text-xl font-semibold text-indigo-900 dark:text-white text-center">Управління проєктами</h3>
                <p class="mt-3 text-gray-600 dark:text-gray-300 text-center">
                    Створення та управління подіями та науковими проєктами.
                </p>
            </div>
            <!-- Feature 2 -->
            <div class="bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-gray-700 dark:to-gray-800 p-6 rounded-xl shadow-md hover:shadow-lg transition duration-300">
                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-indigo-600 text-white mx-auto">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-xl font-semibold text-indigo-900 dark:text-white text-center">Комунікація</h3>
                <p class="mt-3 text-gray-600 dark:text-gray-300 text-center">
                    Вбудований чат для зручного спілкування між студентами та керівниками.
                </p>
            </div>
            <!-- Feature 3 -->
            <div class="bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-gray-700 dark:to-gray-800 p-6 rounded-xl shadow-md hover:shadow-lg transition duration-300">
                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-indigo-600 text-white mx-auto">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-xl font-semibold text-indigo-900 dark:text-white text-center">Подача заявок</h3>
                <p class="mt-3 text-gray-600 dark:text-gray-300 text-center">
                    Студенти подають заявки, викладачі їх розглядають та схвалюють.
                </p>
            </div>
            <!-- Feature 4 (New) -->
            <div class="bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-gray-700 dark:to-gray-800 p-6 rounded-xl shadow-md hover:shadow-lg transition duration-300">
                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-indigo-600 text-white mx-auto">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-xl font-semibold text-indigo-900 dark:text-white text-center">Відстеження часу</h3>
                <p class="mt-3 text-gray-600 dark:text-gray-300 text-center">
                    Контролюйте терміни та прогрес завдань для ефективного планування.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Access Information Section -->
<section class="py-20 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-900 dark:to-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-indigo-900 dark:text-white">
                Доступ до Системи
            </h2>
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-300">
                Ексклюзивно для спільноти Природничо-гуманітарного фахового коледжу УжНУ
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-3xl mx-auto">
            <div class="bg-white dark:bg-gray-700 p-6 rounded-xl shadow-md border-l-4 border-blue-500">
                <h3 class="text-xl font-semibold text-blue-900 dark:text-blue-100 mb-3">Для студентів</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    Реєструйтесь за допомогою email <strong>@student.uzhnu.edu.ua</strong> для доступу до проєктів.
                </p>
            </div>
            <div class="bg-white dark:bg-gray-700 p-6 rounded-xl shadow-md border-l-4 border-green-500">
                <h3 class="text-xl font-semibold text-green-900 dark:text-green-100 mb-3">Для викладачів</h3>
                <p class="text-gray-600 dark:text-gray-300">
                    Використовуйте email <strong>@uzhnu.edu.ua</strong> для керування та нагляду за проєктами.
                </p>
            </div>
        </div>
        <div class="mt-10 text-center">
            <a href="{{ route('register') }}" class="px-8 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 dark:hover:bg-indigo-500 transition duration-200">
                {{ __('Зареєструватися зараз') }}
            </a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-indigo-900 dark:bg-gray-900 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Logo and Description -->
            <div class="space-y-4">
                <div class="flex items-center space-x-3">
                    <x-application-logo class="h-10 w-auto text-white" />
                    <span class="text-xl font-bold text-white">ПГФК</span>
                </div>
                <p class="text-gray-300">
                    Платформа для управління науковими проєктами Природничо-гуманітарного фахового коледжу.
                </p>
            </div>
            <!-- Quick Links -->
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider">
                    Швидкі посилання
                </h3>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('login') }}" class="text-gray-300 hover:text-white transition duration-150">
                            Увійти в систему
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('register') }}" class="text-gray-300 hover:text-white transition duration-150">
                            Реєстрація
                        </a>
                    </li>
                    <li>
                        <a href="https://www.college.uzhnu.edu.ua/" target="_blank" class="text-gray-300 hover:text-white transition duration-150">
                            Офіційний сайт ПГФК
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Contact Info -->
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider">
                    Контакти
                </h3>
                <ul class="space-y-2 text-gray-300">
                    <li>Природничо-гуманітарний фаховий коледж</li>
                    <li>вул. Українська, 1</li>
                    <li>м. Ужгород, 88000</li>
                    <li>Україна</li>
                </ul>
            </div>
            <!-- Additional Note -->
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider">
                    Про нас
                </h3>
                <p class="text-gray-300">
                    Система створена для підтримки співпраці та ефективного управління науковими ініціативами.
                </p>
            </div>
        </div>
        <div class="mt-12 pt-8 border-t border-indigo-700 dark:border-gray-700 text-center">
            <p class="text-gray-300 text-sm">
                © {{ date('Y') }} Природничо-гуманітарний фаховий коледж. Всі права захищені.
            </p>
        </div>
    </div>
</footer>

@livewireScripts

<!-- Theme switching script -->
<script>
    function initTheme() {
        const theme = localStorage.getItem('theme') || 'system';
        applyTheme(theme);
    }

    function applyTheme(theme) {
        if (theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }

    initTheme();

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        if (localStorage.getItem('theme') === 'system') {
            initTheme();
        }
    });
</script>
</body>
</html>
