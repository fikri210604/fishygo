<x-guest-layout>
    <div class="max-w-4xl mx-auto py-10 px-4">
        <article class="prose max-w-none">
            <h1>{{ $article->judul }}</h1>
            <div class="text-sm text-gray-500 mb-6">
                Diterbitkan: {{ optional($article->diterbitkan_pada)->format('d M Y H:i') }}
            </div>
            <div class="content">
                {!! $article->isi !!}
            </div>
        </article>
    </div>
</x-guest-layout>
