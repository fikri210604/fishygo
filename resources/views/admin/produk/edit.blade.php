@extends('layouts.admin')
@section('content')
    <h1 class="text-xl font-semibold mb-4">Edit Produk</h1>
    <form method="POST" action="{{ route('admin.produk.update', $produk->produk_id) }}" enctype="multipart/form-data" class="bg-white p-5 rounded border space-y-4 max-w-5xl">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="label"><span class="label-text">Nama Produk</span></label>
                <input type="text" name="nama_produk" class="input input-bordered w-full" value="{{ old('nama_produk', $produk->nama_produk) }}" required />
            </div>
            <div>
                <label class="label"><span class="label-text">Kode Produk</span></label>
                <input type="text" name="kode_produk" class="input input-bordered w-full" value="{{ old('kode_produk', $produk->kode_produk) }}" required />
            </div>
            <div>
                <label class="label"><span class="label-text">Slug (opsional)</span></label>
                <input type="text" name="slug" class="input input-bordered w-full" value="{{ old('slug', $produk->slug) }}" />
            </div>
            
            <div class="md:col-span-2">
                <label class="label"><span class="label-text">Galeri Foto</span></label>
                <input type="file" name="photos[]" accept="image/*" multiple class="file-input file-input-bordered w-full" />
                <div class="text-xs text-gray-500 mt-1">Unggah foto tambahan (opsional). Atur foto utama dan urutan di bawah.</div>
                @php $photos = $produk->photos()->orderBy('urutan')->get(); @endphp
                @if ($photos->count())
                    <div class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach ($photos as $pf)
                            <div class="border rounded p-2 space-y-2">
                                <img src="{{ asset('storage/'.$pf->path) }}" class="h-28 w-full object-cover rounded" alt="Foto" loading="lazy" decoding="async">
                                <div class="flex items-center justify-between text-sm">
                                    <label class="flex items-center gap-1">
                                        <input type="radio" name="primary_photo_id" value="{{ $pf->produk_foto_id }}" class="radio radio-sm" {{ $pf->is_primary ? 'checked' : '' }}>
                                        <span>Jadikan utama</span>
                                    </label>
                                    <label class="flex items-center gap-1 text-red-600">
                                        <input type="checkbox" name="delete_photo_ids[]" value="{{ $pf->produk_foto_id }}" class="checkbox checkbox-xs">
                                        <span>Hapus</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="label"><span class="label-text-alt">Urutan</span></label>
                                    <input type="number" name="order[{{ $pf->produk_foto_id }}]" value="{{ $pf->urutan }}" class="input input-bordered input-sm w-full">
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="mt-2 text-sm text-gray-500">Belum ada foto galeri.</div>
                @endif
            </div>
            <div>
                <label class="label"><span class="label-text">Kategori</span></label>
                <select name="kategori_produk_id" class="select select-bordered w-full" required>
                    @foreach($kategori as $k)
                        <option value="{{ $k->kategori_produk_id }}" @selected(old('kategori_produk_id', $produk->kategori_produk_id) == $k->kategori_produk_id)>
                            {{ $k->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="label"><span class="label-text">Jenis Ikan</span></label>
                <select name="jenis_ikan_id" class="select select-bordered w-full" required>
                    @foreach($jenisIkan as $j)
                        <option value="{{ $j->jenis_ikan_id }}"
                            @selected(old('jenis_ikan_id', $produk->jenis_ikan_id) == $j->jenis_ikan_id)>
                            {{ $j->jenis_ikan }}
                        </option>
                    @endforeach
                </select>
            </div>
            
        </div>

        <div>
            <label class="label"><span class="label-text">Deskripsi</span></label>
            <textarea name="deskripsi" rows="4" class="textarea textarea-bordered w-full" required>{{ old('deskripsi', $produk->deskripsi) }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="label"><span class="label-text">Harga</span></label>
                <input type="number" step="0.01" name="harga" class="input input-bordered w-full" value="{{ old('harga', $produk->harga) }}" required />
            </div>
            <div>
                <label class="label"><span class="label-text">Harga Promo (opsional)</span></label>
                <input type="number" step="0.01" name="harga_promo" class="input input-bordered w-full" value="{{ old('harga_promo', $produk->harga_promo) }}" />
            </div>
            <div>
                <label class="label"><span class="label-text">Satuan</span></label>
                <input type="text" name="satuan" class="input input-bordered w-full" value="{{ old('satuan', $produk->satuan) }}" required />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="label"><span class="label-text">Stok</span></label>
                <input type="number" name="stok" class="input input-bordered w-full" value="{{ old('stok', $produk->stok) }}" required />
            </div>
            <div>
                <label class="label"><span class="label-text">Berat (gram)</span></label>
                <input type="number" name="berat_gram" class="input input-bordered w-full" value="{{ old('berat_gram', $produk->berat_gram) }}" />
            </div>
            <div>
                <label class="label"><span class="label-text">Kadaluarsa</span></label>
                <input type="date" name="kadaluarsa" class="input input-bordered w-full" value="{{ old('kadaluarsa', optional($produk->kadaluarsa)->format('Y-m-d')) }}" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="label"><span class="label-text">Promo Mulai</span></label>
                <input type="datetime-local" name="promo_mulai" class="input input-bordered w-full" value="{{ old('promo_mulai', optional($produk->promo_mulai)->format('Y-m-d\TH:i')) }}" />
            </div>
            <div>
                <label class="label"><span class="label-text">Promo Selesai</span></label>
                <input type="datetime-local" name="promo_selesai" class="input input-bordered w-full" value="{{ old('promo_selesai', optional($produk->promo_selesai)->format('Y-m-d\TH:i')) }}" />
            </div>
        </div>

        <div class="flex gap-2">
            <button class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.produk.index') }}" class="btn">Batal</a>
        </div>
    </form>
@endsection
