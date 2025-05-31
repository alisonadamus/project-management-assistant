@props(['href'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white btn-gradient hover:shadow-md focus:outline-none focus:border-primary-700 focus:ring focus:ring-primary-200 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</a>
