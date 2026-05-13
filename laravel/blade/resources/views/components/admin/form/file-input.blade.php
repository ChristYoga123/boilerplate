@props([
    'accept' => 'image/*',
    'preview' => true,
    'multiple' => false, // Mendukung multiple upload
    'currentImages' => [], // URL gambar lama (bisa array URL atau single string URL)
])

@php
    $errorName = str_replace('[]', '', (string) $attributes->get('name'));
    $field = \App\Support\AdminFormField::make($attributes, [
        'errorNames' => $errorName === '' ? [] : [$errorName . '.*'],
    ]);
    $inputId = $field->id;
    $hasError = $field->hasError($errors ?? null);
    $previewContainerId = 'preview-container-' . $inputId;
    
    // Normalisasi currentImages menjadi array
    $images = is_array($currentImages) ? $currentImages : (!empty($currentImages) ? [$currentImages] : []);
@endphp

<x-admin.form.field :field="$field">
    @if($preview)
        <div id="{{ $previewContainerId }}" class="mb-2 d-flex flex-wrap gap-2" style="{{ empty($images) ? 'display: none;' : '' }}">
            @foreach($images as $img)
                <img src="{{ $img }}" alt="Image Preview" class="img-thumbnail" style="max-height: 150px; object-fit: contain;" />
            @endforeach
        </div>
    @endif

    <input
        id="{{ $inputId }}"
        name="{{ $field->name }}"
        type="file"
        @if($multiple) multiple @endif
        @if($accept) accept="{{ $accept }}" @endif
        @if($field->required) required @endif
        @if($field->disabled) disabled @endif
        {{ $field->controlAttributes()->class([
            'form-control',
            'is-invalid border-danger' => $hasError,
        ]) }}
        @if($preview)
            onchange="previewMultipleImagesComponent(this, '{{ $previewContainerId }}')"
        @endif
    >
</x-admin.form.field>

@if($preview)
    @once
        @push('scripts')
            <script>
                function previewMultipleImagesComponent(input, containerId) {
                    const $container = $('#' + containerId);
                    
                    // Simpan HTML gambar lama jika belum tersimpan
                    if (typeof $container.data('originalHtml') === 'undefined') {
                        $container.data('originalHtml', $container.html());
                    }
                    
                    if (input.files && input.files.length > 0) {
                        $container.css('display', 'flex').empty(); // Kosongkan preview lama
                        
                        $.each(input.files, function(i, file) {
                            if (!file.type.startsWith('image/')) return;
                            
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const $wrapper = $('<div>', {
                                    class: 'position-relative d-inline-block mt-2 me-2'
                                });
                                
                                const $img = $('<img>', {
                                    src: e.target.result,
                                    class: 'img-thumbnail',
                                    css: {
                                        maxHeight: '150px',
                                        objectFit: 'contain'
                                    }
                                });
                                
                                const $removeBtn = $('<button>', {
                                    type: 'button',
                                    html: '&times;',
                                    class: 'btn btn-sm btn-danger position-absolute top-0 start-100 translate-middle rounded-circle shadow',
                                    title: 'Hapus',
                                    css: {
                                        width: '22px',
                                        height: '22px',
                                        padding: '0',
                                        lineHeight: '1',
                                        fontSize: '14px',
                                        fontWeight: 'bold'
                                    }
                                }).on('click', function() {
                                    const dt = new DataTransfer();
                                    $.each(input.files, function(index, currentFile) {
                                        if (currentFile !== file) {
                                            dt.items.add(currentFile);
                                        }
                                    });
                                    input.files = dt.files;
                                    
                                    $wrapper.remove();
                                    
                                    if (input.files.length === 0) {
                                        $container.html($container.data('originalHtml'));
                                        $container.css('display', $container.children().length > 0 ? 'flex' : 'none');
                                    }
                                });
                                
                                $wrapper.append($img, $removeBtn);
                                $container.append($wrapper);
                            }
                            reader.readAsDataURL(file);
                        });
                    } else {
                        // Kembalikan ke gambar lawas (kalau batal input)
                        $container.html($container.data('originalHtml'));
                        $container.css('display', $container.children().length > 0 ? 'flex' : 'none');
                    }
                }
            </script>
        @endpush
    @endonce
@endif
