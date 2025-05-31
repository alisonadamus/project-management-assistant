@props(['disabled' => false])

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'textarea-gradient bg-white dark:bg-gray-900 dark:text-gray-300 focus:ring-primary-500 rounded-md shadow-sm']) !!}>{{ $slot }}</textarea>
