@props([
    'placeholder' => '0',
    'currency' => 'Rp',
    'min' => 0,
    'max' => null,
    'step' => 1,
    'thousands' => true,
    'thousandsSeparator' => '.',
    'decimalSeparator' => ',',
    'decimalScale' => 0,
])

@php
    $field = \App\Support\AdminFormField::make($attributes, [
        'prefix' => $attributes->get('prefix', $currency),
    ]);
    $inputId = $field->id;
    $hasError = $field->hasError($errors ?? null);
    $sizeClass = $field->size ? 'form-control-' . $field->size : null;
    $rawValue = $field->oldValue();
    $displayName = $field->name ? $field->name . '_display' : null;
@endphp

<x-admin.form.field :field="$field">
    <div class="input-group">
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
