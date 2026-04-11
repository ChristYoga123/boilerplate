{{--
    Repeater — dynamically add / remove rows, any slot content.

    Props:
      name         string   — array key used as the PHP field prefix (e.g. "links")
      label        string   — section label (optional)
      addLabel     string   — text on the "add row" button (default '+ Tambah')
      minItems     int      — minimum rows; remove button hidden when at min (default 0)
      maxItems     int|null — maximum rows; add button hidden when reached (default null)
      initialCount int      — rows created on first load (default 1)

    Usage — just use plain field names inside the slot. The repeater
    automatically prefixes them as  name[index][field]  on each row:

        <x-admin.form.repeater name="links" label="Tautan">
            <x-admin.form.text-input name="label" label="Label" />
            <x-admin.form.text-input name="url"   label="URL" type="url" />
            <x-admin.form.select2    name="type"  label="Tipe" :options="$typeOptions" />
        </x-admin.form.repeater>

    PHP receives: links[0][label], links[0][url], links[1][label], …
    If rows were deleted, use array_values(request('links', [])) to re-index.

    Nested names (e.g. name="meta[key]") become links[0][meta][key].
    Multiple selects (name="tags[]") become links[0][tags][].

    Notes:
    — Old values from failed validation are NOT auto-repopulated (template is
      server-rendered once). Pass :initial-count="count(old('links', [[]]))"
      and pre-fill via JS if needed.
    — Select2 inside rows is automatically re-initialised after each add.
    — Rich-editor (Quill) inside a repeater is not supported; use textarea.
--}}

@props([
    'name',
    'label'        => null,
    'addLabel'     => '+ Tambah',
    'minItems'     => 0,
    'maxItems'     => null,
    'initialCount' => 1,
])

@php
    $repeaterId = 'repeater-' . preg_replace('/[^a-z0-9]/i', '-', $name) . '-' . substr(md5(uniqid()), 0, 6);
@endphp

<div
    class="mb-4"
    id="{{ $repeaterId }}"
    data-repeater
    data-repeater-name="{{ $name }}"
    data-min="{{ $minItems }}"
    data-max="{{ $maxItems ?? '' }}"
    data-initial="{{ $initialCount }}"
>
    @if($label)
        <label class="form-label fw-semibold mb-2">{{ $label }}</label>
    @endif

    {{-- Rows injected here by JS --}}
    <div data-repeater-list class="d-flex flex-column gap-3"></div>

    {{--
        Row template — inside <template>, not part of the live DOM.
        Slot content is rendered once by Blade; JS clones it per row
        and auto-prefixes every [name] as  repeaterName[index][field].
    --}}
    <template data-repeater-template>
        <div data-repeater-row class="border rounded p-3 bg-light">
            {{ $slot }}
            <div class="d-flex justify-content-end mt-2">
                <button type="button" data-repeater-remove class="btn btn-sm btn-outline-danger">
                    <i class="feather-trash-2 me-1"></i>Hapus
                </button>
            </div>
        </div>
    </template>

    <div class="mt-2">
        <button type="button" data-repeater-add class="btn btn-sm btn-outline-primary">
            <i class="feather-plus me-1"></i>{{ $addLabel }}
        </button>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var root = document.getElementById('{{ $repeaterId }}');
    if (!root) return;

    var list         = root.querySelector('[data-repeater-list]');
    var tmpl         = root.querySelector('[data-repeater-template]');
    var addBtn       = root.querySelector('[data-repeater-add]');
    var repeaterName = root.dataset.repeaterName;
    var min          = parseInt(root.dataset.min)  || 0;
    var max          = root.dataset.max !== '' ? parseInt(root.dataset.max) : null;
    var counter      = 0;

    // ── Name transform ───────────────────────────────────────────────────────
    // "label"      → "links[0][label]"
    // "meta[key]"  → "links[0][meta][key]"
    // "tags[]"     → "links[0][tags][]"

    function prefixName(raw, idx) {
        if (!raw) return raw;
        var multi = raw.endsWith('[]');
        var base  = multi ? raw.slice(0, -2) : raw;
        var b     = base.indexOf('[');
        var result = b === -1
            ? repeaterName + '[' + idx + '][' + base + ']'
            : repeaterName + '[' + idx + '][' + base.slice(0, b) + ']' + base.slice(b);
        return result + (multi ? '[]' : '');
    }

    // ── Stamp originals (once, on fresh clone) ───────────────────────────────

    function stamp(row) {
        row.querySelectorAll('[name]').forEach(function (el) {
            if (!el.dataset.repName) el.dataset.repName = el.name;
        });
        row.querySelectorAll('[id]').forEach(function (el) {
            if (el.id && !el.dataset.repId) el.dataset.repId = el.id;
        });
        row.querySelectorAll('[for]').forEach(function (el) {
            if (el.htmlFor && !el.dataset.repFor) el.dataset.repFor = el.htmlFor;
        });
    }

    function applyIndex(row, idx) {
        row.querySelectorAll('[data-rep-name]').forEach(function (el) {
            el.name = prefixName(el.dataset.repName, idx);
        });
        row.querySelectorAll('[data-rep-id]').forEach(function (el) {
            el.id = el.dataset.repId + '_' + idx;
        });
        row.querySelectorAll('[data-rep-for]').forEach(function (el) {
            el.htmlFor = el.dataset.repFor + '_' + idx;
        });
    }

    // ── Sync UI state ────────────────────────────────────────────────────────

    function reindex() {
        list.querySelectorAll('[data-repeater-row]').forEach(function (row, i) {
            applyIndex(row, i);
        });
        syncButtons();
    }

    function syncButtons() {
        var rows  = list.querySelectorAll('[data-repeater-row]');
        var count = rows.length;

        rows.forEach(function (row) {
            var btn = row.querySelector('[data-repeater-remove]');
            if (btn) btn.style.display = count <= min ? 'none' : '';
        });

        if (addBtn) addBtn.style.display = (max && count >= max) ? 'none' : '';
    }

    function initSelect2(row) {
        if (window.jQuery && $.fn.select2) {
            $(row).find('.js-select2-component').each(function () {
                $(this).select2({
                    theme: 'bootstrap-5',
                    placeholder: $(this).data('placeholder') || '',
                    allowClear: !$(this).prop('multiple'),
                    width: '100%',
                });
            });
        }
    }

    // ── Add row ──────────────────────────────────────────────────────────────

    function addRow() {
        if (max && list.querySelectorAll('[data-repeater-row]').length >= max) return;

        var clone = tmpl.content.cloneNode(true);
        var row   = clone.querySelector('[data-repeater-row]');

        stamp(row);
        applyIndex(row, counter++);

        row.querySelector('[data-repeater-remove]').addEventListener('click', function () {
            row.remove();
            reindex();
        });

        list.appendChild(clone);
        initSelect2(row);
        syncButtons();
    }

    // ── Boot ─────────────────────────────────────────────────────────────────

    var initial = Math.max(parseInt(root.dataset.initial) || 1, min);
    for (var i = 0; i < initial; i++) addRow();

    addBtn.addEventListener('click', addRow);
})();
</script>
@endpush
