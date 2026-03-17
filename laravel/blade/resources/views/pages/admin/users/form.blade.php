@extends('layouts.admin.form', [
    'action' => isset($user) ? route('admin.users.update', $user) : route('admin.users.store'),
    'method' => isset($user) ? 'PUT' : 'POST',
])

@section('form_content')
    <x-admin.form.section title="Informasi Pengguna">
        <x-admin.form.grid cols="2">
            <x-admin.form.text-input
                name="name"
                label="Nama"
                placeholder="Masukkan nama"
                :value="$user->name ?? ''"
                required
            />
            <x-admin.form.text-input
                name="email"
                type="email"
                label="Email"
                placeholder="Masukkan email"
                :value="$user->email ?? ''"
                required
            />
        </x-admin.form.grid>
        <x-admin.form.grid cols="2">
            <x-admin.form.text-input
                name="password"
                type="password"
                label="Password"
                placeholder="Masukkan password"
                :required="!isset($user)"
                revealable
            />
            <x-admin.form.text-input
                name="password_confirmation"
                type="password"
                label="Konfirmasi Password"
                placeholder="Ulangi password"
                :required="!isset($user)"
            />
        </x-admin.form.grid>
    </x-admin.form.section>
@endsection
