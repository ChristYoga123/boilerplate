@extends('layouts.admin.app')

@section('header_content')
    <x-admin.ui.button :href="route('admin.roles.create')" variant="primary" icon="feather feather-plus" permission="roles.create">
        Tambah {{ $title }}
    </x-admin.ui.button>
@endsection

@section('content')
    <x-admin.ui.container>
        <x-admin.table.container>
            {!! $dataTable->table(['class' => 'table table-hover', 'style' => 'width:100%'], true) !!}
        </x-admin.table.container>
    </x-admin.ui.container>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
