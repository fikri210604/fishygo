@extends('layouts.app')

@section('title', 'Riwayat Pesanan - FishyGo')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <h1 class="text-3xl md:text-4xl font-bold text-primary mb-4">Riwayat Pesanan</h1>

    <form method="GET" class="flex items-center gap-2 mb-4">
        <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari kode pesanan" class="input input-bordered w-56">
        <select name="status" class="select select-bordered">
            <option value="">Semua Status</option>
            <option value="menunggu_pembayaran" {{ ($status ?? '')==='menunggu_pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
            <option value="dikirim" {{ ($status ?? '')==='dikirim' ? 'selected' : '' }}>Dikirim</option>
            <option value="selesai" {{ ($status ?? '')==='selesai' ? 'selected' : '' }}>Selesai</option>
            <option value="dibatalkan" {{ ($status ?? '')==='dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
        </select>
        <button class="btn">Filter</button>
    </form>

    <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Tanggal</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $row)
                    <tr>
                        <td class="font-semibold">{{ $row->kode_pesanan }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $row->status)) }}</td>
                        <td>Rp {{ number_format($row->total, 0, ',', '.') }}</td>
                        <td class="text-sm text-gray-600">{{ $row->created_at?->format('d M Y H:i') }}</td>
                        <td class="text-center">
                            <a href="{{ route('pesanan.show', $row->pesanan_id) }}" class="btn btn-xs">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-500">Belum ada pesanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $items->onEachSide(1)->links() }}
    </div>
</div>
@endsection
