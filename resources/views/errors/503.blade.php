@extends('layouts.app')

@section('title', 'Sedang Maintenance')

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen bg-gray-100 text-center px-6">
    <h1 class="text-8xl font-bold text-blue-500">503</h1>
    <p class="text-xl text-gray-700 mt-4">Situs sedang dalam perbaikan. Mohon kembali lagi nanti.</p>
    
    @if(isset($message))
        <p class="text-gray-500 mt-2">{{ $message }}</p>
    @endif

    <a href="{{ url('/') }}" class="btn btn-primary mt-6">Kembali ke Beranda</a>
</div>
@endsection
