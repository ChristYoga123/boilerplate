@php
    // Defaults — works with both <x-admin.ui.button> and view() rendering
    $permission = $permission ?? null;
    $as = $as ?? 'button'; // button|icon
    $type = $type ?? 'button';
    $variant = $variant ?? 'primary';
    $outline = $outline ?? false;
    $size = $size ?? null;
    $href = $href ?? null;
    $modal = $modal ?? null;
    $icon = $icon ?? null;
    $block = $block ?? false;
    $class = $class ?? '';
    $title = $title ?? null;
    $attrs = $attrs ?? [];

    // Ensure $attributes is available (view() mode won't have it)
    if (!isset($attributes) || !($attributes instanceof \Illuminate\View\ComponentAttributeBag)) {
        $attributes = new \Illuminate\View\ComponentAttributeBag($attrs);
    }

    // Ensure $slot is available
    if (!isset($slot) || (!($slot instanceof \Illuminate\Support\HtmlString) && !($slot instanceof \Illuminate\View\ComponentSlot))) {
        $slot = new \Illuminate\Support\HtmlString('');
    }

    $modalAttrs = $modal ? ['data-bs-toggle' => 'modal', 'data-bs-target' => $modal] : [];
@endphp

@if($permission && !auth()->user()?->can($permission))
@elseif($as === 'icon')
    {{-- Icon-only mode: avatar-text style for table actions etc. --}}
    <a
        href="{{ $href ?? 'javascript:void(0)' }}"
        {{ $attributes->merge(array_merge([
            'class' => trim('avatar-text avatar-md ' . $class),
            'title' => $title,
        ], $modalAttrs)) }}
    >
        <i class="{{ $icon }}"></i>
    </a>
@else
    @php
        $base = 'btn';
        $variantClass = $outline ? 'btn-outline-' . $variant : 'btn-' . $variant;
        $sizeClass = $size ? 'btn-' . $size : '';
        $blockClass = $block ? 'w-100' : '';
        $classes = trim(implode(' ', array_filter([$base, $variantClass, $sizeClass, $blockClass, $class])));
    @endphp

    @if($href && !$modal)
        <a href="{{ $href }}" {{ $attributes->merge(array_merge(['class' => $classes, 'role' => 'button'], $modalAttrs)) }}>
            @if($icon)
                <i class="{{ $icon }} me-2"></i>
            @endif
            {{ $slot }}
        </a>
    @else
        <button
            type="{{ $modal ? 'button' : $type }}"
            {{ $attributes->merge(array_merge(['class' => $classes], $modalAttrs)) }}
            @if($type === 'submit' && !$modal)
                onclick="(function(btn){
                    var $btn = window.jQuery ? window.jQuery(btn) : null;
                    if(!$btn) return;
                    if($btn.data('loading') === 1) return;
                    var $form = $btn.closest('form');
                    if(!$form.length) return;
                    var formEl = $form.get(0);
                    if(typeof formEl.reportValidity === 'function' && !formEl.reportValidity()){
                        return;
                    }
                    $btn.data('loading', 1);
                    var $spinner = $btn.find('[data-spinner]');
                    if($spinner.length){ $spinner.removeClass('d-none'); }
                    $btn.prop('disabled', true);
                    $form.trigger('submit');
                })(this)"
            @endif
        >
            @if($icon)
                <i class="{{ $icon }} me-2"></i>
            @endif
            <span data-label>{{ $slot }}</span>
            @if($type === 'submit' && !$modal)
                <span data-spinner class="ms-2 d-none">
                    <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span>
                </span>
            @endif
        </button>
    @endif
@endif
