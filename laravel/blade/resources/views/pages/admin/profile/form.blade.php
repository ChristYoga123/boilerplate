@extends('layouts.admin.form', [
    'action'    => route('admin.profile.update'),
    'method'    => 'PUT',
    'multipart' => true,
])

@section('form_content')
    <x-admin.form.section title="Informasi Profil">
        <x-admin.form.grid cols="2">
            <x-admin.form.text-input
                name="name"
                label="Nama"
                placeholder="Masukkan nama"
                :value="$user->name"
                required
            />
            <x-admin.form.text-input
                name="email"
                type="email"
                label="Email"
                placeholder="Masukkan email"
                :value="$user->email"
                required
            />
        </x-admin.form.grid>

        <x-admin.form.grid cols="1">
            <x-admin.form.file-input
                name="avatar"
                label="Foto Profil"
                accept="image/*"
                :currentImages="$user->avatar ? [asset('storage/' . $user->avatar)] : []"
            />
        </x-admin.form.grid>

        <x-admin.form.grid cols="2">
            <x-admin.form.text-input
                name="password"
                type="password"
                label="Password Baru"
                placeholder="Kosongkan jika tidak diubah"
                revealable
            />
            <x-admin.form.text-input
                name="password_confirmation"
                type="password"
                label="Konfirmasi Password Baru"
                placeholder="Ulangi password baru"
            />
        </x-admin.form.grid>
    </x-admin.form.section>
@endsection
