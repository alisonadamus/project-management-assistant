@props([
    'name' => '',
    'value' => '',
    'placeholder' => 'Введіть текст',
    'required' => false,
    'id' => null
])

@php
    $id = $id ?? 'easymde-' . uniqid();
@endphp

<div class="easymde-wrapper" wire:ignore>
    <textarea 
        id="{{ $id }}"
        name="{{ $name }}"
        {{ $required ? 'required' : '' }}
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'hidden']) }}
    >{{ $value }}</textarea>
</div>

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Функція для ініціалізації EasyMDE
    function initializeEasyMDE(textareaId) {
        const textarea = document.getElementById(textareaId);
        if (!textarea || textarea.easymdeInstance) {
            return;
        }

        const easymde = new EasyMDE({
            element: textarea,
            placeholder: textarea.getAttribute('placeholder') || 'Введіть текст',
            spellChecker: false,
            autofocus: false,
            autosave: {
                enabled: false
            },
            status: ['autosave', 'lines', 'words', 'cursor'],
            toolbar: [
                'bold', 'italic', 'strikethrough', '|',
                'heading-1', 'heading-2', 'heading-3', '|',
                'quote', 'unordered-list', 'ordered-list', '|',
                'link', 'image', 'table', '|',
                'code', 'horizontal-rule', '|',
                'preview', 'side-by-side', 'fullscreen', '|',
                'guide'
            ],
            previewClass: ['prose', 'prose-sm', 'max-w-none', 'dark:prose-invert'],
            renderingConfig: {
                singleLineBreaks: false,
                codeSyntaxHighlighting: true,
            },
            shortcuts: {
                drawTable: 'Cmd-Alt-T',
                togglePreview: 'Cmd-P',
                toggleSideBySide: 'F9',
                toggleFullScreen: 'F11'
            }
        });

        // Зберігаємо посилання на інстанс
        textarea.easymdeInstance = easymde;

        // Автоматичне розширення висоти (тільки для основних редакторів, не для чату)
        if (!textarea.closest('.project-chat')) {
            function autoResize() {
                const codeMirror = easymde.codemirror;
                const wrapper = codeMirror.getWrapperElement();
                const scrollElement = wrapper.querySelector('.CodeMirror-scroll');
                const sizerElement = wrapper.querySelector('.CodeMirror-sizer');

                if (scrollElement && sizerElement) {
                    const contentHeight = sizerElement.offsetHeight;
                    const minHeight = 120;
                    const maxHeight = 500;
                    const newHeight = Math.max(minHeight, Math.min(maxHeight, contentHeight + 20));

                    scrollElement.style.height = newHeight + 'px';
                    wrapper.style.height = 'auto';
                }
            }

            // Викликаємо автоматичне розширення при зміні контенту
            easymde.codemirror.on('change', autoResize);
            easymde.codemirror.on('update', autoResize);

            // Початкове розширення
            setTimeout(autoResize, 100);
        }

        // Слухаємо зміни теми
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    const isDark = document.documentElement.classList.contains('dark');
                    // Оновлюємо тему редактора
                    if (easymde.codemirror) {
                        easymde.codemirror.setOption('theme', isDark ? 'monokai' : 'default');
                    }
                }
            });
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        });

        return easymde;
    }

    // Ініціалізуємо всі EasyMDE редактори на сторінці
    document.querySelectorAll('.easymde-wrapper textarea').forEach(function(textarea) {
        initializeEasyMDE(textarea.id);
    });

    // Глобальна функція для ініціалізації нових редакторів
    window.initializeEasyMDE = initializeEasyMDE;
});
</script>
@endpush
@endonce

<script>
// Ініціалізуємо конкретний редактор
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.initializeEasyMDE === 'function') {
        window.initializeEasyMDE('{{ $id }}');
    } else {
        // Якщо функція ще не завантажена, чекаємо
        setTimeout(function() {
            if (typeof window.initializeEasyMDE === 'function') {
                window.initializeEasyMDE('{{ $id }}');
            }
        }, 100);
    }
});
</script>
