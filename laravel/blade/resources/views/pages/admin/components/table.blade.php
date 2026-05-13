@extends('layouts.admin.app')

@php
    $statusOptions = [
        'Aktif' => 'Aktif',
        'Pending' => 'Pending',
        'Arsip' => 'Arsip',
    ];

    $roleOptions = [
        'Admin' => 'Admin',
        'Manager' => 'Manager',
        'Staff' => 'Staff',
    ];
@endphp

@section('content')
    <x-admin.ui.container>
        <div class="card-header">
            <div>
                <h5 class="card-title mb-1">Table Wrapper & Filters</h5>
                <p class="card-description mb-0">
                    Komponen filter standar untuk halaman index berbasis DataTables.
                </p>
            </div>
        </div>

        <x-admin.table.filters>
            <x-admin.table.select-filter
                name="status"
                label="Status"
                tableId="component-table-demo"
                column="3"
                :options="$statusOptions"
            />

            <x-admin.table.select-filter
                name="role"
                label="Role"
                tableId="component-table-demo"
                column="2"
                :options="$roleOptions"
            />

            <div class="col-md-3">
                <x-admin.table.date-filter
                    name="joined_at"
                    label="Tanggal Gabung"
                    tableId="component-table-demo"
                    column="4"
                />
            </div>

            <div class="col-md-3">
                <x-admin.table.date-range-filter
                    name="created_range"
                    label="Rentang Dibuat"
                    tableId="component-table-demo"
                    column="5"
                />
            </div>
        </x-admin.table.filters>

        <x-admin.table.container>
            <table id="component-table-demo" class="table table-hover align-middle mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Tanggal Gabung</th>
                        <th>Dibuat</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="fw-semibold">Admin Demo</div>
                            <div class="fs-12 text-muted">Akun utama sistem</div>
                        </td>
                        <td>admin@example.test</td>
                        <td>Admin</td>
                        <td><x-admin.ui.badge variant="success" soft>Aktif</x-admin.ui.badge></td>
                        <td>2026-05-01</td>
                        <td>2026-05-01</td>
                        <td>
                            <div class="hstack gap-2 justify-content-end">
                                <x-admin.table.edit-button href="javascript:void(0)" title="Edit data demo" />
                                <x-admin.table.delete-button action="javascript:void(0)" name="Admin Demo" title="Hapus data demo" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="fw-semibold">Manager Operasional</div>
                            <div class="fs-12 text-muted">Koordinator cabang</div>
                        </td>
                        <td>manager@example.test</td>
                        <td>Manager</td>
                        <td><x-admin.ui.badge variant="warning" soft>Pending</x-admin.ui.badge></td>
                        <td>2026-05-03</td>
                        <td>2026-05-03</td>
                        <td>
                            <div class="hstack gap-2 justify-content-end">
                                <x-admin.table.edit-button href="javascript:void(0)" title="Edit data demo" />
                                <x-admin.table.delete-button action="javascript:void(0)" name="Manager Operasional" title="Hapus data demo" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="fw-semibold">Staff Gudang</div>
                            <div class="fs-12 text-muted">Input transaksi harian</div>
                        </td>
                        <td>staff@example.test</td>
                        <td>Staff</td>
                        <td><x-admin.ui.badge variant="secondary" soft>Arsip</x-admin.ui.badge></td>
                        <td>2026-05-08</td>
                        <td>2026-05-08</td>
                        <td>
                            <div class="hstack gap-2 justify-content-end">
                                <x-admin.table.edit-button href="javascript:void(0)" title="Edit data demo" />
                                <x-admin.table.delete-button action="javascript:void(0)" name="Staff Gudang" title="Hapus data demo" />
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </x-admin.table.container>
    </x-admin.ui.container>

    <div class="col-lg-12">
        <x-admin.form.section
            title="Action Buttons"
            description="Tombol aksi kecil untuk kolom action DataTables."
        >
            <div class="d-flex flex-wrap align-items-center gap-2">
                <x-admin.table.edit-button href="javascript:void(0)" title="Edit" />
                <x-admin.table.delete-button action="javascript:void(0)" name="Data Demo" title="Hapus" />
            </div>
        </x-admin.form.section>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            $('#component-table-demo').DataTable({
                pageLength: 10,
                order: [[0, 'asc']],
                dom: '<"d-flex align-items-center justify-content-between px-3 pt-3"lf>rt<"d-flex align-items-center justify-content-between px-3 pb-3"ip>',
                language: {
                    search: '',
                    searchPlaceholder: 'Cari...',
                    lengthMenu: '_MENU_',
                },
            });
        });
    </script>
@endpush
