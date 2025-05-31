@props(['title' => null])

<div {{ $attributes->merge(['class' => 'section-gradient bg-gray-50 dark:bg-gray-700 p-4 rounded-lg']) }}>
    @if($title)
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">{{ $title }}</h3>
    @endif
    {{ $slot }}
</div>
