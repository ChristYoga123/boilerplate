@props([
    'name',
    'label' => null,
    'id' => null,
])

@php
    $checkboxId = $id ?: $name;
    $isChecked = old($name, $attributes->get('checked')) ? true : false;
@endphp

<div class="form-check">
    <input
        id="{{ $checkboxId }}"
        name="{{ $name }}"
        type="checkbox"
        value="1"
        {{ $isChecked ? 'checked' : '' }}
        {{ $attributes->class([
            'form-check-input',
            'is-invalid' => $errors->has($name),
        ]) }}
    >

    @if($label)
        <label class="form-check-label c-pointer" for="{{ $checkboxId }}">
            {{ $label }}
        </label>
    @else
        {{ $slot }}
    @endif

    @error($name)
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>

