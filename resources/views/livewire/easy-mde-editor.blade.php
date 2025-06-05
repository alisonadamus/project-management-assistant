<div class="easymde-wrapper" wire:ignore>
    <textarea 
        id="{{ $editorId }}"
        name="{{ $name }}"
        {{ $required ? 'required' : '' }}
        placeholder="{{ $placeholder }}"
        wire:model="content"
    >{{ $content }}</textarea>
    
    @if($error)
        <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $error }}</p>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof EasyMDE === 'undefined') {
        console.error('EasyMDE is not loaded');
        return;
    }

    const textarea = document.getElementById('{{ $editorId }}');
    if (!textarea) {
        console.error('Textarea not found: {{ $editorId }}');
        return;
    }

    // Перевіряємо, чи вже ініціалізований редактор
    if (textarea.easymdeInstance) {
        return;
    }

    const easymde = new EasyMDE({
        element: textarea,
        placeholder: '{{ $placeholder }}',
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
        },
        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
    });

    // Зберігаємо посилання на інстанс
    textarea.easymdeInstance = easymde;

    // Синхронізуємо з Livewire
    easymde.codemirror.on('change', function() {
        const content = easymde.value();
        @this.set('content', content);
    });

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

    // Очищення при видаленні компонента
    window.addEventListener('beforeunload', function() {
        if (easymde) {
            easymde.toTextArea();
            easymde = null;
        }
    });
});

// Функція для оновлення контенту ззовні
window.updateEasyMDE = function(editorId, content) {
    const textarea = document.getElementById(editorId);
    if (textarea && textarea.easymdeInstance) {
        textarea.easymdeInstance.value(content);
    }
};
</script>
@endpush
