@props([
    'options' => [], // array of value => label
    'multiple' => false,
    'placeholder' => 'Pilih opsi...',
])

@php
    $field = \App\Support\AdminFormField::make($attributes);
    $inputId = $field->id;
    $hasError = $field->hasError($errors ?? null);
    
    // Normalize value for single/multi select
    $oldValue = $field->oldValue();
    $selectedArray = is_array($oldValue) ? $oldValue : (is_scalar($oldValue) ? [$oldValue] : []);
@endphp

<x-admin.form.field :field="$field">
    <select
        id="{{ $inputId }}"
        name="{{ $field->name }}"
        @if($multiple) multiple @endif
        @if($field->required) required @endif
        @if($field->disabled) disabled @endif
        {{ $field->controlAttributes()->class([
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
</x-admin.form.field>

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
