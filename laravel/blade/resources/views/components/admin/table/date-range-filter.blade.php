@props([
    'name',
    'label' => null,
    'placeholder' => 'Pilih rentang tanggal',
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
            data-filter-daterange
            data-table="{{ $tableId }}"
            data-column="{{ $column }}"
            data-format="{{ $format }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge(['class' => 'form-control']) }}
            autocomplete="off"
            readonly
        >
        <button type="button" class="btn btn-outline-secondary js-clear-daterange" data-target="{{ $filterId }}">
            <i class="feather-x"></i>
        </button>
    </div>
</div>

@pushOnce('scripts')
    <script>
        $(function () {
            window._dtRangeFilters = window._dtRangeFilters || {};

            if (!window._dtRangeFilterRegistered) {
                window._dtRangeFilterRegistered = true;
                $.fn.dataTable.ext.search.push(function (settings, data) {
                    var filters = window._dtRangeFilters[settings.nTable.id];
                    if (!filters) return true;
                    return Object.keys(filters).every(function (col) {
                        var f = filters[col];
                        if (!f.start && !f.end) return true;
                        var val = data[parseInt(col)] || '';
                        if (f.start && val < f.start) return false;
                        if (f.end   && val > f.end)   return false;
                        return true;
                    });
                });
            }

            function applyRange($el, start, end) {
                var tableId = $el.data('table');
                var col     = String($el.data('column'));
                window._dtRangeFilters[tableId]      = window._dtRangeFilters[tableId] || {};
                window._dtRangeFilters[tableId][col] = { start: start, end: end };
                $('#' + tableId).DataTable().draw();
            }

            $('[data-filter-daterange]').each(function () {
                var $el = $(this);
                var fmt = $el.data('format') || 'YYYY-MM-DD';

                $el.daterangepicker({
                    autoUpdateInput: false,
                    locale: { format: fmt, separator: ' – ', cancelLabel: 'Hapus' },
                })
                .on('apply.daterangepicker', function (ev, picker) {
                    var start = picker.startDate.format(fmt);
                    var end   = picker.endDate.format(fmt);
                    $el.val(start + ' – ' + end);
                    applyRange($el, start, end);
                })
                .on('cancel.daterangepicker', function () {
                    $el.val('');
                    applyRange($el, '', '');
                });
            });

            $(document).on('click', '.js-clear-daterange', function () {
                var $input = $('#' + $(this).data('target'));
                $input.val('');
                applyRange($input, '', '');
            });
        });
    </script>
@endPushOnce
