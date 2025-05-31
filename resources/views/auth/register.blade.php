<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Реєстрація дозволена лише з доменами @student.uzhnu.edu.ua або @uzhnu.edu.ua') }}
        </div>

        <form method="POST" action="{{ route('register') }}" x-data="{
            email: '',
            first_name: '',
            last_name: '',
            middle_name: '',
            course_number: null,
            isStudent: false,
            isValidDomain: false,

            checkEmail() {
                const email = this.email;
                const studentDomain = '@student.uzhnu.edu.ua';
                const teacherDomain = '@uzhnu.edu.ua';

                this.isStudent = email.endsWith(studentDomain);
                this.isValidDomain = email.endsWith(studentDomain) || email.endsWith(teacherDomain);

                if (this.isValidDomain) {
                    this.extractNameFromEmail();
                }
            },

            extractNameFromEmail() {
                const email = this.email;
                const username = email.split('@')[0];
                const parts = username.split('.');

                if (parts.length >= 2) {
                    this.first_name = this.capitalizeFirstLetter(parts[0]);
                    this.last_name = this.capitalizeFirstLetter(parts[1]);

                    if (parts.length >= 3) {
                        this.middle_name = this.capitalizeFirstLetter(parts[2]);
                    } else {
                        this.middle_name = '';
                    }
                } else {
                    this.first_name = this.capitalizeFirstLetter(username);
                    this.last_name = '';
                    this.middle_name = '';
                }

                // Додаємо повідомлення про автоматичне заповнення
                setTimeout(() => {
                    const infoMessage = document.getElementById('auto-fill-info');
                    if (infoMessage) {
                        infoMessage.style.display = 'block';
                        setTimeout(() => {
                            infoMessage.style.opacity = '1';
                        }, 100);

                        // Сховати повідомлення через 5 секунд
                        setTimeout(() => {
                            infoMessage.style.opacity = '0';
                            setTimeout(() => {
                                infoMessage.style.display = 'none';
                            }, 500);
                        }, 5000);
                    }
                }, 100);
            },

            capitalizeFirstLetter(string) {
                if (!string) return '';
                return string.charAt(0).toUpperCase() + string.slice(1);
            }
        }">
            @csrf

            <div>
                <x-label for="name" value="{{ __('Логін') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                    x-model="email"
                    x-on:blur="checkEmail"
                    required autocomplete="username" />
                <div x-show="email && !isValidDomain" class="text-sm text-red-600 mt-1">
                    {{ __('Дозволена реєстрація лише з доменами @student.uzhnu.edu.ua або @uzhnu.edu.ua') }}
                </div>
                <div id="auto-fill-info" class="text-sm text-green-600 mt-1 transition-opacity duration-500" style="display: none; opacity: 0;">
                    {{ __('Ім\'я, прізвище та по батькові будуть автоматично заповнені з вашого email.') }}
                </div>
            </div>

            <!-- Приховані поля для автоматичного заповнення -->
            <input type="hidden" id="first_name" name="first_name" x-model="first_name" />
            <input type="hidden" id="last_name" name="last_name" x-model="last_name" />
            <input type="hidden" id="middle_name" name="middle_name" x-model="middle_name" />

            <div class="mt-4" x-show="isStudent">
                <x-label for="course_number" value="{{ __('Курс') }}" />
                <select id="course_number" name="course_number" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
                    <option value="">{{ __('Виберіть курс') }}</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Пароль') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Підтвердження паролю') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('Я погоджуюсь з :terms_of_service та :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Умовами використання').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">'.__('Політикою конфіденційності').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                    {{ __('Вже зареєстровані?') }}
                </a>

                <x-button class="ms-4" x-bind:disabled="email && !isValidDomain">
                    {{ __('Зареєструватися') }}
                </x-button>
            </div>
        </form>

        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ __('Або зареєструйтесь через') }}</p>
            <a href="{{ route('google.redirect') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <g transform="matrix(1, 0, 0, 1, 27.009001, -39.238998)">
                        <path fill="#4285F4" d="M -3.264 51.509 C -3.264 50.719 -3.334 49.969 -3.454 49.239 L -14.754 49.239 L -14.754 53.749 L -8.284 53.749 C -8.574 55.229 -9.424 56.479 -10.684 57.329 L -10.684 60.329 L -6.824 60.329 C -4.564 58.239 -3.264 55.159 -3.264 51.509 Z" />
                        <path fill="#34A853" d="M -14.754 63.239 C -11.514 63.239 -8.804 62.159 -6.824 60.329 L -10.684 57.329 C -11.764 58.049 -13.134 58.489 -14.754 58.489 C -17.884 58.489 -20.534 56.379 -21.484 53.529 L -25.464 53.529 L -25.464 56.619 C -23.494 60.539 -19.444 63.239 -14.754 63.239 Z" />
                        <path fill="#FBBC05" d="M -21.484 53.529 C -21.734 52.809 -21.864 52.039 -21.864 51.239 C -21.864 50.439 -21.724 49.669 -21.484 48.949 L -21.484 45.859 L -25.464 45.859 C -26.284 47.479 -26.754 49.299 -26.754 51.239 C -26.754 53.179 -26.284 54.999 -25.464 56.619 L -21.484 53.529 Z" />
                        <path fill="#EA4335" d="M -14.754 43.989 C -12.984 43.989 -11.404 44.599 -10.154 45.789 L -6.734 42.369 C -8.804 40.429 -11.514 39.239 -14.754 39.239 C -19.444 39.239 -23.494 41.939 -25.464 45.859 L -21.484 48.949 C -20.534 46.099 -17.884 43.989 -14.754 43.989 Z" />
                    </g>
                </svg>
                {{ __('Google') }}
            </a>
        </div>
    </x-authentication-card>
</x-guest-layout>
