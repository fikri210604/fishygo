@extends('layouts.app')

@section('title', 'Kesalahan Server')

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 text-center px-6">
    <h1 class="text-8xl font-bold text-red-500">500</h1>
    <p class="text-xl text-gray-700 mt-4">Oops! Terjadi kesalahan pada server.</p>
    <a href="{{ url('/') }}" class="btn btn-primary mt-6">Kembali ke Beranda</a>
</div>
@endsection
