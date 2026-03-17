@props([
    'title' => null,
    'description' => null,
])

<div class="card stretch stretch-full">
    @if($title)
        <div class="card-header">
            <h5 class="card-title">{{ $title }}</h5>
            @if($description)
                <p class="card-description">{{ $description }}</p>
            @endif
        </div>
    @endif
    <div class="card-body">
        {{ $slot }}
    </div>
</div>
