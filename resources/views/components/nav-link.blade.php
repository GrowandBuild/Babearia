@props(['active'])

@php
    $base = 'inline-flex items-center px-1 pt-1 border-b-2 text-sm leading-5 transition duration-300 ease-in-out';
    $activeClasses = $base . ' nav-link active font-semibold';
    $inactiveClasses = $base . ' nav-link font-medium';
    $classes = ($active ?? false) ? $activeClasses : $inactiveClasses;
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
