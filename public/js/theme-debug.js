// Скрипт для перевірки роботи кольорової теми
document.addEventListener('DOMContentLoaded', function() {
    // Виводимо поточну тему
    console.log('Поточна тема:', document.documentElement.getAttribute('data-theme'));
    
    // Перевіряємо наявність класів теми
    console.log('Класи HTML елемента:', document.documentElement.className);
    
    // Перевіряємо CSS змінні
    const computedStyle = getComputedStyle(document.documentElement);
    console.log('--color-primary-500:', computedStyle.getPropertyValue('--color-primary-500'));
    console.log('--color-primary-600:', computedStyle.getPropertyValue('--color-primary-600'));
    
    // Додаємо слухач подій для відстеження змін теми
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class' || mutation.attributeName === 'data-theme') {
                console.log('Тема змінена:', document.documentElement.getAttribute('data-theme'));
                console.log('Нові класи:', document.documentElement.className);
            }
        });
    });
    
    observer.observe(document.documentElement, { attributes: true });
    
    // Додаємо функцію для тестування зміни теми
    window.testThemeChange = function(theme) {
        console.log('Тестуємо зміну теми на:', theme);
        setColorTheme(theme);
    };
});
