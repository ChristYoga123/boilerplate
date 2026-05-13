@props([
    'options' => [],
    'inline' => false,
])

@php
    $field = \App\Support\AdminFormField::make($attributes);
    $selectedValue = $field->oldValue();
    $hasError = $field->hasError($errors ?? null);
@endphp

<x-admin.form.field :field="$field" :for="false">
    <div class="{{ $inline ? 'd-flex flex-wrap gap-3' : 'd-flex flex-column gap-2' }}">
        @foreach($options as $optionValue => $optionLabel)
            @php
                $optionId = $field->id . '-' . preg_replace('/[^A-Za-z0-9\-_:.]+/', '-', (string) $optionValue);
            @endphp
            <div class="form-check {{ $inline ? 'mb-0' : '' }}">
                <input
                    id="{{ $optionId }}"
                    name="{{ $field->name }}"
                    type="radio"
                    value="{{ $optionValue }}"
                    @checked((string) $selectedValue === (string) $optionValue)
                    @if($field->required) required @endif
                    @if($field->disabled) disabled @endif
                    {{ $field->controlAttributes()->class([
                        'form-check-input',
                        'is-invalid' => $hasError,
                    ]) }}
                >
                <label class="form-check-label c-pointer" for="{{ $optionId }}">
                    {{ $optionLabel }}
                </label>
            </div>
        @endforeach

        {{ $slot }}
    </div>
</x-admin.form.field>
