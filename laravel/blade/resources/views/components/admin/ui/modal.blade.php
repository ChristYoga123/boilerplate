@props([
    'id',
    'title' => '',
    'action' => null,
    'method' => 'POST',
    'size' => null, // sm|lg|xl
    'static' => false,
])

<div
    class="modal fade"
    id="{{ $id }}"
    tabindex="-1"
    aria-labelledby="{{ $id }}Label"
    aria-hidden="true"
    @if($static) data-bs-backdrop="static" data-bs-keyboard="false" @endif
>
    <div class="modal-dialog {{ $size ? 'modal-'.$size : '' }} modal-dialog-centered">
        <div class="modal-content">
            @if($action)
            <form action="{{ $action }}" method="POST" @if(strtoupper($method) === 'MULTIPART') enctype="multipart/form-data" @endif>
                @csrf
                @if(!in_array(strtoupper($method), ['GET', 'POST', 'MULTIPART']))
                    @method($method)
                @endif
            @endif

            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                {{ $slot }}
            </div>

            @if(isset($footer))
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @elseif($action)
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <x-admin.ui.button type="submit">Simpan</x-admin.ui.button>
                </div>
            @endif

            @if($action)
            </form>
            @endif
        </div>
    </div>
</div>
