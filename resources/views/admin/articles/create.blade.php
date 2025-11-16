@extends('layouts.admin')

@section('content')
    <div class="max-w-5xl mx-auto">
        <h1 class="text-xl font-semibold mb-4">Buat Artikel</h1>
        <form id="article-form" action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow">
            @csrf
            @include('admin.articles._form')

            <div class="mt-4 flex gap-3">
                <button type="button" class="btn btn-primary" onclick="document.getElementById('confirm-create-article').showModal()">Simpan</button>
                <a href="{{ route('admin.articles.index') }}" class="btn">Batal</a>
            </div>
        </form>
    </div>

    <dialog id="confirm-create-article" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Konfirmasi</h3>
            <p class="py-4">Simpan artikel baru?</p>
            <div class="modal-action">
                <button class="btn" type="submit" form="article-form">Ya, Simpan</button>
                <form method="dialog"><button class="btn btn-ghost">Batal</button></form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>
    </div>
@endsection
