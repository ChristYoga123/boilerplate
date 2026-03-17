@props([
    'name',
    'label' => null,
    'placeholder' => 'Pilih tanggal',
    'format' => 'YYYY-MM-DD',
    'tableId' => '',
    'column' => 0,
])

@php
    $filterId = 'filter_' . $name;
    $tableId  = ltrim($tableId, '#');
@endphp

<div class="mb-3">
    @if($label)
        <label for="{{ $filterId }}" class="form-label">{{ $label }}</label>
    @endif
    <div class="input-group">
        <input
            type="text"
            id="{{ $filterId }}"
            data-filter-date
            data-table="{{ $tableId }}"
            data-column="{{ $column }}"
            data-format="{{ $format }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge(['class' => 'form-control']) }}
            autocomplete="off"
            readonly
        >
        <button type="button" class="btn btn-outline-secondary js-clear-date-filter" data-target="{{ $filterId }}">
            <i class="feather-x"></i>
        </button>
    </div>
</div>

@pushOnce('scripts')
    <script>
        $(function () {
            $('[data-filter-date]').each(function () {
                var $el = $(this);
                var fmt = $el.data('format') || 'YYYY-MM-DD';

                $el.daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    autoUpdateInput: false,
                    locale: { format: fmt, cancelLabel: 'Hapus' },
                })
                .on('apply.daterangepicker', function (ev, picker) {
                    $el.val(picker.startDate.format(fmt));
                    $('#' + $el.data('table')).DataTable()
                        .column($el.data('column'))
                        .search($el.val())
                        .draw();
                })
                .on('cancel.daterangepicker', function () {
                    $el.val('');
                    $('#' + $el.data('table')).DataTable()
                        .column($el.data('column'))
                        .search('')
                        .draw();
                });
            });

            $(document).on('click', '.js-clear-date-filter', function () {
                $('#' + $(this).data('target')).trigger('cancel.daterangepicker');
            });
        });
    </script>
@endPushOnce
