@extends('layouts.app')

@section('title', 'Akses Ditolak')

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 text-center px-6">
    <h1 class="text-8xl font-bold text-yellow-500">403</h1>
    <p class="text-xl text-gray-700 mt-4">Maaf! Kamu tidak memiliki akses ke halaman ini.</p>

    @auth
        <a href="{{ url()->previous() }}" class="btn btn-primary mt-6">Kembali</a>
    @else
        <a href="{{ route('login') }}" class="btn btn-primary mt-6">Login</a>
    @endauth
</div>
@endsection
