@props([
    'name',
    'label'    => null,
    'placeholder' => null,
    'value'    => null,
    'required' => false,
    'rows'     => 4,
])

@php
    $inputId  = $attributes->get('id', $name);
    $hasError = $errors->has($name);
@endphp

<div class="mb-3">
    @if($label)
        <label for="{{ $inputId }}" class="form-label">
            {{ $label }}
            @if($required)<span class="text-danger">*</span>@endif
        </label>
    @endif

    <textarea
        id="{{ $inputId }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($required) required @endif
        {{ $attributes->class([
            'form-control',
            'is-invalid border-danger' => $hasError,
        ]) }}
    >{{ old($name, $value) }}</textarea>

    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
