@extends('layouts.admin.app')

@section('content')
<div class="col-lg-12">
    <form
        action="{{ $action }}"
        method="POST"
        {!! !empty($multipart) ? 'enctype="multipart/form-data"' : '' !!}
    >
        @csrf
        @if(($method ?? 'POST') !== 'POST')
            @method($method)
        @endif

        @yield('form_content')

        @hasSection('form_footer')
            @yield('form_footer')
        @else
            <div class="d-flex justify-content-end gap-2 mt-3">
                <x-admin.ui.button :href="url()->previous()" variant="secondary" outline>Batal</x-admin.ui.button>
                <x-admin.ui.button type="submit">Simpan</x-admin.ui.button>
            </div>
        @endif
    </form>
</div>
@endsection
