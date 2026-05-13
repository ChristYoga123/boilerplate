@props([
    'label' => 'Aksi',
    'icon' => 'feather-more-vertical',
    'variant' => 'light',
    'align' => 'end',
    'buttonClass' => null,
])

<div {{ $attributes->class('dropdown') }}>
    <button
        type="button"
        class="btn btn-{{ $variant }} {{ $buttonClass }}"
        data-bs-toggle="dropdown"
        aria-expanded="false"
    >
        @if($icon)
            <i class="{{ $icon }} {{ $label ? 'me-2' : '' }}"></i>
        @endif
        @if($label)
            {{ $label }}
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-{{ $align }}">
        {{ $slot }}
    </div>
</div>
