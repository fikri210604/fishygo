@extends('layouts.admin')
@section('content')
    <h1 class="text-xl font-semibold mb-4">Tambah Produk</h1>
    <form method="POST" action="{{ route('admin.produk.store') }}" enctype="multipart/form-data" class="bg-white p-5 rounded border space-y-4 max-w-3xl">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="label"><span class="label-text">Nama Produk</span></label>
                <input type="text" name="nama_produk" class="input input-bordered w-full" required />
            </div>
            <div>
                <label class="label"><span class="label-text">Kode Produk (opsional)</span></label>
                <input type="text" name="kode_produk" class="input input-bordered w-full" />
            </div>
            <div>
                <label class="label"><span class="label-text">Slug (opsional)</span></label>
                <input type="text" name="slug" class="input input-bordered w-full" />
            </div>
            <div class="md:col-span-2">
                <label class="label"><span class="label-text">Galeri Foto (opsional)</span></label>
                <input type="file" name="photos[]" accept="image/*" multiple class="file-input file-input-bordered w-full" />
                <div class="text-xs text-gray-500 mt-1">Anda dapat mengunggah beberapa foto. Foto pertama akan dijadikan utama jika tidak memilih gambar utama.</div>
            </div>
            <div>
                <label class="label"><span class="label-text">Kategori</span></label>
                <select name="kategori_produk_id" class="select select-bordered w-full" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach(\App\Models\KategoriProduk::orderBy('nama_kategori')->get() as $k)
                        <option value="{{ $k->kategori_produk_id }}">{{ $k->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label"><span class="label-text">Jenis Ikan</span></label>
                <select name="jenis_ikan_id" class="select select-bordered w-full" required>
                    <option value="">-- Pilih Jenis Ikan --</option>
                    @foreach(\App\Models\JenisIkan::orderBy('jenis_ikan')->get() as $j)
                        <option value="{{ $j->jenis_ikan_id }}">{{ $j->jenis_ikan }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="label"><span class="label-text">Deskripsi</span></label>
            <textarea name="deskripsi" rows="4" class="textarea textarea-bordered w-full" required></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="label"><span class="label-text">Harga</span></label>
                <input type="number" step="0.01" name="harga" class="input input-bordered w-full" required />
            </div>
            <div>
                <label class="label"><span class="label-text">Harga Promo (opsional)</span></label>
                <input type="number" step="0.01" name="harga_promo" class="input input-bordered w-full" />
            </div>
            <div>
                <label class="label"><span class="label-text">Satuan</span></label>
                <input type="text" name="satuan" class="input input-bordered w-full" placeholder="kg/pcs/gram" required />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="label"><span class="label-text">Stok</span></label>
                <input type="number" name="stok" class="input input-bordered w-full" required />
            </div>
            <div>
                <label class="label"><span class="label-text">Berat (gram)</span></label>
                <input type="number" name="berat_gram" class="input input-bordered w-full" />
            </div>
            <div>
                <label class="label"><span class="label-text">Kadaluarsa</span></label>
                <input type="date" name="kadaluarsa" class="input input-bordered w-full" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="label"><span class="label-text">Promo Mulai</span></label>
                <input type="datetime-local" name="promo_mulai" class="input input-bordered w-full" />
            </div>
            <div>
                <label class="label"><span class="label-text">Promo Selesai</span></label>
                <input type="datetime-local" name="promo_selesai" class="input input-bordered w-full" />
            </div>
        </div>

        <div class="flex gap-2">
            <button class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.produk.index') }}" class="btn">Batal</a>
        </div>
    </form>
@endsection
