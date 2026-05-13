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
                        hint="Preview gambar muncul sebelum submit."
                        accept="image/*"
                    />

                    <x-admin.form.file-input
                        name="gallery[]"
                        label="Galeri"
                        hint="Contoh upload multiple image."
                        accept="image/*"
                        multiple
                    />
                </x-admin.form.grid>

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
@endsection
