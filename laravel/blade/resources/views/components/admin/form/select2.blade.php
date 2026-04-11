@props([
    'name',
    'label' => null,
    'options' => [], // array of value => label
    'value' => null,
    'required' => false,
    'multiple' => false,
    'placeholder' => 'Pilih opsi...',
])

@php
    $inputId = $attributes->get('id', $name);
    $hasError = $errors->has(str_replace('[]', '', $name));
    $wrapperClasses = 'mb-3';
    
    // Normalize value for single/multi select
    $oldValue = old(str_replace('[]', '', $name), $value);
    $selectedArray = is_array($oldValue) ? $oldValue : (is_scalar($oldValue) ? [$oldValue] : []);
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

    <select
        id="{{ $inputId }}"
        name="{{ $name }}"
        @if($multiple) multiple @endif
        @if($required) required @endif
        {{ $attributes->class([
            'form-select js-select2-component',
            'is-invalid border-danger' => $hasError,
        ]) }}
        data-placeholder="{{ $placeholder }}"
    >
        @if(!$multiple)
            <option value=""></option>
        @endif
        
        @foreach($options as $opValue => $opLabel)
            <option value="{{ $opValue }}" @selected(in_array($opValue, $selectedArray))>
                {{ $opLabel }}
            </option>
        @endforeach
        
        {{ $slot }}
    </select>

    @error(str_replace('[]', '', $name))
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>

@once
    @push('scripts')
        <script>
            $(document).ready(function() {
                if($.fn.select2) {
                    $('.js-select2-component').each(function() {
                        $(this).select2({
                            placeholder: $(this).data('placeholder'),
                            allowClear: !$(this).prop('multiple'),
                            width: '100%',
                            theme: 'bootstrap-5'
                        });
                    });
                }
            });
        </script>
    @endpush
@endonce
