import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        },
    },
});

// Додаємо обробку помилок підключення
window.Echo.connector.pusher.connection.bind('error', function(err) {
    console.error('Echo connection error:', err);
});

window.Echo.connector.pusher.connection.bind('connected', function() {
    console.log('Echo connected successfully');
});

window.Echo.connector.pusher.connection.bind('disconnected', function() {
    console.log('Echo disconnected');
});
