@props(['type' => 'button'])

<button {{ $attributes->merge(['type' => $type, 'class' => 'inline-flex items-center px-4 py-2 btn-danger-gradient border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
