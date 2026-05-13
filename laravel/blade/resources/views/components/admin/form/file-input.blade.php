@props([
    'mode' => 'image', // image|file
    'accept' => null,
    'preview' => true,
    'multiple' => false,
    'currentImages' => [], // Backward compatible: URL gambar lama.
    'currentFiles' => [], // string URL/path or ['url' => ..., 'name' => ..., 'value' => ...]
    'remove' => false,
    'removeName' => null,
    'removeLabel' => 'Hapus file lama',
])

@php
    $mode = $mode === 'file' ? 'file' : 'image';
    $resolvedAccept = $accept ?? ($mode === 'image' ? 'image/*' : null);
    $errorName = str_replace('[]', '', (string) $attributes->get('name'));
    $field = \App\Support\AdminFormField::make($attributes, [
        'errorNames' => $errorName === '' ? [] : [$errorName . '.*'],
    ]);
    $inputId = $field->id;
    $hasError = $field->hasError($errors ?? null);
    $sizeClass = $field->size ? 'form-control-' . $field->size : null;
    $previewContainerId = 'preview-container-' . $inputId;
    $sourceFiles = !empty($currentFiles) ? $currentFiles : $currentImages;
    $removeInputName = $removeName ?? ($multiple ? $errorName . '_remove[]' : $errorName . '_remove');

    $normalizeFiles = function ($files) {
        if (empty($files)) {
            return [];
        }

        if (is_string($files)) {
            $files = [$files];
        }

        if (is_array($files) && (array_key_exists('url', $files) || array_key_exists('path', $files))) {
            $files = [$files];
        }

        return collect($files)
            ->map(function ($file) {
                if (is_string($file)) {
                    $url = $file;
                    $name = basename(parse_url($url, PHP_URL_PATH) ?: $url);

                    return [
                        'url' => $url,
                        'name' => $name,
                        'value' => $url,
                    ];
                }

                if (! is_array($file)) {
                    return null;
                }

                $url = $file['url'] ?? $file['path'] ?? null;
                if (! $url) {
                    return null;
                }

                return [
                    'url' => $url,
                    'name' => $file['name'] ?? basename(parse_url($url, PHP_URL_PATH) ?: $url),
                    'value' => $file['value'] ?? $file['id'] ?? $url,
                ];
            })
            ->filter()
            ->values()
            ->all();
    };

    $files = $normalizeFiles($sourceFiles);
@endphp

<x-admin.form.field :field="$field">
    @if($preview)
        <div
            id="{{ $previewContainerId }}"
            class="admin-file-preview mb-2 {{ empty($files) ? 'd-none' : '' }}"
            data-original-display="{{ empty($files) ? 'none' : 'flex' }}"
        >
            @foreach($files as $file)
                <div class="admin-file-preview__item">
                    @if($mode === 'image')
                        <a href="{{ $file['url'] }}" target="_blank" rel="noopener" class="admin-file-preview__image-link">
                            <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}" class="admin-file-preview__image" />
                        </a>
                    @else
                        <a href="{{ $file['url'] }}" target="_blank" rel="noopener" class="admin-file-preview__file">
                            <span class="admin-file-preview__file-icon">
                                <i class="feather-file-text"></i>
                            </span>
                            <span class="admin-file-preview__file-name">{{ $file['name'] }}</span>
                        </a>
                    @endif

                    @if($remove && $removeInputName)
                        <label class="form-check admin-file-preview__remove">
                            <input
                                type="checkbox"
                                name="{{ $removeInputName }}"
                                value="{{ $multiple ? $file['value'] : '1' }}"
                                class="form-check-input"
                            >
                            <span class="form-check-label">{{ $removeLabel }}</span>
                        </label>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <input
        id="{{ $inputId }}"
        name="{{ $field->name }}"
        type="file"
        @if($multiple) multiple @endif
        @if($resolvedAccept) accept="{{ $resolvedAccept }}" @endif
        @if($field->required) required @endif
        @if($field->disabled) disabled @endif
        {{ $field->controlAttributes()->class([
            'form-control',
            $sizeClass,
            'is-invalid border-danger' => $hasError,
        ]) }}
        @if($preview)
            onchange="previewAdminFilesComponent(this, '{{ $previewContainerId }}', '{{ $mode }}')"
        @endif
    >
</x-admin.form.field>

