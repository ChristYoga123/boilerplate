@props([
    'title',
    'description' => null,
    'icon' => null,
])

<section
    data-wizard-step
    data-title="{{ $title }}"
    @if($description) data-description="{{ $description }}" @endif
    @if($icon) data-icon="{{ $icon }}" @endif
    {{ $attributes->class('admin-form-wizard__step') }}
>
    <div class="admin-form-wizard__step-heading">
        <div>
            <h5 class="mb-1">{{ $title }}</h5>
            @if($description)
                <p class="text-muted mb-0">{{ $description }}</p>
            @endif
        </div>
    </div>

    {{ $slot }}
</section>
