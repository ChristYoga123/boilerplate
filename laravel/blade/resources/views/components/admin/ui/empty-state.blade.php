@props([
    'title' => 'Belum ada data',
    'description' => null,
    'icon' => 'feather-inbox',
])

<div {{ $attributes->class('text-center py-5 px-3') }}>
    @if($icon)
        <div class="avatar-text avatar-lg mx-auto mb-3">
            <i class="{{ $icon }}"></i>
        </div>
    @endif

    <h5 class="mb-1">{{ $title }}</h5>

    @if($description)
        <p class="text-muted mb-4 mx-auto" style="max-width: 420px;">
            {{ $description }}
        </p>
    @endif

    @if(trim((string) $slot) !== '')
        <div class="d-flex flex-wrap justify-content-center gap-2">
            {{ $slot }}
        </div>
    @endif
</div>
