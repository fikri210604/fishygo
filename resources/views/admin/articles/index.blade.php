<x-admin-layout>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">📰 Manajemen Artikel</h1>
        <a href="{{ route('admin.articles.create') }}" 
           class="btn btn-primary shadow-md hover:shadow-lg transition-all duration-200">
            <i class="fa-solid fa-plus mr-2"></i> Buat Artikel
        </a>
    </div>

    <div class="overflow-x-auto bg-white shadow rounded-lg border border-base-200">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gradient-to-r from-cyan-500 to-blue-600 text-white">
                <tr>
                    <th class="px-4 py-3 font-semibold tracking-wide">Judul</th>
                    <th class="px-4 py-3 font-semibold tracking-wide">Slug</th>
                    <th class="px-4 py-3 font-semibold tracking-wide">Tanggal Terbit</th>
                    <th class="px-4 py-3 font-semibold tracking-wide">Terakhir Diubah</th>
                    <th class="px-4 py-3 text-right font-semibold tracking-wide">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse ($articles as $a)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $a->judul }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $a->slug }}</td>
                        <td class="px-4 py-3 text-gray-700">
                            {{ $a->diterbitkan_pada ? $a->diterbitkan_pada->format('Y-m-d H:i') : '-' }}
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            {{ $a->updated_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('admin.articles.edit', $a) }}" 
                               class="btn btn-sm btn-outline btn-info bg-blue-600 hover:bg-blue-700">Edit</a>

                            <button class="btn btn-sm btn-outline btn-error bg-red-600 hover:bg-red-700 px-4 py-3"
                                    onclick="document.getElementById('confirm-delete-article-{{ $a->id }}').showModal()">
                                Hapus
                            </button>

                            <!-- Modal konfirmasi hapus -->
                            <dialog id="confirm-delete-article-{{ $a->id }}" class="modal">
                                <div class="modal-box">
                                    <h3 class="font-bold text-lg text-error">Hapus Artikel</h3>
                                    <p class="py-3 text-gray-700">Yakin ingin menghapus artikel ini?</p>
                                    <div class="modal-action">
                                        <form action="{{ route('admin.articles.destroy', $a) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-error">Ya, Hapus</button>
                                        </form>
                                        <form method="dialog">
                                            <button class="btn btn-ghost">Batal</button>
                                        </form>
                                    </div>
                                </div>
                                <form method="dialog" class="modal-backdrop"><button>close</button></form>
                            </dialog>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-6 text-center text-gray-500" colspan="5">Belum ada artikel.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $articles->links() }}
    </div>
</x-admin-layout>
