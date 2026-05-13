@props([
    'type' => 'date',
    'min' => null,
    'max' => null,
])

@php
    $field = \App\Support\AdminFormField::make($attributes);
    $inputId = $field->id;
    $hasError = $field->hasError($errors ?? null);
    $sizeClass = $field->size ? 'form-control-' . $field->size : null;
@endphp

<x-admin.form.field :field="$field">
    <input
        id="{{ $inputId }}"
        name="{{ $field->name }}"
        type="{{ $type }}"
        @if($min) min="{{ $min }}" @endif
        @if($max) max="{{ $max }}" @endif
        value="{{ $field->oldValue() }}"
        @if($field->required) required @endif
        @if($field->disabled) disabled @endif
        @if($field->readonly) readonly @endif
        {{ $field->controlAttributes()->class([
            'form-control',
            $sizeClass,
            'is-invalid border-danger' => $hasError,
        ]) }}
    >
</x-admin.form.field>
