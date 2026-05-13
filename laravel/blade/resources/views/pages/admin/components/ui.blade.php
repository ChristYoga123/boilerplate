@extends('layouts.admin.app')

@section('header_content')
    <x-admin.ui.button icon="feather-plus" modal="#componentModal">
        Buka Modal
    </x-admin.ui.button>
@endsection

@section('content')
    <div class="col-xxl-3 col-md-6">
        <x-admin.ui.widget
            title="Total Pengguna"
            value="1.248"
            icon="feather-users"
        >
            <div class="d-flex align-items-center gap-2">
                <x-admin.ui.badge variant="success" soft icon="feather-trending-up">12%</x-admin.ui.badge>
                <span class="fs-12 text-muted">naik bulan ini</span>
            </div>
        </x-admin.ui.widget>
    </div>

    <div class="col-xxl-3 col-md-6">
        <x-admin.ui.widget
            title="Transaksi"
            value="842"
            icon="feather-shopping-bag"
        >
            <div class="d-flex align-items-center gap-2">
                <x-admin.ui.badge variant="primary" soft icon="feather-activity">Stabil</x-admin.ui.badge>
                <span class="fs-12 text-muted">7 hari terakhir</span>
            </div>
        </x-admin.ui.widget>
    </div>

    <div class="col-xxl-3 col-md-6">
        <x-admin.ui.widget
            title="Approval"
            value="36"
            icon="feather-check-square"
        >
            <div class="d-flex align-items-center gap-2">
                <x-admin.ui.badge variant="warning" soft icon="feather-clock">Menunggu</x-admin.ui.badge>
                <span class="fs-12 text-muted">perlu review</span>
            </div>
        </x-admin.ui.widget>
    </div>

    <div class="col-xxl-3 col-md-6">
        <x-admin.ui.widget
            title="Arsip"
            value="128"
            icon="feather-archive"
        >
            <div class="d-flex align-items-center gap-2">
                <x-admin.ui.badge variant="secondary" soft icon="feather-folder">Tersimpan</x-admin.ui.badge>
                <span class="fs-12 text-muted">data lama</span>
            </div>
        </x-admin.ui.widget>
    </div>

    <div class="col-lg-12">
        <x-admin.form.section
            title="Action Bar, Buttons & Dropdown"
            description="Komponen command area untuk header kecil, toolbar, dan aksi halaman."
        >
            <x-admin.ui.action-bar
                title="Daftar Produk"
                description="Contoh toolbar index dengan badge, dropdown, dan tombol utama."
            >
                <x-admin.ui.badge variant="success" soft icon="feather-check-circle">
                    24 Aktif
                </x-admin.ui.badge>

                <x-admin.ui.dropdown-action label="Export" icon="feather-download">
                    <a href="javascript:void(0);" class="dropdown-item">
                        <i class="feather-file-text me-2"></i>CSV
                    </a>
                    <a href="javascript:void(0);" class="dropdown-item">
                        <i class="feather-file me-2"></i>Excel
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="javascript:void(0);" class="dropdown-item">
                        <i class="feather-printer me-2"></i>Print
                    </a>
                </x-admin.ui.dropdown-action>

                <x-admin.ui.button icon="feather-plus" modal="#componentModal">
                    Tambah Data
                </x-admin.ui.button>
            </x-admin.ui.action-bar>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <x-admin.ui.button icon="feather-save">Primary</x-admin.ui.button>
                <x-admin.ui.button variant="secondary" icon="feather-settings">Secondary</x-admin.ui.button>
                <x-admin.ui.button variant="success" icon="feather-check">Success</x-admin.ui.button>
                <x-admin.ui.button variant="danger" outline icon="feather-trash-2">Outline Danger</x-admin.ui.button>
                <x-admin.ui.button variant="light" size="sm" icon="feather-filter">Small</x-admin.ui.button>
                <x-admin.ui.button as="icon" href="javascript:void(0)" icon="feather-edit-3" title="Icon Action" />
            </div>
        </x-admin.form.section>
    </div>

    <div class="col-lg-6">
        <x-admin.form.section
            title="Badges"
            description="Status label solid dan soft untuk table, card, atau ringkasan data."
        >
            <div class="d-flex flex-wrap gap-2">
                <x-admin.ui.badge variant="primary">Primary</x-admin.ui.badge>
                <x-admin.ui.badge variant="success" soft icon="feather-check-circle">Aktif</x-admin.ui.badge>
                <x-admin.ui.badge variant="warning" soft icon="feather-clock">Pending</x-admin.ui.badge>
                <x-admin.ui.badge variant="danger" soft icon="feather-alert-circle">Gagal</x-admin.ui.badge>
                <x-admin.ui.badge variant="secondary" soft>Arsip</x-admin.ui.badge>
            </div>
        </x-admin.form.section>
    </div>

    <div class="col-lg-6">
        <x-admin.form.section
            title="Breadcrumb & Title"
            description="Komponen kecil untuk judul dan navigasi lokal."
        >
            <x-admin.ui.title title="Detail Pengguna" />
            <div class="mt-3">
                <x-admin.ui.breadcrumb
                    :items="[
                        ['label' => 'Home', 'url' => route('admin.dashboard')],
                        ['label' => 'Pengguna', 'url' => route('admin.users.index')],
                        ['label' => 'Detail'],
                    ]"
                />
            </div>
        </x-admin.form.section>
    </div>

    <div class="col-lg-12">
        <x-admin.form.section
            title="Empty State"
            description="Tampilan saat data kosong, hasil pencarian nihil, atau user belum membuat item."
        >
            <x-admin.ui.empty-state
                title="Belum ada produk"
                description="Gunakan empty state untuk memberi konteks singkat dan command lanjutan."
                icon="feather-inbox"
                class="border rounded"
            >
                <x-admin.ui.button icon="feather-plus" modal="#componentModal">
                    Tambah Produk
                </x-admin.ui.button>
                <x-admin.ui.button variant="light" icon="feather-upload">
                    Import
                </x-admin.ui.button>
            </x-admin.ui.empty-state>
        </x-admin.form.section>
    </div>

    <x-admin.ui.modal id="componentModal" title="Contoh Modal" size="lg" static>
        <x-admin.form.grid cols="2">
            <x-admin.form.text-input
                name="modal_name"
                label="Nama"
                placeholder="Masukkan nama"
            />

            <x-admin.form.select2
                name="modal_status"
                label="Status"
                :options="[
                    'active' => 'Aktif',
                    'pending' => 'Pending',
                    'archived' => 'Arsip',
                ]"
                placeholder="Pilih status"
            />
        </x-admin.form.grid>

        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <x-admin.ui.button type="button" icon="feather-check" data-bs-dismiss="modal">
                Simpan Preview
            </x-admin.ui.button>
        </x-slot:footer>
    </x-admin.ui.modal>
@endsection
