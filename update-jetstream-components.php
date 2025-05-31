<?php

// Список компонентів Jetstream
$jetstreamComponents = [
    'action-message',
    'action-section',
    'application-logo',
    'application-mark',
    'authentication-card-logo',
    'authentication-card',
    'banner',
    'button',
    'checkbox',
    'confirmation-modal',
    'confirms-password',
    'danger-button',
    'dialog-modal',
    'dropdown-link',
    'dropdown',
    'form-section',
    'input-error',
    'input',
    'label',
    'modal',
    'nav-link',
    'responsive-nav-link',
    'secondary-button',
    'section-border',
    'section-title',
    'switchable-team',
    'validation-errors',
    'welcome'
];

// Оновлюємо посилання в компонентах Jetstream
$files = glob('resources/views/components/jetstream/*.blade.php');
$count = 0;

foreach ($files as $file) {
    $content = file_get_contents($file);
    $updated = false;
    
    foreach ($jetstreamComponents as $component) {
        // Шукаємо використання компонента у форматі <x-component>
        $pattern = '/<x-' . preg_quote($component) . '(\s|>)/';
        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, '<x-jetstream.' . $component . '$1', $content);
            $updated = true;
        }
    }
    
    if ($updated) {
        file_put_contents($file, $content);
        echo "Оновлено файл: $file\n";
        $count++;
    }
}

echo "Всього оновлено файлів: $count\n";
