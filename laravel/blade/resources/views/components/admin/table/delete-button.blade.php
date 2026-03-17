@php
    $action = $action ?? null;
    $name = $name ?? null;
    $title = $title ?? 'Hapus';
    $permission = $permission ?? null;
    if (!isset($attributes) || !($attributes instanceof \Illuminate\View\ComponentAttributeBag)) {
        $attributes = new \Illuminate\View\ComponentAttributeBag([]);
    }
@endphp

@if($action && (!$permission || auth()->user()?->can($permission)))
    <a href="javascript:void(0)"
        {{ $attributes->merge([
            'class' => 'avatar-text avatar-md text-danger js-delete',
            'data-action' => $action,
            'data-name' => $name,
            'title' => $title,
        ]) }}
    >
        <i class="feather feather-trash-2"></i>
    </a>
@endif

<script>
    (function () {
        if (window.__adminDeleteButtonInit) return;
        window.__adminDeleteButtonInit = true;

        $(document).on('click', '.js-delete', function () {
            var $el = $(this);
            var action = $el.data('action');
            var name = $el.data('name') || 'data ini';

            if (!action) return;

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus ' + name + '? Tindakan ini tidak dapat dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545',
                reverseButtons: true,
            }).then(function (result) {
                if (!result.isConfirmed) return;
                $('<form method="POST">')
                    .attr('action', action)
                    .append($('<input type="hidden" name="_token">').val('{{ csrf_token() }}'))
                    .append('<input type="hidden" name="_method" value="DELETE">')
                    .appendTo('body')
                    .submit();
            });
        });
    })();
</script>
