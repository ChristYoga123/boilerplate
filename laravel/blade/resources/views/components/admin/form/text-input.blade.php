@props([
    'name',
    'type' => 'text', // text, email, password, number, etc.
    'label' => null,
    'placeholder' => null,
    'value' => null,
    'required' => false,
    'autocomplete' => null,
    'revealable' => false,
])

@php
    $inputId = $attributes->get('id', $name);
    $isPassword = $type === 'password';
    $hasError = $errors->has($name);
    $wrapperClasses = 'mb-3';
@endphp

<div class="{{ $wrapperClasses }}">
    @if($label)
        <label for="{{ $inputId }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    <div class="{{ $isPassword && $revealable ? 'input-group' : '' }}">
        <input
            id="{{ $inputId }}"
            name="{{ $name }}"
            type="{{ $type }}"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            @if($value !== null) value="{{ old($name, $value) }}" @else value="{{ old($name) }}" @endif
            @if($required) required @endif
            {{ $attributes->class([
                'form-control',
                'is-invalid border-danger' => $hasError,
            ]) }}
        >

        @if($isPassword && $revealable)
            <button
                class="btn btn-outline-secondary"
                type="button"
                onclick="(function(btn){const input = btn.closest('.input-group').querySelector('input'); if(!input) return; const isPwd = input.type === 'password'; input.type = isPwd ? 'text' : 'password'; btn.querySelector('[data-eye-open]').classList.toggle('d-none', !isPwd); btn.querySelector('[data-eye-close]').classList.toggle('d-none', isPwd); })(this)"
            >
                <span data-eye-open>
                    <i class="feather-eye"></i>
                </span>
                <span data-eye-close class="d-none">
                    <i class="feather-eye-off"></i>
                </span>
            </button>
        @endif
    </div>

    @error($name)
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>

