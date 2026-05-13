@props([
    'placeholder' => null,
    'rows'     => 4,
])

@php
    $field = \App\Support\AdminFormField::make($attributes);
    $inputId = $field->id;
    $hasError = $field->hasError($errors ?? null);
@endphp

<x-admin.form.field :field="$field">
    <textarea
        id="{{ $inputId }}"
        name="{{ $field->name }}"
        rows="{{ $rows }}"
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($field->required) required @endif
        @if($field->disabled) disabled @endif
        @if($field->readonly) readonly @endif
        {{ $field->controlAttributes()->class([
            'form-control',
            'is-invalid border-danger' => $hasError,
        ]) }}
    >{{ $field->oldValue() }}</textarea>
</x-admin.form.field>
