@php
    $logo = \App\Models\Setting::get('site.logo');
    $src = $logo ? asset('storage/' . $logo) : asset('logo.svg');
@endphp

<img src="{{ $src }}" {{ $attributes->merge(['alt' => config('app.name', 'Esmalteria Vida Maria')]) }} />
