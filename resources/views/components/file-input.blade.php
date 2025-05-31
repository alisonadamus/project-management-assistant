@props(['disabled' => false])

<input type="file" {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-primary-600 file:to-primary-500 file:text-white hover:file:shadow-md dark:file:bg-gradient-to-r dark:file:from-primary-600 dark:file:to-primary-500 dark:file:text-white']) !!}>
