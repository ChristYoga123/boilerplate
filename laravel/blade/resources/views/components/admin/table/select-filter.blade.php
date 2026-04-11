@props([
    'name',
    'label' => null,
    'placeholder' => 'Semua',
    'options' => [],
    'tableId' => '',
    'column' => 0,
    'cols' => 3,
])

@php
    $filterId = 'filter_' . $name;
    $tableId  = ltrim($tableId, '#');
@endphp

<div class="col-md-{{ $cols }}">
    @if($label)
        <label for="{{ $filterId }}" class="form-label">{{ $label }}</label>
    @endif
    <select
        id="{{ $filterId }}"
        data-filter-select
        data-table="{{ $tableId }}"
        data-column="{{ $column }}"
        data-placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'form-select']) }}
    >
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $value => $text)
            <option value="{{ $value }}">{{ $text }}</option>
        @endforeach
    </select>
</div>

@pushOnce('scripts')
    <script>
        $(function () {
            $('[data-filter-select]').each(function () {
                $(this).select2({
                    theme: 'bootstrap-5',
                    placeholder: $(this).data('placeholder'),
                    allowClear: true,
                    width: '100%',
                    minimumResultsForSearch: 0,
                    dropdownParent: $(document.body),
                });
            });

            $(document).on('change', '[data-filter-select]', function () {
                var value = $(this).val() || '';
                $('#' + $(this).data('table')).DataTable()
                    .column($(this).data('column'))
                    .search(value)
                    .draw();
            });
        });
    </script>
@endPushOnce
