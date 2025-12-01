@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-semibold">Pesanan</h1>
            <div class="text-xs text-gray-500 mt-1">Total: {{ $counts['all'] }} • Menunggu: {{ $counts['waiting'] }} • Dibatalkan: {{ $counts['cancelled'] }}</div>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari kode/akun" class="input input-bordered input-sm w-56" />
            <select name="status" class="select select-bordered select-sm select-no-truncate">
                <option value="">Semua Status</option>
                <option value="menunggu_pembayaran" {{ $status==='menunggu_pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                <option value="dikirim" {{ $status==='dikirim' ? 'selected' : '' }}>Dikirim</option>
                <option value="selesai" {{ $status==='selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="dibatalkan" {{ $status==='dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
            <button class="btn btn-sm">Filter</button>
        </form>
    </div>

    <div class="overflow-x-auto bg-white rounded border">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Pelanggan</th>
                    <th>Status</th>
                    <th>Item</th>
                    <th>Total</th>
                    <th>Metode Pembayaran</th>
                    <th>Dibuat</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $row)
                    <tr>
                        <td class="font-medium">{{ $row->kode_pesanan }}</td>
                        <td>
                            <div class="text-sm">{{ $row->user?->nama ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $row->user?->email }}</div>
                        </td>
                        <td class="text-xs">{{ ucfirst(str_replace('_',' ', $row->status)) }}</td>
                        <td>{{ $row->items_count }}</td>
                        <td>Rp {{ number_format($row->total, 0, ',', '.') }}</td>
                        <td>{{strtoupper($row->metode_pembayaran)}}</td>
                        <td class="text-xs text-gray-600">{{ $row->created_at?->format('d M Y H:i') }}</td>
                        <td class="text-center">
                            <a class="btn btn-xs" href="{{ route('admin.pesanan.show', $row) }}">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-gray-500">Belum ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $items->onEachSide(1)->links() }}
    </div>
@endsection
