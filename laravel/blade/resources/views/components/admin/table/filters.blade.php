@props([
    'class' => 'row g-3',
])

<div class="card-body border-bottom py-3 px-3">
    <div {{ $attributes->merge(['class' => $class]) }}>
        {{ $slot }}
    </div>
</div>

