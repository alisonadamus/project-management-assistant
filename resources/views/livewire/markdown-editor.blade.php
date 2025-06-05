<div class="markdown-editor">
    <!-- Панель інструментів -->
    <div class="flex items-center justify-between mb-2 p-2 bg-gray-50 dark:bg-gray-800 rounded-t-lg border border-gray-300 dark:border-gray-600">
        <div class="flex items-center space-x-2">
            <!-- Кнопки форматування -->
            <button type="button" 
                    onclick="insertMarkdown('**', '**', 'жирний текст')"
                    class="p-1 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 rounded"
                    title="Жирний текст">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 5a1 1 0 011-1h5.5a2.5 2.5 0 010 5H6v2h4.5a2.5 2.5 0 010 5H4a1 1 0 01-1-1V5zM6 6v2h3.5a.5.5 0 000-1H6zm0 5v2h4.5a.5.5 0 000-1H6z"/>
                </svg>
            </button>
            
            <button type="button" 
                    onclick="insertMarkdown('*', '*', 'курсив')"
                    class="p-1 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 rounded"
                    title="Курсив">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8.5 3a1 1 0 00-1 1v1H6a1 1 0 000 2h1.5v6H6a1 1 0 000 2h1.5v1a1 1 0 001 1h3a1 1 0 000-2H10V5h1.5a1 1 0 000-2H10V2a1 1 0 00-1-1H8.5z"/>
                </svg>
            </button>
            
            <button type="button" 
                    onclick="insertMarkdown('`', '`', 'код')"
                    class="p-1 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 rounded"
                    title="Інлайн код">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
            
            <div class="w-px h-4 bg-gray-300 dark:bg-gray-600"></div>
            
            <button type="button" 
                    onclick="insertMarkdown('- ', '', 'елемент списку')"
                    class="p-1 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 rounded"
                    title="Список">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                </svg>
            </button>
            
            <button type="button" 
                    onclick="insertMarkdown('[', '](url)', 'текст посилання')"
                    class="p-1 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 rounded"
                    title="Посилання">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
        
        <!-- Кнопка попереднього перегляду -->
        <button type="button" 
                wire:click="togglePreview"
                class="px-3 py-1 text-sm bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 rounded hover:bg-primary-200 dark:hover:bg-primary-800 transition-colors">
            {{ $showPreview ? 'Редагувати' : 'Попередній перегляд' }}
        </button>
    </div>
    
    <!-- Область редагування/перегляду -->
    <div class="border border-gray-300 dark:border-gray-600 rounded-b-lg">
        @if($showPreview)
            <!-- Попередній перегляд -->
            <div class="p-4 min-h-[{{ $rows * 1.5 }}rem] bg-white dark:bg-gray-900">
                @if(empty(trim($content)))
                    <p class="text-gray-500 dark:text-gray-400 italic">Немає контенту для попереднього перегляду</p>
                @else
                    <x-markdown :content="$content" />
                @endif
            </div>
        @else
            <!-- Редактор -->
            <div class="relative">
                <textarea 
                    wire:model.live.debounce.500ms="content"
                    name="{{ $name }}"
                    rows="{{ $rows }}"
                    placeholder="{{ $placeholder }}"
                    class="w-full p-4 border-0 rounded-b-lg resize-none focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100"
                    {{ $required ? 'required' : '' }}
                    id="markdown-textarea-{{ $name }}"
                ></textarea>
                
                <!-- Підказка по Markdown -->
                <div class="absolute bottom-2 right-2 text-xs text-gray-400 dark:text-gray-500">
                    <span class="bg-white dark:bg-gray-900 px-1 rounded">Підтримується Markdown</span>
                </div>
            </div>
        @endif
    </div>
    
    @if($error)
        <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $error }}</p>
    @endif
</div>

<script>
function insertMarkdown(before, after, placeholder) {
    const textarea = document.getElementById('markdown-textarea-{{ $name }}');
    if (!textarea) return;
    
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);
    const textToInsert = selectedText || placeholder;
    
    const newText = before + textToInsert + after;
    
    textarea.value = textarea.value.substring(0, start) + newText + textarea.value.substring(end);
    
    // Встановлюємо курсор
    const newCursorPos = start + before.length + textToInsert.length;
    textarea.setSelectionRange(newCursorPos, newCursorPos);
    textarea.focus();
    
    // Оновлюємо Livewire
    textarea.dispatchEvent(new Event('input'));
}
</script>
