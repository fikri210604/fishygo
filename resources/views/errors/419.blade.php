@extends('layouts.app') {{-- Menggunakan layouts.app agar konsisten --}}

@section('title', 'Sesi Berakhir')

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
        animation: shake 1s ease-in-out infinite alternate; /* Menggunakan durasi yang berbeda */
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
    <h1 class="text-8xl md:text-9xl font-extrabold text-purple-600 animate-shake">
        419
    </h1>

    <p class="text-xl md:text-2xl text-gray-700 mt-4 animate-fade-in">
        Maaf! **Sesi Anda telah berakhir.**
    </p>
    
    <p class="text-sm text-gray-500 mt-2 animate-fade-in animate-delay-200" style="opacity: 0;">
        (Silakan masuk kembali untuk melanjutkan pekerjaan Anda.)
    </p>

    <div class="flex items-center justify-center gap-4 mt-6 animate-fade-in animate-delay-400" style="opacity: 0;">
        {{-- Tombol utama: Login Ulang --}}
        <a href="{{ route('login') }}" class="btn bg-purple-500 hover:bg-purple-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md">
            🔑 Login Ulang
        </a>
        
        {{-- Tombol Sekunder: Kembali --}}
        <a href="{{ url()->previous() }}" class="btn bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg shadow-md">
            ⬅️ Kembali
        </a>
    </div>
    
    {{-- Jika Anda memiliki komponen toast/flash message --}}
    {{-- <x-flash-toast /> --}} 
</div>
@endsection