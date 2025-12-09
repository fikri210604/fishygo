@extends('layouts.app')

@section('title', 'Sedang Maintenance')

@section('content')

<style>
    /* Definisi Keyframes untuk animasi Goyangan (Shake) */
    @keyframes shake {
        /* Goyangan yang lebih lembut karena ini bukan error, tapi status sementara */
        0%, 100% { transform: translateX(0); }
        20%, 60% { transform: translateX(-3px); }
        40%, 80% { transform: translateX(3px); }
    }

    /* Definisi Keyframes untuk animasi Pudar Masuk (Fade In) */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Kelas Kustom untuk Menerapkan Animasi */
    .animate-shake {
        animation: shake 2s ease-in-out infinite alternate;
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
    <h1 class="text-8xl md:text-9xl font-extrabold text-teal-600 animate-shake">
        503
    </h1>

    <p class="text-xl md:text-2xl text-gray-700 mt-4 animate-fade-in">
        🚧 **Situs sedang dalam perbaikan terjadwal.** 🚧
    </p>
    
    <p class="text-sm text-gray-500 mt-2 animate-fade-in animate-delay-200" style="opacity: 0;">
        Mohon maaf atas ketidaknyamanannya. Kami akan segera kembali online!
    </p>

    {{-- Pesan opsional dari server --}}
    @if(isset($message))
        <p class="text-gray-600 mt-4 px-4 py-2 bg-gray-200 rounded-lg animate-fade-in animate-delay-200" style="opacity: 0;">
            **Pesan Pemeliharaan:** {{ $message }}
        </p>
    @endif

    {{-- Karena sedang maintenance, tombol kembali ke beranda mungkin tidak berguna, 
    tetapi kita tetap memberikannya jika ada halaman statis yang dapat diakses. --}}
    <a href="{{ url('/') }}" class="btn bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md mt-6 animate-fade-in animate-delay-400" style="opacity: 0;">
        🔄 Coba Muat Ulang Halaman
    </a>
</div>
@endsection