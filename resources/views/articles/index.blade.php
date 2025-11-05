<x-guest-layout>
    <div class="max-w-4xl mx-auto py-10 px-4">
        <h1 class="text-2xl font-semibold mb-6">Artikel</h1>

        @forelse ($articles as $a)
            <article class="mb-6 p-5 bg-white shadow rounded">
                <h2 class="text-xl font-semibold">
                    <a href="{{ route('articles.show', $a) }}" class="hover:underline">{{ $a->judul }}</a>
                </h2>
                <div class="text-xs text-gray-500 mt-1">
                    Diterbitkan: {{ optional($a->diterbitkan_pada)->format('d M Y H:i') }}
                </div>
                <div class="mt-3 text-gray-700 line-clamp-3">
                    {!! \Illuminate\Support\Str::limit(strip_tags($a->isi), 200) !!}
                </div>
                <div class="mt-3">
                    <a href="{{ route('articles.show', $a) }}" class="text-indigo-600 text-sm hover:underline">Baca selengkapnya</a>
                </div>
            </article>
        @empty
            <p class="text-gray-500">Belum ada artikel.</p>
        @endforelse

        <div class="mt-6">{{ $articles->links() }}</div>
    </div>
</x-guest-layout>