@once
    @push('styles')
        <style>
            .admin-file-preview {
                display: flex;
                flex-wrap: wrap;
                gap: .75rem;
            }

            .admin-file-preview__item {
                align-items: flex-start;
                background: #f8fafc;
                border: 1px solid #dfe5ec;
                border-radius: .5rem;
                display: flex;
                flex-direction: column;
                gap: .5rem;
                max-width: 220px;
                padding: .625rem;
                position: relative;
            }

            .admin-file-preview__image {
                border-radius: .375rem;
                display: block;
                height: 130px;
                object-fit: cover;
                width: 180px;
            }

            .admin-file-preview__file {
                align-items: center;
                color: inherit;
                display: flex;
                gap: .625rem;
                min-width: 180px;
                text-decoration: none;
            }

            .admin-file-preview__file-icon {
                align-items: center;
                background: #eef1f5;
                border-radius: .375rem;
                display: inline-flex;
                height: 2.25rem;
                justify-content: center;
                width: 2.25rem;
            }

            .admin-file-preview__file-name {
                font-size: .875rem;
                font-weight: 600;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .admin-file-preview__remove {
                align-items: center;
                display: flex;
                gap: .375rem;
                margin: 0;
            }

            .admin-file-preview__remove .form-check-input {
                margin: 0;
            }

            .admin-file-preview__remove-button {
                align-items: center;
                display: inline-flex;
                height: 1.5rem;
                justify-content: center;
                padding: 0;
                position: absolute;
                right: -.45rem;
                top: -.45rem;
                width: 1.5rem;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function previewAdminFilesComponent(input, containerId, mode) {
                const $container = $('#' + containerId);
                if (!$container.length) return;

                if (typeof $container.data('originalHtml') === 'undefined') {
                    $container.data('originalHtml', $container.html());
                    $container.data('originalDisplay', $container.css('display') === 'none' ? 'none' : 'flex');
                }

                function restoreOriginal() {
                    $container.html($container.data('originalHtml'));
                    $container.toggleClass('d-none', $container.data('originalDisplay') === 'none');
                }

                function syncVisibility() {
                    $container.toggleClass('d-none', $container.children().length === 0);
                }

                function removeFileFromInput(file, $wrapper) {
                    const dt = new DataTransfer();

                    $.each(input.files, function(index, currentFile) {
                        if (currentFile !== file) {
                            dt.items.add(currentFile);
                        }
                    });

                    input.files = dt.files;
                    $wrapper.remove();

                    if (input.files.length === 0) {
                        restoreOriginal();
                    } else {
                        syncVisibility();
                    }
                }

                function appendRemoveButton(file, $wrapper) {
                    $('<button>', {
                        type: 'button',
                        html: '&times;',
                        class: 'btn btn-sm btn-danger rounded-circle shadow admin-file-preview__remove-button',
                        title: 'Hapus',
                        'aria-label': 'Hapus file'
                    }).on('click', function() {
                        removeFileFromInput(file, $wrapper);
                    }).appendTo($wrapper);
                }

                function appendImage(file) {
                    if (!file.type.startsWith('image/')) return;

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const $wrapper = $('<div>', { class: 'admin-file-preview__item' });
                        $('<img>', {
                            src: e.target.result,
                            alt: file.name,
                            class: 'admin-file-preview__image'
                        }).appendTo($wrapper);

                        appendRemoveButton(file, $wrapper);
                        $container.append($wrapper);
                        syncVisibility();
                    };
                    reader.readAsDataURL(file);
                }

                function appendFile(file) {
                    const $wrapper = $('<div>', { class: 'admin-file-preview__item' });
                    const $file = $('<div>', { class: 'admin-file-preview__file' });
                    $('<span>', { class: 'admin-file-preview__file-icon' }).html('<i class="feather-file-text"></i>').appendTo($file);
                    $('<span>', { class: 'admin-file-preview__file-name', text: file.name }).appendTo($file);
                    $file.appendTo($wrapper);

                    appendRemoveButton(file, $wrapper);
                    $container.append($wrapper);
                    syncVisibility();
                }

                if (input.files && input.files.length > 0) {
                    $container.removeClass('d-none').empty();

                    $.each(input.files, function(i, file) {
                        mode === 'image' ? appendImage(file) : appendFile(file);
                    });
                } else {
                    restoreOriginal();
                }
            }
        </script>
    @endpush
@endonce
