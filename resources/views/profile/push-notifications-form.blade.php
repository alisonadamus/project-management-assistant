<x-form-section submit="">
    <x-slot name="title">
        {{ __('Push сповіщення') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Налаштуйте push сповіщення для отримання миттєвих повідомлень про нові чат повідомлення навіть коли браузер закритий.') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6">
            <!-- Статус підтримки push сповіщень -->
            <div id="push-support-status" class="mb-4 p-4 rounded-lg border" style="display: none;">
                <div id="push-supported" class="hidden">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-green-700 dark:text-green-300">Push сповіщення підтримуються вашим браузером</span>
                    </div>
                </div>
                <div id="push-not-supported" class="hidden">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-red-700 dark:text-red-300">Push сповіщення не підтримуються вашим браузером</span>
                    </div>
                </div>
            </div>

            <!-- Перемикач push сповіщень -->
            <div class="flex items-center justify-between p-4 border rounded-lg">
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Push сповіщення про чат') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ __('Отримувати миттєві сповіщення про нові повідомлення в чаті проектів') }}
                    </p>
                </div>
                <div class="ml-4">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="push-notifications-toggle" class="sr-only peer" disabled>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>

            <!-- Статус підписки -->
            <div id="subscription-status" class="mt-4 p-3 rounded-lg" style="display: none;">
                <div id="subscription-active" class="hidden">
                    <div class="flex items-center text-green-700 dark:text-green-300">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm">Push сповіщення активні</span>
                    </div>
                </div>
                <div id="subscription-inactive" class="hidden">
                    <div class="flex items-center text-gray-600 dark:text-gray-400">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm">Push сповіщення неактивні</span>
                    </div>
                </div>
            </div>

            <!-- Інформація про використання -->
            <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-sm">
                        <h4 class="font-medium text-blue-800 dark:text-blue-200 mb-1">
                            {{ __('Як це працює?') }}
                        </h4>
                        <ul class="text-blue-700 dark:text-blue-300 space-y-1">
                            <li>• Push сповіщення працюють навіть коли браузер закритий</li>
                            <li>• Ви отримаєте сповіщення тільки про нові повідомлення в чаті ваших проектів</li>
                            <li>• Клік по сповіщенню відкриє відповідний проект</li>
                            <li>• Ви можете відключити сповіщення в будь-який час</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Повідомлення про помилки -->
            <div id="push-error-message" class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg hidden">
                <div class="flex">
                    <svg class="w-5 h-5 text-red-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-sm">
                        <h4 class="font-medium text-red-800 dark:text-red-200 mb-1">
                            {{ __('Помилка') }}
                        </h4>
                        <p id="push-error-text" class="text-red-700 dark:text-red-300"></p>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>
</x-form-section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Перевіряємо підтримку push сповіщень
    const supportStatus = document.getElementById('push-support-status');
    const supported = document.getElementById('push-supported');
    const notSupported = document.getElementById('push-not-supported');
    const toggle = document.getElementById('push-notifications-toggle');
    
    if ('serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window) {
        supported.classList.remove('hidden');
        supportStatus.classList.remove('border-gray-200');
        supportStatus.classList.add('border-green-200', 'bg-green-50', 'dark:bg-green-900/20');
        toggle.disabled = false;
    } else {
        notSupported.classList.remove('hidden');
        supportStatus.classList.remove('border-gray-200');
        supportStatus.classList.add('border-red-200', 'bg-red-50', 'dark:bg-red-900/20');
        toggle.disabled = true;
    }
    
    supportStatus.style.display = 'block';
    
    // Перевіряємо поточний статус підписки
    if (window.pushNotificationManager) {
        updateSubscriptionStatus();
    }
    
    // Функція оновлення статусу підписки
    function updateSubscriptionStatus() {
        const statusDiv = document.getElementById('subscription-status');
        const activeDiv = document.getElementById('subscription-active');
        const inactiveDiv = document.getElementById('subscription-inactive');
        
        if (window.pushNotificationManager && window.pushNotificationManager.isEnabled) {
            activeDiv.classList.remove('hidden');
            inactiveDiv.classList.add('hidden');
            statusDiv.classList.add('bg-green-50', 'dark:bg-green-900/20');
            statusDiv.classList.remove('bg-gray-50', 'dark:bg-gray-800/20');
            toggle.checked = true;
        } else {
            activeDiv.classList.add('hidden');
            inactiveDiv.classList.remove('hidden');
            statusDiv.classList.add('bg-gray-50', 'dark:bg-gray-800/20');
            statusDiv.classList.remove('bg-green-50', 'dark:bg-green-900/20');
            toggle.checked = false;
        }
        
        statusDiv.style.display = 'block';
    }
    
    // Функція показу помилки
    function showError(message) {
        const errorDiv = document.getElementById('push-error-message');
        const errorText = document.getElementById('push-error-text');
        
        errorText.textContent = message;
        errorDiv.classList.remove('hidden');
        
        // Автоматично приховуємо через 5 секунд
        setTimeout(() => {
            errorDiv.classList.add('hidden');
        }, 5000);
    }
    
    // Оновлюємо статус кожні 2 секунди
    setInterval(updateSubscriptionStatus, 2000);
});
</script>
