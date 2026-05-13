@props([
    'type' => 'text', // text, email, password, number, etc.
    'placeholder' => null,
    'autocomplete' => null,
    'revealable' => false,
])

@php
    $field = \App\Support\AdminFormField::make($attributes);
    $inputId = $field->id;
    $isPassword = $type === 'password';
    $hasError = $field->hasError($errors ?? null);
    $hasGroup = ($isPassword && $revealable) || $field->prefix || $field->suffix;
    $sizeClass = $field->size ? 'form-control-' . $field->size : null;
@endphp

<x-admin.form.field :field="$field">
    <div class="{{ $hasGroup ? 'input-group' : '' }}">
        @if($field->prefix)
            <span class="input-group-text">{{ $field->prefix }}</span>
        @endif

        <input
            id="{{ $inputId }}"
            name="{{ $field->name }}"
            type="{{ $type }}"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
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

        @if($field->suffix)
            <span class="input-group-text">{{ $field->suffix }}</span>
        @endif

        @if($isPassword && $revealable)
            <button
                class="btn btn-outline-secondary"
                type="button"
                onclick="let $input = $(this).closest('.input-group').find('input'); let isPwd = $input.attr('type') === 'password'; $input.attr('type', isPwd ? 'text' : 'password'); $(this).find('[data-eye-open]').toggleClass('d-none', !isPwd); $(this).find('[data-eye-close]').toggleClass('d-none', isPwd);"
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
</x-admin.form.field>
