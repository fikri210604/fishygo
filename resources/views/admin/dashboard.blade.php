@extends('layouts.admin')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

    <div class="card card-sm shadow-sm bg-base-100 border">
        <div class="card-body flex flex-row items-center gap-4">
            <span class="material-symbols-outlined text-primary text-3xl">group</span>
            <div>
                <p class="text-sm text-gray-500">Total Pengguna</p>
                <p class="text-xl font-bold">{{ number_format($totalUser) }}</p>
            </div>
        </div>
    </div>

    <div class="card card-sm shadow-sm bg-base-100 border">
        <div class="card-body flex flex-row items-center gap-4">
            <span class="material-symbols-outlined text-primary text-3xl">inventory_2</span>
            <div>
                <p class="text-sm text-gray-500">Total Pesanan</p>
                <p class="text-xl font-bold">{{ number_format($totalOrder) }}</p>
            </div>
        </div>
    </div>

    <div class="card card-sm shadow-sm bg-base-100 border">
        <div class="card-body flex flex-row items-center gap-4">
            <span class="material-symbols-outlined text-primary text-3xl">savings</span>
            <div>
                <p class="text-sm text-gray-500">Total Penjualan</p>
                <p class="text-xl font-bold">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="card card-sm shadow-sm bg-base-100 border">
        <div class="card-body flex flex-row items-center gap-4">
            <span class="material-symbols-outlined text-primary text-3xl">cancel</span>
            <div>
                <p class="text-sm text-gray-500">Pembatalan</p>
                <p class="text-xl font-bold">{{ number_format($totalPembatalan) }}</p>
            </div>
        </div>
    </div>

</div>

<div class="card shadow-sm bg-base-100 border">
    <div class="card-body">
        <h2 class="card-title">Grafik Jumlah Pesanan (30 Hari Terakhir)</h2>
        <canvas id="orderChart" width="400" height="200"></canvas>
    </div>
</div>

@endsection

@push('scripts')
<script>
    window.orderData = @json($orderData);
</script>
@vite('resources/js/dashboard.js')
