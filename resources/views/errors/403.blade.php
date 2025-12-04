@extends('layouts.app')

@section('title', 'Akses Ditolak')

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
        animation: shake 0.8s ease-in-out infinite alternate;
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
    {{-- Animasi Goyangan untuk Angka 403 --}}
    <h1 class="text-8xl md:text-9xl font-extrabold text-red-600 animate-shake">
        403
    </h1>

    {{-- Animasi Pudar Masuk untuk Teks Utama --}}
    <p class="text-xl md:text-2xl text-gray-700 mt-4 animate-fade-in">
        Maaf! Kamu **tidak memiliki akses** ke halaman ini.
    </p>
    
    {{-- Animasi Pudar Masuk untuk Teks Tambahan dengan delay --}}
    <p class="text-sm text-gray-500 mt-2 animate-fade-in animate-delay-200" style="opacity: 0;">
        (Silakan pastikan kamu sudah masuk dengan akun yang benar.)
    </p>

    {{-- Animasi Pudar Masuk untuk Tombol dengan delay --}}
    @auth
        <a href="{{ url()->previous() }}" class="btn bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md mt-6 animate-fade-in animate-delay-400" style="opacity: 0;">
            ⬅️ Kembali
        </a>
    @else
        <a href="{{ route('login') }}" class="btn bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md mt-6 animate-fade-in animate-delay-400" style="opacity: 0;">
            🔑 Login
        </a>
    @endauth
</div>
@endsection