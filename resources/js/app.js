import './bootstrap';
import './push-notifications';

// Імпорт EasyMDE
import EasyMDE from 'easymde';
import 'easymde/dist/easymde.min.css';

// Робимо EasyMDE доступним глобально
window.EasyMDE = EasyMDE;

// Функціонал перемикання теми (світла/темна та кольорова)
document.addEventListener('DOMContentLoaded', function() {
    // Перевіряємо збережену тему або використовуємо системні налаштування
    if (localStorage.getItem('theme') === 'dark' ||
        (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }

    // Перевіряємо збережену кольорову тему
    const savedColorTheme = localStorage.getItem('colorTheme');
    if (savedColorTheme) {
        applyColorTheme(savedColorTheme);
        updateActiveColorButton(savedColorTheme);
    }

    // Функція для перемикання теми (світла/темна)
    window.toggleDarkMode = function() {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        } else {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }
    }

    // Функція для зміни кольорової теми
    window.setColorTheme = function(theme) {
        applyColorTheme(theme);
        localStorage.setItem('colorTheme', theme);
        updateActiveColorButton(theme);
    }

    // Функція для застосування кольорової теми
    function applyColorTheme(theme) {
        // Видаляємо всі класи тем
        document.documentElement.classList.remove(
            'theme-yellow-green', 'theme-blue-pink', 'theme-green-purple',
            'theme-pastel', 'theme-vibrant', 'theme-ocean', 'theme-sunset', 'theme-neon'
        );

        // Додаємо новий клас теми
        if (theme) {
            document.documentElement.classList.add(`theme-${theme}`);
        }

        // Додатково додаємо атрибут data-theme для кращої сумісності
        document.documentElement.setAttribute('data-theme', theme || 'blue');

        // Застосовуємо зміни негайно
        document.body.style.transition = 'background-color 0.3s ease, color 0.3s ease';

        // Додаємо повідомлення про зміну теми
        console.log(`Застосовано кольорову тему: ${theme || 'blue'}`);
    }

    // Функція для оновлення активної кнопки
    function updateActiveColorButton(theme) {
        // Видаляємо клас active з усіх кнопок
        document.querySelectorAll('.color-theme-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Додаємо клас active до вибраної кнопки
        const activeBtn = document.querySelector(`.color-theme-btn[data-theme="${theme || 'blue'}"]`);
        if (activeBtn) {
            activeBtn.classList.add('active');
        }
    }
});
