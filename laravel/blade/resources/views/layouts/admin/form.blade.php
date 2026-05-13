@extends('layouts.admin.app')

@section('content')
<div class="col-lg-12">
    <x-admin.form.container
        :action="$action"
        :method="$method ?? 'POST'"
        :multipart="$multipart ?? false"
    >
        @yield('form_content')

        @hasSection('form_footer')
            <x-slot:footer>
                @yield('form_footer')
            </x-slot:footer>
        @endif
    </x-admin.form.container>
</div>
@endsection
