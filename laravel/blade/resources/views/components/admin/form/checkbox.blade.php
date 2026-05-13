@props([
    'value' => '1',
])

@php
    $field = \App\Support\AdminFormField::make($attributes);
    $checkboxId = $field->id;
    $isChecked = old($field->errorName, $attributes->get('checked')) ? true : false;
    $hasError = $field->hasError($errors ?? null);
    $errorBag = $errors ?? null;
@endphp

<div class="form-check">
    <input
        id="{{ $checkboxId }}"
        name="{{ $field->name }}"
        type="checkbox"
        value="{{ $value }}"
        {{ $isChecked ? 'checked' : '' }}
        @if($field->required) required @endif
        @if($field->disabled) disabled @endif
        @if($field->readonly) readonly @endif
        {{ $field->controlAttributes(['checked'])->class([
            'form-check-input',
            'is-invalid' => $hasError,
        ]) }}
    >

    @if($field->label)
        <label class="form-check-label c-pointer" for="{{ $checkboxId }}">
            {{ $field->label }}
            @if($field->required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @else
        {{ $slot }}
    @endif

    @if($field->hint)
        <div class="form-text">
            {{ $field->hint }}
        </div>
    @endif

    @if(is_object($errorBag) && method_exists($errorBag, 'has'))
        @foreach($field->errorNames as $errorName)
            @if($errorBag->has($errorName))
                <div class="invalid-feedback d-block">
                    {{ $errorBag->first($errorName) }}
                </div>
            @endif
        @endforeach
    @endif
</div>
