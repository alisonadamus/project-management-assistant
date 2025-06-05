// Service Worker для обробки push сповіщень
const CACHE_NAME = 'project-management-v1';

// Встановлення Service Worker
self.addEventListener('install', function(event) {
    console.log('Service Worker: Встановлено');
    self.skipWaiting();
});

// Активація Service Worker
self.addEventListener('activate', function(event) {
    console.log('Service Worker: Активовано');
    event.waitUntil(self.clients.claim());
});

// Обробка push повідомлень
self.addEventListener('push', function(event) {
    console.log('Service Worker: Отримано push повідомлення');
    
    if (!event.data) {
        console.log('Push повідомлення без даних');
        return;
    }

    try {
        const data = event.data.json();
        console.log('Push дані:', data);

        const options = {
            body: data.body || 'Нове повідомлення',
            icon: data.icon || '/favicon.ico',
            badge: data.badge || '/favicon.ico',
            image: data.image,
            data: data.data || {},
            actions: data.actions || [],
            requireInteraction: data.requireInteraction || false,
            silent: data.silent || false,
            tag: data.tag || 'default',
            timestamp: Date.now(),
            vibrate: data.vibrate || [200, 100, 200],
            renotify: true
        };

        // Додаємо дії для чат повідомлень
        if (data.data && data.data.type === 'new_chat_message') {
            options.actions = [
                {
                    action: 'view',
                    title: '👁️ Переглянути',
                    icon: '/favicon.ico'
                },
                {
                    action: 'close',
                    title: '❌ Закрити',
                    icon: '/favicon.ico'
                }
            ];
        }

        event.waitUntil(
            self.registration.showNotification(data.title || 'Нове повідомлення', options)
        );

    } catch (error) {
        console.error('Помилка обробки push повідомлення:', error);
        
        // Показуємо базове повідомлення у випадку помилки
        event.waitUntil(
            self.registration.showNotification('Нове повідомлення', {
                body: 'У вас є нове повідомлення',
                icon: '/favicon.ico',
                badge: '/favicon.ico'
            })
        );
    }
});

// Обробка кліків по повідомленням
self.addEventListener('notificationclick', function(event) {
    console.log('Service Worker: Клік по повідомленню');
    
    event.notification.close();

    const data = event.notification.data;
    let urlToOpen = '/dashboard';

    // Визначаємо URL для відкриття
    if (data && data.url) {
        urlToOpen = data.url;
    } else if (data && data.project_id) {
        urlToOpen = `/projects/${data.project_id}`;
    }

    // Обробка дій
    if (event.action === 'view') {
        urlToOpen = data && data.url ? data.url : urlToOpen;
    } else if (event.action === 'close') {
        return; // Просто закриваємо повідомлення
    }

    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        }).then(function(clientList) {
            // Перевіряємо, чи є вже відкрита вкладка з потрібним URL
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url.includes(urlToOpen.split('?')[0]) && 'focus' in client) {
                    return client.focus();
                }
            }
            
            // Якщо вкладка не знайдена, відкриваємо нову
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});

// Обробка закриття повідомлень
self.addEventListener('notificationclose', function(event) {
    console.log('Service Worker: Повідомлення закрито');
    
    // Можна додати аналітику або інші дії при закритті
    const data = event.notification.data;
    if (data && data.type === 'new_chat_message') {
        console.log('Закрито чат повідомлення для проекту:', data.project_id);
    }
});

// Обробка помилок
self.addEventListener('error', function(event) {
    console.error('Service Worker помилка:', event.error);
});

// Обробка необроблених помилок Promise
self.addEventListener('unhandledrejection', function(event) {
    console.error('Service Worker необроблена помилка Promise:', event.reason);
});
