@extends('layouts.admin.form', [
    'action' => isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store'),
    'method' => isset($role) ? 'PUT' : 'POST',
])

@section('form_content')
    @php
        $selectedIds = array_map('intval', (array) old('permissions', isset($role) ? $role->permissions->pluck('id')->toArray() : []));
        $order = ['index', 'create', 'edit', 'delete', 'force_delete', 'restore'];

        $grouped = $permissions
            ->groupBy(fn ($p) => explode('.', $p->name)[0])
            ->map(function ($perms) use ($order) {
                return $perms->sortBy(function ($permission) use ($order) {
                    $action = explode('.', $permission->name)[1] ?? $permission->name;
                    $idx = array_search($action, $order, true);
                    return $idx === false ? PHP_INT_MAX : $idx;
                });
            });
    @endphp

    {{-- Section 1: Nama Role --}}
    <x-admin.form.section title="Informasi Role">
        <x-admin.form.text-input
            name="name"
            label="Nama Role"
            placeholder="Masukkan nama role"
            :value="$role->name ?? ''"
            required
        />
    </x-admin.form.section>

    {{-- Section 2: Permissions --}}
    <x-admin.form.section title="Permissions">

        @if($errors->has('permissions') || $errors->has('permissions.*'))
            <div class="text-danger small mb-3">{{ $errors->first('permissions') }}</div>
        @endif

        <table class="table table-bordered align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 180px">
                        <div class="form-check mb-0">
                            <input type="checkbox" id="select-all" class="form-check-input">
                            <label class="form-check-label fw-semibold c-pointer" for="select-all">Pilih Semua</label>
                        </div>
                    </th>
                    <th>Permission</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($grouped as $resource => $perms)
                    <tr>
                        <td>
                            <div class="form-check mb-0">
                                <input
                                    type="checkbox"
                                    id="resource-{{ $resource }}"
                                    class="form-check-input resource-toggle"
                                    data-resource="{{ $resource }}"
                                >
                                <label class="form-check-label fw-semibold c-pointer text-capitalize" for="resource-{{ $resource }}">
                                    {{ $resource }}
                                </label>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach ($perms as $permission)
                                    @php $action = explode('.', $permission->name)[1] ?? $permission->name; @endphp
                                    <div class="form-check mb-0">
                                        <input
                                            type="checkbox"
                                            id="perm-{{ $permission->id }}"
                                            name="permissions[]"
                                            value="{{ $permission->id }}"
                                            class="form-check-input perm-check"
                                            data-resource="{{ $resource }}"
                                            {{ in_array($permission->id, $selectedIds) ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label c-pointer" for="perm-{{ $permission->id }}">
                                            {{ str_replace('_', ' ', $action) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-admin.form.section>
@endsection

@push('scripts')
<script>
    function getResourcePerms(resource) {
        return $('.perm-check[data-resource="' + resource + '"]');
    }

    function syncResourceToggle(resource) {
        var $perms = getResourcePerms(resource);
        var total = $perms.length;
        var checked = $perms.filter(':checked').length;
        var $toggle = $('#resource-' + resource);
        $toggle.prop('checked', checked === total && total > 0);
        $toggle.prop('indeterminate', checked > 0 && checked < total);
    }

    function syncSelectAll() {
        var total = $('.perm-check').length;
        var checked = $('.perm-check:checked').length;
        $('#select-all').prop('checked', checked === total && total > 0);
        $('#select-all').prop('indeterminate', checked > 0 && checked < total);
    }

    // Resource toggle → check all perms in that resource
    $(document).on('change', '.resource-toggle', function () {
        var resource = $(this).data('resource');
        getResourcePerms(resource).prop('checked', this.checked);
        $(this).prop('indeterminate', false);
        syncSelectAll();
    });

    // Individual perm → sync its resource toggle + select all
    $(document).on('change', '.perm-check', function () {
        syncResourceToggle($(this).data('resource'));
        syncSelectAll();
    });

    // Global select all
    $('#select-all').on('change', function () {
        var checked = this.checked;
        $('.perm-check').prop('checked', checked);
        $('.resource-toggle').prop('checked', checked).prop('indeterminate', false);
    });

    // Init on page load
    $(function () {
        $('.resource-toggle').each(function () {
            syncResourceToggle($(this).data('resource'));
        });
        syncSelectAll();
    });
</script>
@endpush
