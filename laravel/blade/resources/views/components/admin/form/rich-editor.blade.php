{{--
    Rich Editor — Quill 1.3.7 (Snow theme)

    Props:
      name        string   — form field name
      label       string   — visible label (optional)
      value       string   — initial HTML content (optional)
      required    bool     — marks field as required
      placeholder string   — editor placeholder
      height      int      — editor surface height in px (default 250)

    XSS — Quill converts all pasted/typed HTML to its internal Delta format,
    stripping scripts and event handlers before rendering. The hidden textarea
    therefore only ever contains Quill-rendered markup (p, br, b, i, ul, ol,
    li, h1-h3, a, blockquote, pre).

    Server-side: still recommended to sanitize the submitted value in your
    controller with strip_tags() or mews/purifier before persisting:
        $validated['body'] = strip_tags($request->body,
            '<p><br><b><i><u><strong><em><ul><ol><li><h1><h2><h3><a><blockquote><pre><code>');
--}}

@props([
    'placeholder' => 'Tulis konten di sini...',
    'height'      => 250,
])

@php
    $field      = \App\Support\AdminFormField::make($attributes);
    $inputId    = $field->id;
    $editorId   = 'quill-editor-' . $inputId;
    $hasError   = $field->hasError($errors ?? null);
    $oldValue   = $field->oldValue('');
@endphp

@once
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" />
    @endpush
@endonce

<x-admin.form.field :field="$field">
    {{-- Hidden textarea — submitted with the form --}}
    <textarea
        id="{{ $inputId }}"
        name="{{ $field->name }}"
        @if($field->required) required @endif
        @if($field->disabled) disabled @endif
        @if($field->readonly) readonly @endif
        class="d-none"
        aria-hidden="true"
    >{{ $oldValue }}</textarea>

    {{-- Quill surface --}}
    <div
        id="{{ $editorId }}"
        style="height: {{ $height }}px;"
        class="bg-white {{ $hasError ? 'border border-danger rounded' : '' }}"
    ></div>
</x-admin.form.field>

@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    @endpush
@endonce

@push('scripts')
<script>
(function () {
    var quill = new Quill('#{{ $editorId }}', {
        theme: 'snow',
        placeholder: '{{ addslashes($placeholder) }}',
        modules: {
            toolbar: [
                [{ header: [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                [{ indent: '-1' }, { indent: '+1' }],
                ['blockquote', 'code-block'],
                ['link'],
                ['clean'],
            ]
        }
    });

    // Load initial / old value
    var $hidden = document.getElementById('{{ $inputId }}');
    var initial = $hidden.value.trim();
    if (initial) {
        quill.clipboard.dangerouslyPasteHTML(initial);
    }

    // Keep hidden textarea in sync
    quill.on('text-change', function () {
        $hidden.value = quill.root.innerHTML === '<p><br></p>' ? '' : quill.root.innerHTML;
    });
})();
</script>
@endpush
