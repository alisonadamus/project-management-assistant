@props(['content' => '', 'class' => ''])

@php
    $markdownService = app(\Alison\ProjectManagementAssistant\Services\MarkdownService::class);
    $htmlContent = $markdownService->toHtml($content);
@endphp

@if(!empty(trim($content)))
    <div {{ $attributes->merge(['class' => 'prose prose-sm max-w-none dark:prose-invert ' . $class]) }}>
        {!! $htmlContent !!}
    </div>
@endif
