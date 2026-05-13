@props([
    'variant' => 'secondary',
    'soft' => false,
    'icon' => null,
])

@php
    $classes = $soft
        ? 'badge bg-' . $variant . '-subtle text-' . $variant
        : 'badge bg-' . $variant;
@endphp

<span {{ $attributes->class($classes) }}>
    @if($icon)
        <i class="{{ $icon }} me-1"></i>
    @endif
    {{ $slot }}
</span>
