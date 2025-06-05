<?php

namespace Tests\Unit\Services;

use Alison\ProjectManagementAssistant\Services\MarkdownService;
use Tests\TestCase;

class MarkdownServiceTest extends TestCase
{
    private MarkdownService $markdownService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->markdownService = new MarkdownService();
    }

    public function test_converts_basic_markdown_to_html()
    {
        $markdown = '# Заголовок 1

Це звичайний текст з **жирним** та *курсивом*.

## Заголовок 2

- Елемент списку 1
- Елемент списку 2

1. Нумерований список
2. Другий елемент

`Інлайн код` та блок коду:

```php
echo "Hello World";
```

> Це цитата

[Посилання](https://example.com)';

        $html = $this->markdownService->toHtml($markdown);

        $this->assertStringContainsString('<h1', $html);
        $this->assertStringContainsString('<h2', $html);
        $this->assertStringContainsString('<strong>', $html);
        $this->assertStringContainsString('<em>', $html);
        $this->assertStringContainsString('<ul', $html);
        $this->assertStringContainsString('<ol', $html);
        $this->assertStringContainsString('<code>', $html);
        $this->assertStringContainsString('<pre>', $html);
        $this->assertStringContainsString('<blockquote>', $html);
        $this->assertStringContainsString('<a href="https://example.com">', $html);
    }

    public function test_converts_table_markdown_to_html()
    {
        $markdown = '| Колонка 1 | Колонка 2 | Колонка 3 |
|-----------|-----------|-----------|
| Дані 1    | Дані 2    | Дані 3    |
| Дані 4    | Дані 5    | Дані 6    |';

        $html = $this->markdownService->toHtml($markdown);

        $this->assertStringContainsString('<table', $html);
        $this->assertStringContainsString('<thead>', $html);
        $this->assertStringContainsString('<tbody>', $html);
        $this->assertStringContainsString('<th', $html);
        $this->assertStringContainsString('<td', $html);
    }

    public function test_returns_empty_string_for_empty_input()
    {
        $this->assertEquals('', $this->markdownService->toHtml(''));
        $this->assertEquals('', $this->markdownService->toHtml('   '));
    }

    public function test_detects_markdown_syntax()
    {
        $this->assertTrue($this->markdownService->hasMarkdownSyntax('# Заголовок'));
        $this->assertTrue($this->markdownService->hasMarkdownSyntax('**жирний текст**'));
        $this->assertTrue($this->markdownService->hasMarkdownSyntax('*курсив*'));
        $this->assertTrue($this->markdownService->hasMarkdownSyntax('`код`'));
        $this->assertTrue($this->markdownService->hasMarkdownSyntax('- список'));
        $this->assertTrue($this->markdownService->hasMarkdownSyntax('1. нумерований'));
        $this->assertTrue($this->markdownService->hasMarkdownSyntax('> цитата'));
        $this->assertTrue($this->markdownService->hasMarkdownSyntax('[посилання](url)'));
        $this->assertTrue($this->markdownService->hasMarkdownSyntax('| таблиця |'));

        $this->assertFalse($this->markdownService->hasMarkdownSyntax('Звичайний текст'));
        $this->assertFalse($this->markdownService->hasMarkdownSyntax(''));
    }

    public function test_generates_preview()
    {
        $markdown = '# Довгий заголовок

Це дуже довгий текст, який повинен бути обрізаний до певної кількості символів для попереднього перегляду. Він містить багато інформації, яка не поміститься в короткий опис.

## Ще один заголовок

Більше тексту тут.';

        $preview = $this->markdownService->getPreview($markdown, 50);

        $this->assertLessThanOrEqual(155, strlen($preview)); // Може бути довшим через українські символи
        $this->assertStringContainsString('...', $preview);
        $this->assertStringNotContainsString('<', $preview); // Без HTML тегів
        $this->assertStringNotContainsString('#', $preview); // Без Markdown синтаксису
    }

    public function test_strips_unsafe_content()
    {
        $markdown = '# Заголовок

<script>alert("XSS")</script>

[Безпечне посилання](https://example.com)
[Небезпечне посилання](javascript:alert("XSS"))';

        $html = $this->markdownService->toHtml($markdown);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('javascript:', $html);
        $this->assertStringContainsString('https://example.com', $html);
    }

    public function test_handles_ukrainian_text()
    {
        $markdown = '# Український заголовок

Це текст **українською** мовою з *курсивом*.

- Перший пункт
- Другий пункт

> Українська цитата

`код` та [посилання](https://приклад.укр)';

        $html = $this->markdownService->toHtml($markdown);

        $this->assertStringContainsString('Український заголовок', $html);
        $this->assertStringContainsString('українською', $html);
        $this->assertStringContainsString('Українська цитата', $html);
    }
}
