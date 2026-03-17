@props([
    'cols' => 1,
])

<div {{ $attributes->merge(['class' => 'form-grid']) }} style="grid-template-columns: repeat({{ (int) $cols }}, 1fr);">
    {{ $slot }}
</div>

@once
@push('styles')
<style>
    .form-grid { display: grid; gap: 0 1.5rem; }
    @media (max-width: 767.98px) { .form-grid { grid-template-columns: 1fr !important; } }
</style>
@endpush
@endonce
