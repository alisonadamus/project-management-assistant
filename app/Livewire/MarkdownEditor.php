<?php

namespace Alison\ProjectManagementAssistant\Livewire;

use Alison\ProjectManagementAssistant\Services\MarkdownService;
use Livewire\Component;

class MarkdownEditor extends Component
{
    public string $content = '';
    public string $name = '';
    public string $placeholder = '';
    public int $rows = 6;
    public bool $showPreview = false;
    public bool $required = false;
    public ?string $error = null;

    protected MarkdownService $markdownService;

    public function boot(MarkdownService $markdownService)
    {
        $this->markdownService = $markdownService;
    }

    public function mount(
        string $content = '',
        string $name = '',
        string $placeholder = '',
        int $rows = 6,
        bool $required = false
    ) {
        $this->content = $content;
        $this->name = $name;
        $this->placeholder = $placeholder;
        $this->rows = $rows;
        $this->required = $required;
    }

    public function togglePreview()
    {
        $this->showPreview = !$this->showPreview;
    }

    public function updatedContent()
    {
        $this->dispatch('markdown-updated', [
            'name' => $this->name,
            'content' => $this->content
        ]);
    }

    public function getPreviewProperty()
    {
        return $this->markdownService->toHtml($this->content);
    }

    public function render()
    {
        return view('livewire.markdown-editor');
    }
}
