@extends('layouts.app')

@section('title', 'Halaman Tidak Ditemukan')

@section('content')

<style>
    /* Definisi Keyframes untuk animasi Goyangan (Shake) */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        20%, 60% { transform: translateX(-5px); }
        40%, 80% { transform: translateX(5px); }
    }

    /* Definisi Keyframes untuk animasi Pudar Masuk (Fade In) */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Kelas Kustom untuk Menerapkan Animasi */
    .animate-shake {
        /* Menggunakan durasi dan iterasi yang berbeda agar tidak terlalu mengganggu */
        animation: shake 1.5s ease-in-out infinite alternate;
    }
    .animate-fade-in {
        animation: fadeIn 0.6s ease-out forwards;
    }
    .animate-delay-200 {
        animation-delay: 0.2s;
    }
    .animate-delay-400 {
        animation-delay: 0.4s;
    }
</style>

<div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 text-center px-6">
    <h1 class="text-8xl md:text-9xl font-extrabold text-orange-600 animate-shake">
        404
    </h1>

    <p class="text-xl md:text-2xl text-gray-700 mt-4 animate-fade-in">
        Oops! Halaman yang kamu cari **tidak ditemukan**.
    </p>
    
    <p class="text-sm text-gray-500 mt-2 animate-fade-in animate-delay-200" style="opacity: 0;">
        (Mungkin tautan rusak, atau halaman sudah dipindahkan.)
    </p>

    <a href="{{ url('/') }}" class="btn bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md mt-6 animate-fade-in animate-delay-400" style="opacity: 0;">
        🏠 Kembali ke Beranda
    </a>
</div>
@endsection