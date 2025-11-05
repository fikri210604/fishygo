@php
    $isEdit = isset($article);
@endphp

<div class="space-y-4">
    <div>
        <x-input-label for="judul" :value="__('Judul')" />
        <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full text-gray-900" :value="old('judul', $article->judul ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('judul')" />
    </div>

    <div>
        <x-input-label for="slug" :value="__('Slug (opsional)')" />
        <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full text-gray-900" :value="old('slug', $article->slug ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('slug')" />
    </div>

    <div>
        <x-input-label for="isi" :value="__('Konten')" />
        <input type="hidden" id="isi" name="isi" value="{{ old('isi', $article->isi ?? '') }}">


        <div id="editor" class="richtexteditor" style="min-height: 300px;"></div>
        <x-input-error class="mt-2" :messages="$errors->get('content')" />
    </div>

    <div>
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="published" value="1" @checked(old('published', ($article->diterbitkan_pada ?? null) ? 1 : 0))>
            <span>Terbitkan</span>
        </label>
    </div>

    
    <link rel="stylesheet" href="{{ asset('richtexteditorforphp/richtexteditor/rte_theme_default.css') }}" />
    <script src="{{ asset('richtexteditorforphp/richtexteditor/rte.js') }}"></script>
    <script src="{{ asset('richtexteditorforphp/richtexteditor/plugins/all_plugins.js') }}"></script>
    <script src="{{ asset('richtexteditorforphp/rte-upload.js') }}"></script>
    <script>
        // Point upload handler to Laravel route
        window.uploadhandlerpath = "{{ route('admin.uploads.rte') }}";
    </script>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var editor = new RichTextEditor(document.getElementById('editor'));
        // Set initial content
        var initial = document.getElementById('isi').value || '';
        if (initial) editor.setHTMLCode(initial);

        // Keep hidden input in sync
        editor.attachEvent('change', function () {
            document.getElementById('isi').value = editor.getHTMLCode();
        });

        // Ensure latest value on submit
        var form = document.getElementById('article-form');
        if (form) {
            form.addEventListener('submit', function () {
                document.getElementById('isi').value = editor.getHTMLCode();
            });
        }
    });
</script>
