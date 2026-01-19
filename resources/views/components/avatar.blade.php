@props(['src', 'name', 'size' => 'md'])

@php
$sizes = [
    'xs' => 'w-6 h-6 text-xs',
    'sm' => 'w-8 h-8 text-sm',
    'md' => 'w-10 h-10 text-base',
    'lg' => 'w-12 h-12 text-lg',
    'xl' => 'w-16 h-16 text-xl',
    '2xl' => 'w-20 h-20 text-2xl',
    '3xl' => 'w-24 h-24 text-3xl',
];

$sizeClass = $sizes[$size] ?? $sizes['md'];

// Use the stored brand secondary color for avatar fallback and ring
$brandSecondary = \App\Models\Setting::get('brand.secondary', '#D4AF37');
$fallbackBg = ltrim($brandSecondary, '#');
@endphp

<div {{ $attributes->merge(['class' => "relative inline-block $sizeClass"]) }}>
    @if(!empty($src))
        <img src="{{ $src }}"
             alt="{{ $name ?? 'Avatar' }}"
             class="rounded-full object-cover w-full h-full shadow-lg"
             style="box-shadow: 0 0 0 2px var(--brand-secondary);">
    @else
        @php
            $initials = collect(explode(' ', trim($name ?? '')))->filter()->map(fn($p) => mb_substr($p,0,1))->take(2)->join('');
            if (!$initials) $initials = 'U';
        @endphp
        <div class="rounded-full w-full h-full flex items-center justify-center font-semibold" style="background: var(--brand-secondary); color: var(--brand-on-secondary, #0A1647);">
            <span class="text-xl">{{ $initials }}</span>
        </div>
    @endif

    @if(isset($online) && $online)
        <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full bg-green-400 ring-2 ring-white"></span>
    @endif
</div>

