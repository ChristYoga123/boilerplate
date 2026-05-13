@extends('layouts.admin.app')

@php
    $roleOptions = [
        'admin' => 'Admin',
        'manager' => 'Manager',
        'staff' => 'Staff',
    ];

    $statusOptions = [
        'draft' => 'Draft',
        'active' => 'Aktif',
        'archived' => 'Arsip',
    ];

    $typeOptions = [
        'personal' => 'Personal',
        'company' => 'Perusahaan',
        'partner' => 'Partner',
    ];
@endphp

@section('content')
    <div class="col-lg-12">
        <x-admin.form.section
            title="Basic Fields"
            description="Komponen dasar untuk form CRUD standar."
        >
            <x-admin.form.container
                :action="route('admin.form-components')"
                method="GET"
                :show-cancel="false"
                submit-label="Preview Submit"
            >
                <x-admin.form.grid cols="2">
                    <x-admin.form.text-input
                        name="name"
                        label="Nama"
                        placeholder="Masukkan nama"
                        hint="Contoh hint global dari field wrapper."
                        required
                    />

                    <x-admin.form.text-input
                        name="email"
                        type="email"
                        label="Email"
                        placeholder="nama@email.com"
                        autocomplete="email"
                        required
                    />
                </x-admin.form.grid>

                <x-admin.form.grid cols="2">
                    <x-admin.form.text-input
                        name="password"
                        type="password"
                        label="Password"
                        placeholder="Masukkan password"
                        revealable
                        required
                    />

                    <x-admin.form.select2
                        name="status"
                        label="Status"
                        :options="$statusOptions"
                        placeholder="Pilih status"
                        required
                    />
                </x-admin.form.grid>

                <x-admin.form.textarea
                    name="description"
                    label="Deskripsi"
                    placeholder="Tulis deskripsi singkat"
                    hint="Textarea mengikuti pola label, hint, dan error yang sama."
                    rows="5"
                />

                <x-admin.form.checkbox
                    name="is_active"
                    label="Aktifkan data ini"
                    hint="Checkbox juga mendukung hint dan error global."
                />
            </x-admin.form.container>
        </x-admin.form.section>
    </div>

    <div class="col-lg-12">
        <x-admin.form.section
            title="Field Props & Input Variants"
            description="Contoh props global dan field yang sering dipakai di form operasional."
        >
            <x-admin.form.container
                :action="route('admin.form-components')"
                method="GET"
                :show-cancel="false"
                submit-label="Preview Variants"
            >
                <x-admin.form.grid cols="3">
                    <x-admin.form.date-input
                        name="started_at"
                        label="Tanggal Mulai"
                        hint="Date input memakai wrapper global yang sama."
                        required
                    />

                    <x-admin.form.number-input
                        name="stock"
                        label="Stok"
                        min="0"
                        step="1"
                        suffix="pcs"
                        hint="Ketik 1000000, tampilan menjadi 1.000.000."
                        required
                    />

                    <x-admin.form.money-input
                        name="price"
                        label="Harga"
                        hint="Prefix default money adalah Rp."
                        min="0"
                        step="500"
                        required
                    />
                </x-admin.form.grid>

                <x-admin.form.grid cols="2">
                    <x-admin.form.number-input
                        name="weight"
                        label="Berat"
                        decimal-scale="2"
                        suffix="kg"
                        hint="Decimal memakai koma, misalnya 1.250,75."
                    />

                    <x-admin.form.money-input
                        name="price_decimal"
                        label="Harga Decimal"
                        decimal-scale="2"
                        step="0.01"
                        hint="Nilai submit tetap raw decimal seperti 1250.75."
                    />
                </x-admin.form.grid>

                <x-admin.form.grid cols="2">
                    <x-admin.form.text-input
                        name="code"
                        label="Kode"
                        prefix="#"
                        size="sm"
                        placeholder="USR-001"
                    />

                    <x-admin.form.radio-group
                        name="account_type"
                        label="Tipe Akun"
                        :options="$typeOptions"
                        inline
                        required
                    />
                </x-admin.form.grid>

                <x-admin.form.switch
                    name="send_notification"
                    label="Kirim notifikasi setelah submit"
                    hint="Switch cocok untuk pilihan aktif/nonaktif."
                />
            </x-admin.form.container>
        </x-admin.form.section>
    </div>

    <div class="col-lg-12">
        <x-admin.form.section
            title="Upload & Rich Content"
            description="Contoh input file dengan preview dan rich editor."
        >
            <x-admin.form.container
                :action="route('admin.form-components')"
                method="GET"
                multipart
                :show-cancel="false"
                submit-label="Preview Upload"
            >
                <x-admin.form.grid cols="2">
                    <x-admin.form.file-input
                        name="avatar"
                        label="Foto Profil"
                        hint="Preview gambar muncul sebelum submit, file lama bisa ditandai untuk dihapus."
                        accept="image/*"
                        :current-images="[asset('assets/admin/images/avatar/1.png')]"
                        remove
                    />

                    <x-admin.form.file-input
                        name="gallery[]"
                        label="Galeri"
                        hint="Contoh upload multiple image."
                        accept="image/*"
                        multiple
                    />
                </x-admin.form.grid>

                <x-admin.form.file-input
                    name="document"
                    mode="file"
                    label="Dokumen"
                    hint="Mode file menampilkan nama dokumen, bukan thumbnail gambar."
                    accept=".pdf,.doc,.docx,.xls,.xlsx"
                    :current-files="[
                        ['url' => '#', 'name' => 'proposal-lama.pdf', 'value' => 'proposal-lama.pdf'],
                    ]"
                    remove
                />

                <x-admin.form.rich-editor
                    name="body"
                    label="Konten"
                    hint="Rich editor cocok untuk field artikel atau deskripsi panjang."
                    placeholder="Tulis konten di sini..."
                    height="220"
                />
            </x-admin.form.container>
        </x-admin.form.section>
    </div>

    <div class="col-lg-12">
        <x-admin.form.section
            title="Repeater"
            description="Container dinamis untuk daftar field berulang."
        >
            <x-admin.form.container
                :action="route('admin.form-components')"
                method="GET"
                :show-cancel="false"
                submit-label="Preview Repeater"
            >
                <x-admin.form.repeater
                    name="links"
                    label="Tautan"
                    add-label="Tambah Tautan"
                    :initial-count="2"
                >
                    <x-admin.form.grid cols="2">
                        <x-admin.form.text-input
                            name="label"
                            label="Label"
                            placeholder="Website"
                        />

                        <x-admin.form.text-input
                            name="url"
                            type="url"
                            label="URL"
                            placeholder="https://example.com"
                        />
                    </x-admin.form.grid>

                    <x-admin.form.select2
                        name="type"
                        label="Tipe"
                        :options="[
                            'website' => 'Website',
                            'social' => 'Sosial Media',
                            'document' => 'Dokumen',
                        ]"
                    />
                </x-admin.form.repeater>
            </x-admin.form.container>
        </x-admin.form.section>
    </div>

    <div class="col-lg-12">
        <x-admin.form.section
            title="Wizard"
            description="Stepper untuk form panjang tanpa memecah submit Laravel."
        >
            <x-admin.form.container
                :action="route('admin.form-components')"
                method="GET"
            >
                <x-admin.form.wizard
                    title="Tambah Pengguna"
                    description="Semua step tetap berada dalam satu form."
                    submit-label="Preview Wizard"
                >
                    <x-admin.form.wizard-step
                        title="Profil"
                        description="Data identitas pengguna"
                    >
                        <x-admin.form.grid cols="2">
                            <x-admin.form.text-input
                                name="wizard_name"
                                label="Nama"
                                placeholder="Masukkan nama"
                                required
                            />

                            <x-admin.form.text-input
                                name="wizard_email"
                                type="email"
                                label="Email"
                                placeholder="nama@email.com"
                                required
                            />
                        </x-admin.form.grid>
                    </x-admin.form.wizard-step>

                    <x-admin.form.wizard-step
                        title="Akses"
                        description="Role dan status akun"
                    >
                        <x-admin.form.grid cols="2">
                            <x-admin.form.select2
                                name="wizard_roles[]"
                                label="Role"
                                :options="$roleOptions"
                                multiple
                                required
                            />

                            <x-admin.form.select2
                                name="wizard_status"
                                label="Status"
                                :options="$statusOptions"
                                required
                            />
                        </x-admin.form.grid>
                    </x-admin.form.wizard-step>

                    <x-admin.form.wizard-step
                        title="Keamanan"
                        description="Password awal pengguna"
                    >
                        <x-admin.form.grid cols="2">
                            <x-admin.form.text-input
                                name="wizard_password"
                                type="password"
                                label="Password"
                                revealable
                                required
                            />

                            <x-admin.form.text-input
                                name="wizard_password_confirmation"
                                type="password"
                                label="Konfirmasi Password"
                                required
                            />
                        </x-admin.form.grid>
                    </x-admin.form.wizard-step>
                </x-admin.form.wizard>
            </x-admin.form.container>
        </x-admin.form.section>
    </div>

    <div class="col-lg-12">
        <x-admin.form.section
            title="Index UI Helpers"
            description="Komponen kecil untuk halaman index, table toolbar, status, dan empty state."
        >
            <x-admin.ui.action-bar
                title="Daftar Pengguna"
                description="Action bar menyatukan judul kecil, filter, dan command utama."
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
                </x-admin.ui.dropdown-action>

                <x-admin.ui.button icon="feather-plus">
                    Tambah Data
                </x-admin.ui.button>
            </x-admin.ui.action-bar>

            <div class="table-responsive mb-4">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="fw-semibold">Admin Demo</div>
                                <div class="fs-12 text-muted">admin@example.test</div>
                            </td>
                            <td>
                                <x-admin.ui.badge variant="success" soft>Aktif</x-admin.ui.badge>
                            </td>
                            <td class="text-end">
                                <x-admin.ui.dropdown-action
                                    label=""
                                    icon="feather-more-vertical"
                                    variant="light"
                                    button-class="btn-sm"
                                >
                                    <a href="javascript:void(0);" class="dropdown-item">
                                        <i class="feather-edit-3 me-2"></i>Edit
                                    </a>
                                    <a href="javascript:void(0);" class="dropdown-item text-danger">
                                        <i class="feather-trash-2 me-2"></i>Hapus
                                    </a>
                                </x-admin.ui.dropdown-action>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <x-admin.ui.empty-state
                title="Belum ada data"
                description="Empty state dipakai saat index kosong, hasil filter nihil, atau user belum membuat data pertama."
                icon="feather-inbox"
                class="border rounded"
            >
                <x-admin.ui.button icon="feather-plus">
                    Tambah Data
                </x-admin.ui.button>
            </x-admin.ui.empty-state>
        </x-admin.form.section>
    </div>
@endsection
