<?php

namespace Alison\ProjectManagementAssistant\Livewire;

use Livewire\Component;

class EasyMdeEditor extends Component
{
    public string $content = '';
    public string $name = '';
    public string $placeholder = '';
    public bool $required = false;
    public ?string $error = null;
    public string $editorId;

    public function mount(
        string $content = '',
        string $name = '',
        string $placeholder = '',
        bool $required = false
    ) {
        $this->content = $content;
        $this->name = $name;
        $this->placeholder = $placeholder;
        $this->required = $required;
        $this->editorId = 'easymde-' . uniqid();
    }

    public function updatedContent()
    {
        $this->dispatch('markdown-updated', [
            'name' => $this->name,
            'content' => $this->content
        ]);
    }

    public function render()
    {
        return view('livewire.easy-mde-editor');
    }
}
