@extends('layouts.admin')

@section('content')
    <div class="mb-5">
        <h1 class="text-xl font-semibold">Dashboard Kurir</h1>
        <div class="text-xs text-gray-500 mt-1">Daftar tugas pengiriman Anda</div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="card card-sm shadow-sm bg-base-100 border">
            <div class="card-body flex flex-row items-center gap-4">
                <span class="material-symbols-outlined text-primary text-3xl">assignment_ind</span>
                <div>
                    <p class="text-sm text-gray-500">Tugas Saya</p>
                    <p class="text-xl font-bold">{{ number_format($totalAssigned ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="card card-sm shadow-sm bg-base-100 border">
            <div class="card-body flex flex-row items-center gap-4">
                <span class="material-symbols-outlined text-primary text-3xl">local_shipping</span>
                <div>
                    <p class="text-sm text-gray-500">Diantar</p>
                    <p class="text-xl font-bold">{{ number_format($countDiantar ?? 0) }}</p>
                </div>
            </div>
        </div>
        <div class="card card-sm shadow-sm bg-base-100 border">
            <div class="card-body">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-3xl">undo</span>
                        <div>
                            <p class="text-sm text-gray-500">Dikembalikan</p>
                            <p class="text-xl font-bold">{{ number_format($returnCountSelected ?? 0) }}</p>
                        </div>
                    </div>
                    <form method="GET">
                        <input type="hidden" name="status" value="{{ $status }}" />
                        <input type="hidden" name="done_range" value="{{ $doneRange ?? 'today' }}" />
                        <select name="return_range" class="select select-bordered select-xs" onchange="this.form.submit()">
                            <option value="today" {{ ($returnRange ?? 'today') === 'today' ? 'selected' : '' }}>Hari Ini
                            </option>
                            <option value="7d" {{ ($returnRange ?? '') === '7d' ? 'selected' : '' }}>7 Hari</option>
                            <option value="30d" {{ ($returnRange ?? '') === '30d' ? 'selected' : '' }}>30 Hari</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>
        <div class="card card-sm shadow-sm bg-base-100 border">
            <div class="card-body">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-3xl">done_all</span>
                        <div>
                            <p class="text-sm text-gray-500">Selesai</p>
                            <p class="text-xl font-bold">{{ number_format($doneCountSelected ?? 0) }}</p>
                        </div>
                    </div>
                    <form method="GET">
                        <input type="hidden" name="status" value="{{ $status }}" />
                        <input type="hidden" name="return_range" value="{{ $returnRange ?? 'today' }}" />
                        <select name="done_range" class="select select-bordered select-xs" onchange="this.form.submit()">
                            <option value="today" {{ ($doneRange ?? 'today') === 'today' ? 'selected' : '' }}>Hari Ini
                            </option>
                            <option value="7d" {{ ($doneRange ?? '') === '7d' ? 'selected' : '' }}>7 Hari</option>
                            <option value="30d" {{ ($doneRange ?? '') === '30d' ? 'selected' : '' }}>30 Hari</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if (session('error'))
        <div class="alert alert-error mb-4">{{ session('error') }}</div>
    @endif
    @if (session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
        <div class="p-4 border-b flex items-center gap-3">
            <form method="GET" class="flex items-center gap-2">
                <label class="text-sm">Status</label>
                <select name="status" class="select select-bordered select-sm">
                    <option value="" {{ empty($status) ? 'selected' : '' }}>Semua</option>
                    <option value="siap" {{ ($status ?? '') === 'siap' ? 'selected' : '' }}>Siap</option>
                    <option value="diambil" {{ ($status ?? '') === 'diambil' ? 'selected' : '' }}>Diambil</option>
                    <option value="diantar" {{ ($status ?? '') === 'diantar' ? 'selected' : '' }}>Diantar</option>
                    <option value="diterima" {{ ($status ?? '') === 'diterima' ? 'selected' : '' }}>Diterima</option>
                    <option value="dikembalikan" {{ ($status ?? '') === 'dikembalikan' ? 'selected' : '' }}>Dikembalikan
                    </option>
                </select>
                <button class="btn btn-sm">Filter</button>
                <a href="{{ route('kurir.dashboard') }}" class="btn btn-sm btn-outline">Reset</a>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-xs font-semibold text-gray-700">Kode</th>
                        <th class="text-xs font-semibold text-gray-700">Nama Penerima</th>
                        <th class="text-xs font-semibold text-gray-700">Alamat</th>
                        <th class="text-xs font-semibold text-gray-700">Telepon</th>
                        <th class="text-xs font-semibold text-gray-700">Status</th>
                        <th class="text-xs font-semibold text-gray-700 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($items as $row)
                        <tr>
                            <td class="font-medium">
                                <a href="{{ route('kurir.pengiriman.show', $row->pengiriman_id) }}" class="link link-primary">
                                    {{ $row->pesanan->kode_pesanan }}
                                </a>
                            </td>
                            <td>{{ $row->pesanan->alamat->penerima }}</td>
                            <td class="text-sm">{{ $row->pesanan->alamat->alamat_lengkap }}</td>
                            <td>{{ $row->pesanan->user->nomor_telepon ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $row->status_class }}">{{ $row->status_label }}</span>
                            </td>
                            <td class="text-center">
                                <div class="flex gap-2 justify-center">
                                    @foreach ($row->action_buttons as $action => $label)
                                        <form method="POST" action="{{ route('kurir.pengiriman.status', $row->pengiriman_id) }}">
                                            @csrf
                                            <input type="hidden" name="action" value="{{ $action }}">
                                            <button class="{{ $row->button_class[$action] }}">{{ $label }}</button>
                                        </form>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-gray-500">
                                Belum ada tugas pengiriman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        <div class="p-3 border-t">
            <div class="flex justify-center">{{ $items->links() }}</div>
        </div>
    </div>
@endsection
