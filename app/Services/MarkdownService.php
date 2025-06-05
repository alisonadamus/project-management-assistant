<?php

namespace Alison\ProjectManagementAssistant\Services;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;

class MarkdownService
{
    private MarkdownConverter $converter;

    public function __construct()
    {
        // Створюємо середовище з розширеннями
        $environment = new Environment([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 10,
            'table' => [
                'wrap' => [
                    'enabled' => true,
                    'tag' => 'div',
                    'attributes' => ['class' => 'table-responsive'],
                ],
            ],
        ]);

        // Додаємо основні розширення
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new TableExtension());

        $this->converter = new MarkdownConverter($environment);
    }

    /**
     * Конвертує Markdown текст в HTML
     */
    public function toHtml(string $markdown): string
    {
        if (empty(trim($markdown))) {
            return '';
        }

        $html = $this->converter->convert($markdown)->getContent();
        
        // Додаємо CSS класи для кращого стилізування
        $html = $this->addCssClasses($html);
        
        return $html;
    }

    /**
     * Додає CSS класи до HTML елементів
     */
    private function addCssClasses(string $html): string
    {
        // Не додаємо класи, оскільки вони будуть додані через CSS в компоненті
        return $html;
    }

    /**
     * Перевіряє, чи містить текст Markdown синтаксис
     */
    public function hasMarkdownSyntax(string $text): bool
    {
        if (empty(trim($text))) {
            return false;
        }

        // Перевіряємо наявність основних Markdown елементів
        $patterns = [
            '/^#{1,6}\s/',                    // Заголовки
            '/\*\*.*?\*\*/',                  // Жирний текст
            '/\*.*?\*/',                      // Курсив
            '/`.*?`/',                        // Інлайн код
            '/```[\s\S]*?```/',               // Блоки коду
            '/^\s*[-*+]\s/',                  // Списки
            '/^\s*\d+\.\s/',                  // Нумеровані списки
            '/^\s*>\s/',                      // Цитати
            '/\[.*?\]\(.*?\)/',               // Посилання
            '/\|.*?\|/',                      // Таблиці
            '/^---+$/',                       // Горизонтальні лінії
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Отримує попередній перегляд тексту (перші N символів без HTML)
     */
    public function getPreview(string $markdown, int $length = 150): string
    {
        if (empty(trim($markdown))) {
            return '';
        }

        // Конвертуємо в HTML і видаляємо теги
        $html = $this->toHtml($markdown);
        $text = strip_tags($html);
        
        // Обрізаємо до потрібної довжини
        if (mb_strlen($text) > $length) {
            $text = mb_substr($text, 0, $length) . '...';
        }

        return $text;
    }
}
