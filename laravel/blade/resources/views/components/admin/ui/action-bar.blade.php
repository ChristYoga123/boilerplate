@props([
    'title' => null,
    'description' => null,
])

<div {{ $attributes->class('d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3') }}>
    @if($title || $description)
        <div>
            @if($title)
                <h5 class="mb-1">{{ $title }}</h5>
            @endif
            @if($description)
                <p class="text-muted mb-0">{{ $description }}</p>
            @endif
        </div>
    @endif

    <div class="d-flex flex-wrap align-items-center gap-2 ms-auto">
        {{ $slot }}
    </div>
</div>
