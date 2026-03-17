@props([
    /**
     * items: [
     *   ['label' => 'Home', 'url' => route('admin.dashboard')],
     *   ['label' => 'Dashboard'],
     * ]
     */
    'items' => [],
    'class' => '',
])

@php
    $baseClasses = 'breadcrumb mb-3';
    $classes = trim($baseClasses . ' ' . $class);
@endphp

<nav aria-label="breadcrumb">
    <ol {{ $attributes->merge(['class' => $classes]) }}>
        @foreach($items as $item)
            @php
                $label = $item['label'] ?? '';
                $url = $item['url'] ?? null;
                $isLast = $loop->last;
            @endphp

            @if($isLast || !$url)
                <li class="breadcrumb-item active" aria-current="page">{{ $label }}</li>
            @else
                <li class="breadcrumb-item">
                    <a href="{{ $url }}">{{ $label }}</a>
                </li>
            @endif
        @endforeach
    </ol>
</nav>

