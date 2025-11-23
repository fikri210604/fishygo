@extends('layouts.guest')

@section('title', 'Sesi Berakhir')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full bg-white shadow rounded-xl p-6 text-center">
        <div class="text-6xl mb-2">419</div>
        <h1 class="text-2xl font-semibold mb-2">Sesi Berakhir</h1>
        <p class="text-gray-600 mb-6">Sesi Anda telah berakhir. Silakan login ulang untuk melanjutkan.</p>
        <div class="flex items-center justify-center gap-2">
            <a href="{{ route('login') }}" class="btn btn-primary">Login Ulang</a>
            <a href="{{ url()->previous() }}" class="btn">Kembali</a>
        </div>
    </div>
    <x-flash-toast />
  </div>
@endsection

