@props([
    'placeholder' => null,
    'min' => null,
    'max' => null,
    'step' => null,
    'thousands' => true,
    'thousandsSeparator' => '.',
    'decimalSeparator' => ',',
    'decimalScale' => null,
])

@php
    $field = \App\Support\AdminFormField::make($attributes);
    $inputId = $field->id;
    $hasError = $field->hasError($errors ?? null);
    $hasGroup = $field->prefix || $field->suffix;
    $sizeClass = $field->size ? 'form-control-' . $field->size : null;
    $rawValue = $field->oldValue();
    $displayName = $field->name ? $field->name . '_display' : null;
@endphp

<x-admin.form.field :field="$field">
    <div class="{{ $hasGroup ? 'input-group' : '' }}">
        @if($field->prefix)
            <span class="input-group-text">{{ $field->prefix }}</span>
        @endif

        <input
            id="{{ $inputId }}"
            name="{{ $thousands ? $displayName : $field->name }}"
            type="{{ $thousands ? 'text' : 'number' }}"
            inputmode="decimal"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if(!$thousands && $min !== null) min="{{ $min }}" @endif
            @if(!$thousands && $max !== null) max="{{ $max }}" @endif
            @if(!$thousands && $step !== null) step="{{ $step }}" @endif
            value="{{ $rawValue }}"
            @if($field->required) required @endif
            @if($field->disabled) disabled @endif
            @if($field->readonly) readonly @endif
            @if($thousands)
                data-number-format
                data-target="{{ $inputId }}_value"
                data-thousands-separator="{{ $thousandsSeparator }}"
                data-decimal-separator="{{ $decimalSeparator }}"
                @if($decimalScale !== null) data-decimal-scale="{{ $decimalScale }}" @endif
                @if($min !== null) data-min="{{ $min }}" @endif
                @if($max !== null) data-max="{{ $max }}" @endif
            @endif
            {{ $field->controlAttributes()->class([
                'form-control',
                $sizeClass,
                'is-invalid border-danger' => $hasError,
            ]) }}
        >

        @if($field->suffix)
            <span class="input-group-text">{{ $field->suffix }}</span>
        @endif
    </div>

    @if($thousands)
        <input
            id="{{ $inputId }}_value"
            type="hidden"
            name="{{ $field->name }}"
            value="{{ $rawValue }}"
        >
    @endif
</x-admin.form.field>

@once
    @push('scripts')
        <script>
            (function () {
                if (window.__adminNumberFormatInit) return;
                window.__adminNumberFormatInit = true;

                function escapeRegExp(value) {
                    return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                }

                function normalizeValue(value, decimalSeparator, decimalScale) {
                    value = String(value || '').trim();
                    if (!value) {
                        return { raw: '', trailingDecimal: false };
                    }

                    var negative = value.charAt(0) === '-';
                    var trailingDecimal = decimalScale !== '0' && value.endsWith(decimalSeparator);
                    value = value.replace(new RegExp('[^0-9' + escapeRegExp(decimalSeparator) + ']', 'g'), '');

                    var parts = value.split(decimalSeparator);
                    var integer = parts.shift() || '';
                    var decimal = parts.join('');

                    integer = integer.replace(/^0+(?=\d)/, '');

                    if (decimalScale !== null && decimalScale !== '') {
                        decimal = decimal.slice(0, parseInt(decimalScale, 10));
                    }

                    return {
                        raw: (negative ? '-' : '') + (integer || '0') + (decimal ? '.' + decimal : ''),
                        trailingDecimal: trailingDecimal,
                    };
                }

                function formatValue(raw, thousandsSeparator, decimalSeparator, decimalScale, trailingDecimal) {
                    raw = String(raw || '').replace(',', '.');
                    if (!raw) return '';

                    var negative = raw.charAt(0) === '-';
                    if (negative) raw = raw.slice(1);

                    var parts = raw.split('.');
                    var integer = (parts[0] || '').replace(/\D/g, '').replace(/^0+(?=\d)/, '');
                    var decimal = (parts[1] || '').replace(/\D/g, '');

                    if (decimalScale !== null && decimalScale !== '') {
                        decimal = decimal.slice(0, parseInt(decimalScale, 10));
                    }

                    integer = integer || '0';
                    integer = integer.replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSeparator);

                    return (negative ? '-' : '') + integer + ((decimal || trailingDecimal) ? decimalSeparator + decimal : '');
                }

                function sync(input) {
                    var target = document.getElementById(input.dataset.target);
                    if (!target) return;

                    var decimalSeparator = input.dataset.decimalSeparator || ',';
                    var thousandsSeparator = input.dataset.thousandsSeparator || '.';
                    var decimalScale = input.dataset.decimalScale || null;
                    var normalized = normalizeValue(input.value, decimalSeparator, decimalScale);

                    target.value = normalized.raw;
                    input.value = formatValue(
                        normalized.raw,
                        thousandsSeparator,
                        decimalSeparator,
                        decimalScale,
                        normalized.trailingDecimal
                    );
                }

                function init(input) {
                    var target = document.getElementById(input.dataset.target);
                    if (target && target.value) {
                        input.value = formatValue(
                            target.value,
                            input.dataset.thousandsSeparator || '.',
                            input.dataset.decimalSeparator || ',',
                            input.dataset.decimalScale || null
                        );
                    } else {
                        sync(input);
                    }

                    input.addEventListener('input', function () {
                        sync(input);
                    });

                    input.addEventListener('blur', function () {
                        sync(input);
                    });

                    input.closest('form')?.addEventListener('submit', function () {
                        sync(input);
                    });
                }

                document.addEventListener('DOMContentLoaded', function () {
                    document.querySelectorAll('[data-number-format]').forEach(init);
                });
            })();
        </script>
    @endpush
@endonce
