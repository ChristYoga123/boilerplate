@props([
    'field',
    'for' => null,
    'label' => null,
    'hint' => null,
    'required' => null,
    'errorNames' => null,
])

@php
    $for = $for ?? $field->id;
    $label = $label ?? $field->label;
    $hint = $hint ?? $field->hint;
    $required = $required ?? $field->required;
    $errorNames = $errorNames ?? $field->errorNames;
    $errorBag = $errors ?? null;
@endphp

<div class="{{ $field->wrapperClass }}">
    @if($label)
        <label for="{{ $for }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    {{ $slot }}

    @if($hint)
        <div class="form-text">
            {{ $hint }}
        </div>
    @endif

    @if(is_object($errorBag) && method_exists($errorBag, 'has'))
        @foreach($errorNames as $errorName)
            @if($errorBag->has($errorName))
                <div class="invalid-feedback d-block">
                    {{ $errorBag->first($errorName) }}
                </div>
            @endif
        @endforeach
    @endif
</div>
