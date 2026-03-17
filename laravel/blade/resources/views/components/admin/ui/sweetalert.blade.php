@php
    $sessionTypes = [
        'success' => 'success',
        'info' => 'info',
        'warning' => 'warning',
        'danger' => 'error',
        'error' => 'error',
    ];

    $messages = [];
    foreach ($sessionTypes as $key => $icon) {
        if (session()->has($key)) {
            $messages[] = [
                'type' => $key,
                'icon' => $icon,
                'text' => session($key),
            ];
        }
    }
@endphp

@if (!empty($messages))
    <script>
        (function () {
            if (typeof Swal === 'undefined') {
                return;
            }

            var messages = @json($messages);

            messages.forEach(function (msg) {
                var opts = {
                    title: msg.type.charAt(0).toUpperCase() + msg.type.slice(1),
                    text: msg.text,
                    timer: 3000,
                    timerProgressBar: true,
                    timerProgressBarColor: '#3085d6',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                };
                if (msg.icon === 'success') {
                    opts.icon = 'success';
                    opts.iconHtml = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width:4rem;height:4rem;color:#22c55e"><polyline points="20 6 9 17 4 12"></polyline></svg>';
                } else {
                    opts.icon = msg.icon;
                }
                Swal.fire(opts);
            });
        })();
    </script>
@endif

