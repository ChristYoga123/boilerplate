@props([
    'action',
    'method' => 'POST',
    'multipart' => false,
    'cancelUrl' => null,
    'cancelLabel' => 'Batal',
    'submitLabel' => 'Simpan',
    'showCancel' => true,
    'showFooter' => true,
    'footerClass' => 'd-flex justify-content-end gap-2 mt-3',
])

@php
    $method = strtoupper($method);
    $isGet = $method === 'GET';
    $htmlMethod = $isGet ? 'GET' : 'POST';
    $cancelUrl = $cancelUrl ?? url()->previous();
    $hasCustomFooter = isset($footer) && trim((string) $footer) !== '';
    $hasWizard = str_contains((string) $slot, 'data-form-wizard');
    $shouldShowFooter = $showFooter && ($hasCustomFooter || ! $hasWizard);
@endphp

<form
    action="{{ $action }}"
    method="{{ $htmlMethod }}"
    @if($multipart) enctype="multipart/form-data" @endif
    {{ $attributes }}
>
    @unless($isGet)
        @csrf
    @endunless

    @if(!in_array($method, ['GET', 'POST'], true))
        @method($method)
    @endif

    {{ $slot }}

    @if($shouldShowFooter)
        @if($hasCustomFooter)
            {{ $footer }}
        @else
            <div class="{{ $footerClass }}">
                @if($showCancel)
                    <x-admin.ui.button :href="$cancelUrl" variant="secondary" outline>
                        {{ $cancelLabel }}
                    </x-admin.ui.button>
                @endif

                <x-admin.ui.button type="submit">
                    {{ $submitLabel }}
                </x-admin.ui.button>
            </div>
        @endif
    @endif
</form>
