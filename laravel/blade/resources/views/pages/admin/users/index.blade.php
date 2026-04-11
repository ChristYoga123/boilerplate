@extends('layouts.admin.app')

@section('header_content')
    <x-admin.ui.button :href="route('admin.users.create')" variant="primary" icon="feather feather-plus" permission="users.create">
        Tambah {{ $title }}
    </x-admin.ui.button>
@endsection

@section('content')
    <x-admin.ui.container>
        <x-admin.table.filters>
            <x-admin.table.select-filter
                name="role"
                label="Role"
                tableId="user-table"
                column="2"
                :options="$roleOptions"
            />
        </x-admin.table.filters>

        <x-admin.table.container>
            {!! $dataTable->table(['class' => 'table table-hover', 'style' => 'width:100%'], true) !!}
        </x-admin.table.container>
    </x-admin.ui.container>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
