@props([
    'type' => 'stat', // stat|chart
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'value' => null,
    'badge' => null,
    'chartId' => null,
    'chartType' => null, // chart.js type: bar, line, doughnut, etc
    'chartOptions' => null, // json/string; inisialisasi lewat JS di luar
    'chartData' => null, // json/string; inisialisasi lewat JS di luar
    'class' => '',
])

@php
    $baseClasses = 'card stretch stretch-full';
    $classes = trim($baseClasses . ' ' . $class);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @if($type !== 'stat' && ($title || $badge))
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                @if($title)
                    <h5 class="card-title mb-0">{{ $title }}</h5>
                @endif
                @if($subtitle)
                    <div class="fs-12 text-muted mt-1">{{ $subtitle }}</div>
                @endif
            </div>
            @if($badge)
                <span class="badge bg-gray-200 text-dark">{{ $badge }}</span>
            @endif
        </div>
    @endif

    <div class="card-body">
        @if($type === 'stat')
            <div class="d-flex align-items-start justify-content-between mb-4">
                <div class="d-flex gap-4 align-items-center">
                    @if($icon)
                        <div class="avatar-text avatar-lg bg-gray-200">
                            <i class="{{ $icon }}"></i>
                        </div>
                    @endif
                    <div>
                        @if($value !== null)
                            <div class="fs-4 fw-bold text-dark">{{ $value }}</div>
                        @endif
                        @if($title)
                            <h3 class="fs-13 fw-semibold text-truncate-1-line">{{ $title }}</h3>
                        @endif
                    </div>
                </div>
                <a href="javascript:void(0);" class="">
                    <i class="feather-more-vertical"></i>
                </a>
            </div>
            @if($slot->isNotEmpty())
                <div class="pt-4">
                    {{ $slot }}
                </div>
            @endif
        @elseif($type === 'chart')
            @php
                $id = $chartId ?? 'widget-chart-' . uniqid();
            @endphp
            <div class="position-relative">
                <canvas
                    id="{{ $id }}"
                    data-chart-type="{{ $chartType }}"
                    @if($chartData) data-chart-data='@json($chartData)' @endif
                    @if($chartOptions) data-chart-options='@json($chartOptions)' @endif
                ></canvas>
            </div>
        @else
            {{ $slot }}
        @endif
    </div>
</div>

