<x-admin-layout>
    <div class="max-w-5xl mx-auto">
        <h1 class="text-xl font-semibold mb-4 text-gray-800">Edit Artikel</h1>

        <form id="article-form" 
              action="{{ route('admin.articles.update', $article) }}" 
              method="POST" 
              class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            @csrf
            @method('PUT')

            @include('admin.articles._form', ['article' => $article])

            <div class="mt-6 flex flex-wrap gap-3 justify-between">
                <!-- Tombol Simpan -->
                <button type="button" 
                        class="btn bg-blue-500/80 hover:bg-blue-600 text-white border-none px-6 py-3 rounded-lg transition-all"
                        onclick="document.getElementById('confirm-update-article').showModal()">
                    Simpan
                </button>

                <!-- Tombol Batal -->
                <a href="{{ route('admin.articles.index') }}" 
                   class="btn btn-outline border-gray-300 text-gray-700 hover:bg-gray-100 px-6 py-3 rounded-lg transition-all">
                    Batal
                </a>

                <!-- Tombol Hapus -->
                <button type="button" 
                        class="btn bg-red-400/90 hover:bg-red-500 text-white px-6 py-3 rounded-lg transition-all ml-auto" 
                        onclick="document.getElementById('confirm-delete-article').showModal()">
                    Hapus
                </button>
            </div>
        </form>
    </div>

    <!-- Modal Konfirmasi Update -->
    <dialog id="confirm-update-article" class="modal">
        <div class="modal-box bg-white text-gray-800">
            <h3 class="font-semibold text-lg">Konfirmasi</h3>
            <p class="py-4 text-sm text-gray-600">Simpan perubahan artikel?</p>
            <div class="modal-action">
                <button class="btn bg-blue-500/80 hover:bg-blue-600 text-white border-none px-5 py-2.5 rounded-lg"
                        type="submit" form="article-form">Ya, Simpan</button>
                <form method="dialog">
                    <button class="btn btn-outline border-gray-300 text-gray-700 hover:bg-gray-100 px-5 py-2.5 rounded-lg">Batal</button>
                </form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    <!-- Modal Konfirmasi Delete -->
    <dialog id="confirm-delete-article" class="modal">
        <div class="modal-box bg-white text-gray-800">
            <h3 class="font-semibold text-lg">Hapus Artikel</h3>
            <p class="py-4 text-sm text-gray-600">Yakin hapus artikel ini?</p>
            <div class="modal-action">
                <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn bg-red-400/90 hover:bg-red-500 text-white px-5 py-2.5 rounded-lg">Ya, Hapus</button>
                </form>
                <form method="dialog">
                    <button class="btn btn-outline border-gray-300 text-gray-700 hover:bg-gray-100 px-5 py-2.5 rounded-lg">Batal</button>
                </form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>
</x-admin-layout>
