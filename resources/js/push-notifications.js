/**
 * Менеджер Push сповіщень для чат повідомлень
 */
class PushNotificationManager {
    constructor() {
        this.vapidPublicKey = null;
        this.registration = null;
        this.isSupported = this.checkSupport();
        this.isEnabled = false;
        
        this.init();
    }

    /**
     * Перевірка підтримки push сповіщень
     */
    checkSupport() {
        return 'serviceWorker' in navigator && 
               'PushManager' in window && 
               'Notification' in window;
    }

    /**
     * Ініціалізація менеджера
     */
    async init() {
        if (!this.isSupported) {
            console.log('Push сповіщення не підтримуються цим браузером');
            return;
        }

        try {
            // Отримуємо VAPID ключ з мета-тегу
            const vapidMeta = document.querySelector('meta[name="vapid-public-key"]');
            if (!vapidMeta) {
                console.error('VAPID публічний ключ не знайдено в мета-тегах');
                return;
            }
            
            this.vapidPublicKey = vapidMeta.content;
            
            // Реєструємо Service Worker
            await this.registerServiceWorker();
            
            // Перевіряємо поточний статус дозволів
            await this.checkPermissionStatus();
            
            // Ініціалізуємо UI
            this.initializeUI();
            
        } catch (error) {
            console.error('Помилка ініціалізації push сповіщень:', error);
        }
    }

    /**
     * Реєстрація Service Worker
     */
    async registerServiceWorker() {
        try {
            this.registration = await navigator.serviceWorker.register('/sw.js', {
                scope: '/'
            });
            
            console.log('Service Worker зареєстровано:', this.registration);
            
            // Чекаємо, поки Service Worker стане активним
            if (this.registration.installing) {
                await this.waitForServiceWorker(this.registration.installing);
            } else if (this.registration.waiting) {
                await this.waitForServiceWorker(this.registration.waiting);
            }
            
        } catch (error) {
            console.error('Помилка реєстрації Service Worker:', error);
            throw error;
        }
    }

    /**
     * Очікування активації Service Worker
     */
    waitForServiceWorker(worker) {
        return new Promise((resolve) => {
            worker.addEventListener('statechange', () => {
                if (worker.state === 'activated') {
                    resolve();
                }
            });
        });
    }

    /**
     * Перевірка статусу дозволів
     */
    async checkPermissionStatus() {
        const permission = await Notification.requestPermission();
        this.isEnabled = permission === 'granted';
        
        if (this.isEnabled) {
            // Перевіряємо, чи є активна підписка
            const subscription = await this.registration.pushManager.getSubscription();
            if (subscription) {
                console.log('Активна push підписка знайдена');
            }
        }
    }

    /**
     * Запит дозволу на сповіщення
     */
    async requestPermission() {
        if (!this.isSupported) {
            throw new Error('Push сповіщення не підтримуються');
        }

        const permission = await Notification.requestPermission();
        
        if (permission === 'granted') {
            this.isEnabled = true;
            await this.subscribeToPush();
            return true;
        } else if (permission === 'denied') {
            this.isEnabled = false;
            throw new Error('Дозвіл на сповіщення відхилено');
        } else {
            this.isEnabled = false;
            throw new Error('Дозвіл на сповіщення не надано');
        }
    }

    /**
     * Підписка на push сповіщення
     */
    async subscribeToPush() {
        try {
            const subscription = await this.registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(this.vapidPublicKey)
            });

            console.log('Push підписка створена:', subscription);

            // Відправляємо підписку на сервер
            await this.sendSubscriptionToServer(subscription);
            
            return subscription;
            
        } catch (error) {
            console.error('Помилка створення push підписки:', error);
            throw error;
        }
    }

    /**
     * Відправка підписки на сервер
     */
    async sendSubscriptionToServer(subscription) {
        try {
            const response = await fetch('/push-subscriptions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(subscription)
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Помилка збереження підписки');
            }

            const result = await response.json();
            console.log('Підписка збережена на сервері:', result);
            
        } catch (error) {
            console.error('Помилка відправки підписки на сервер:', error);
            throw error;
        }
    }

    /**
     * Відписка від push сповіщень
     */
    async unsubscribeFromPush() {
        try {
            const subscription = await this.registration.pushManager.getSubscription();
            
            if (subscription) {
                // Відписуємося локально
                await subscription.unsubscribe();
                
                // Видаляємо підписку з сервера
                await this.removeSubscriptionFromServer(subscription);
                
                console.log('Push підписка видалена');
            }
            
            this.isEnabled = false;
            
        } catch (error) {
            console.error('Помилка видалення push підписки:', error);
            throw error;
        }
    }

    /**
     * Видалення підписки з сервера
     */
    async removeSubscriptionFromServer(subscription) {
        try {
            const response = await fetch('/push-subscriptions', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    endpoint: subscription.endpoint
                })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Помилка видалення підписки');
            }

        } catch (error) {
            console.error('Помилка видалення підписки з сервера:', error);
            throw error;
        }
    }

    /**
     * Конвертація VAPID ключа
     */
    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    /**
     * Ініціалізація UI елементів
     */
    initializeUI() {
        const toggleButton = document.getElementById('push-notifications-toggle');
        if (toggleButton) {
            toggleButton.addEventListener('change', async (e) => {
                try {
                    if (e.target.checked) {
                        await this.requestPermission();
                        this.showNotification('Push сповіщення увімкнено', 'success');
                    } else {
                        await this.unsubscribeFromPush();
                        this.showNotification('Push сповіщення вимкнено', 'info');
                    }
                } catch (error) {
                    e.target.checked = false;
                    this.showNotification('Помилка: ' + error.message, 'error');
                }
            });

            // Встановлюємо початковий стан
            toggleButton.checked = this.isEnabled;
        }
    }

    /**
     * Показ повідомлення користувачу
     */
    showNotification(message, type = 'info') {
        // Можна використовувати toast або інший UI компонент
        console.log(`[${type.toUpperCase()}] ${message}`);
        
        // Простий alert для демонстрації
        if (type === 'error') {
            alert(message);
        }
    }
}

// Ініціалізація при завантаженні DOM
document.addEventListener('DOMContentLoaded', () => {
    window.pushNotificationManager = new PushNotificationManager();
});

// Експорт для використання в інших модулях
export default PushNotificationManager;
